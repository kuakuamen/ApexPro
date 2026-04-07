<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Gemini\Client;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Gemini\Resources\GenerativeModel;
use Illuminate\Support\Facades\Storage;

class AiAnalysisService
{
    protected $client;

    public function __construct()
    {
        // Inicializa o cliente Gemini com a chave via config (compatível com config:cache)
        $apiKey = config('services.gemini.api_key');
        
        if (!$apiKey) {
            Log::error('GEMINI_API_KEY não está configurada no arquivo .env');
            $this->client = null;
        } else {
            Log::info('Cliente Gemini inicializado com sucesso');
            $this->client = \Gemini::client($apiKey);
        }
    }

    /**
     * Calcula composição corporal com as fórmulas corretas
     * Use este método para substituir qualquer cálculo da IA incorreto
     * 
     * @param float $weight Peso em kg
     * @param int $age Idade em anos
     * @param string $gender Gênero (masculino/feminino)
     * @param array $skinfolds Array de dobras cutâneas
     * @return array com Guedes, Pollock3, Pollock7
     */
    public function calculateCorrectBodyComposition(float $weight, int $age, string $gender, array $skinfolds): array
    {
        return [
            'guedes' => BodyCompositionService::calculateGuedes($weight, $age, $skinfolds),
            'pollock3' => BodyCompositionService::calculatePollock3($weight, $age, $gender, $skinfolds),
            'pollock7' => BodyCompositionService::calculatePollock7($weight, $age, $gender, $skinfolds),
        ];
    }

    /**
     * Envia as imagens para a IA e retorna a análise estruturada + TREINO COMPLETO.
     *
     * @param array $imagePaths Caminhos das imagens (front, side, back)
     * @param array $studentData Dados do aluno (peso, altura, objetivo)
     * @return array
     */
    public function analyzeImages(array $imagePaths, array $studentData): array
    {
        Log::info('Iniciando análise com Gemini AI (Postura + Treino):', $imagePaths);

        if (!$this->client) {
            Log::error('GEMINI_API_KEY não está configurada!');
            throw new \RuntimeException('Chave API do Gemini não está configurada.');
        }

        try {
            $parts = [];
            
            // PROMPT ENGENHADO PARA GERAR TREINO ESTRUTURADO
            $anamnese = $studentData['anamnese'] ?? [];
            $anamneseStr = "Histórico de Lesões: " . ($anamnese['injuries'] ?? 'Nenhuma') . ". " .
                           "Medicamentos: " . ($anamnese['medications'] ?? 'Nenhum') . ". " .
                           "Cirurgias: " . ($anamnese['surgeries'] ?? 'Nenhuma') . ". " .
                           "Dores Atuais: " . ($anamnese['pain_points'] ?? 'Nenhuma') . ". " .
                           "Hábitos: " . ($anamnese['habits'] ?? 'Não informado') . ".";

            $prompt = "Você é um Personal Trainer de elite e especialista em biomecânica. " .
                "Analise estas fotos do aluno (frontal, lateral, costas e extras, quando enviados) e seus dados: " .
                "Objetivo: " . ($studentData['goal'] ?? 'Geral') . ". " .
                "Experiência: " . ($studentData['experience'] ?? 'Iniciante') . ". " .
                "PERFIL: Sexo: " . ($studentData['gender'] ?? 'Não inf.') . ". " .
                "ANAMNESE (MUITO IMPORTANTE - ÚLTIMA AVALIAÇÃO): " . $anamneseStr . " " .
                "OBSERVAÇÕES DO PERSONAL: " . ($studentData['notes'] ?? '') . ". " .
                
                "TAREFA 1: Identifique desvios posturais nas fotos (lordose, cifose, escoliose, ombros, joelhos). " .
                "TAREFA 2: Crie um ROTINA DE TREINO SEMANAL COMPLETA (Microciclo). " .
                "REGRAS DO TREINO: " .
                "1. Respeite as lesões e dores informadas na anamnese (ex: se tiver lesão no joelho, adapte). " .
                "2. Inclua exercícios corretivos específicos para os desvios encontrados nas fotos. " .
                
                "Retorne APENAS um JSON válido com esta estrutura exata: " .
                "{
                    'posture_analysis': {
                        'lordosis': 'string',
                        'scoliosis': 'string',
                        'shoulders': 'string',
                        'head_position': 'string',
                        'knees': 'string',
                        'feet': 'string'
                    },
                    'suggested_focus': {
                        'strengthen': ['musculo1', 'musculo2'],
                        'stretch': ['musculo1', 'musculo2']
                    },
                    'workout_recommendation': {
                        'type': 'string (ex: ABC, Fullbody)',
                        'priority': 'string',
                        'days': [
                            {
                                'name': 'Treino A - Peito e Tríceps',
                                'exercises': [
                                    { 'name': 'Supino Reto', 'sets': 4, 'reps': '8-10', 'notes': 'Focar na descida controlada' },
                                    { 'name': 'Crucifixo Inclinado', 'sets': 3, 'reps': '12', 'notes': '' }
                                ]
                            },
                            {
                                'name': 'Treino B - Costas e Bíceps',
                                'exercises': [
                                    { 'name': 'Puxada Frontal', 'sets': 4, 'reps': '10-12', 'notes': 'Segurar 1s embaixo' }
                                ]
                            }
                        ]
                    },
                    'risk_factors': {
                        'low': ['risco1'],
                        'medium': ['risco1'],
                        'high': ['risco1']
                    }
                }";

            $promptSize = strlen($prompt);
            Log::info("Gemini diagnóstico — prompt_bytes: {$promptSize}, imagens: " . count($imagePaths) . ", caminhos: " . implode(', ', $imagePaths));

            $parts[] = $prompt;

            $totalImageBytes = 0;
            foreach ($imagePaths as $path) {
                if (Storage::disk('private')->exists($path)) {
                    $imageData = Storage::disk('private')->get($path);
                    $mimeType = Storage::disk('private')->mimeType($path);
                    $totalImageBytes += strlen($imageData);
                    Log::info("Gemini imagem — path: {$path}, mime: {$mimeType}, bytes: " . strlen($imageData));
                    
                    // Tenta converter string para Enum MimeType
                    $enumMimeType = MimeType::tryFrom($mimeType);
                    
                    if (!$enumMimeType) {
                        Log::warning("MimeType não suportado pelo Gemini: $mimeType");
                        continue; 
                    }

                    $parts[] = new Blob(mimeType: $enumMimeType, data: base64_encode($imageData));
                }
            }

            if (count($parts) <= 1) {
                Log::warning('Nenhuma imagem válida foi processada para o Gemini.');
                throw new \RuntimeException('Nenhuma imagem válida foi encontrada para análise.');
            }

            Log::info("Gemini diagnóstico — total_image_bytes: {$totalImageBytes}, total_parts: " . count($parts));
            Log::info('Enviando requisição para Gemini 2.5-flash com ' . (count($parts) - 1) . ' imagem(ns)');

            $maxAttempts = 3;
            $lastException = null;

            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                try {
                    $response   = $this->client->generativeModel('gemini-2.5-flash')->generateContent($parts);
                    $textResult = $response->text();

                    Log::info("Resposta Gemini recebida (tentativa {$attempt}, primeiros 500 chars): " . substr($textResult, 0, 500));

                    $textResult = preg_replace('/^```json\s*|\s*```$/', '', $textResult);
                    $jsonResult = json_decode($textResult, true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        Log::info('Análise Gemini processada com sucesso');
                        return $jsonResult;
                    }

                    Log::error('Erro JSON Gemini: ' . $textResult);
                    throw new \RuntimeException('A IA retornou uma resposta inválida. Tente novamente.');

                } catch (\RuntimeException $e) {
                    throw $e; // Erros de JSON não fazem retry
                } catch (\Exception $e) {
                    $lastException = $e;
                    $isRetryable   = str_contains($e->getMessage(), 'high demand')
                                  || str_contains($e->getMessage(), 'temporarily')
                                  || str_contains($e->getMessage(), '503');

                    if ($isRetryable && $attempt < $maxAttempts) {
                        Log::warning("Gemini alta demanda — tentativa {$attempt}/{$maxAttempts}. Aguardando 4s...");
                        sleep(4);
                        continue;
                    }

                    if (str_contains($e->getMessage(), 'Quota exceeded') || str_contains($e->getMessage(), '429')) {
                        throw new \RuntimeException('Limite da API Gemini atingido. Aguarde alguns instantes e tente novamente.');
                    }
                    if ($isRetryable) {
                        throw new \RuntimeException('A IA está com alta demanda no momento. Aguarde alguns instantes e tente novamente.');
                    }
                    throw new \RuntimeException('Erro ao processar análise com IA: ' . $e->getMessage());
                }
            }

            throw new \RuntimeException('A IA está com alta demanda no momento. Aguarde alguns instantes e tente novamente.');

        } catch (\Exception $e) {
            Log::error('Erro API Gemini: ' . $e->getMessage());
            throw $e instanceof \RuntimeException ? $e : new \RuntimeException('Erro ao processar análise com IA: ' . $e->getMessage());
        }
    }

    /**
     * Refina a análise anterior com feedback do personal.
     */
    public function refineAnalysis(array $previousAnalysis, string $feedback, array $imagePaths, array $studentData): array
    {
        Log::info('Refinando análise com Gemini AI:', ['feedback' => $feedback]);

        if (!$this->client) {
            return $this->getMockData();
        }

        try {
            $parts = [];
            
            // PROMPT DE REFINAMENTO
            $prompt = "Você é um Personal Trainer de elite. Anteriormente, você analisou este aluno e gerou um treino. " .
                "Agora, o personal trainer responsável forneceu um FEEDBACK para ajustar a análise. " .
                
                "DADOS ORIGINAIS: Objetivo: " . ($studentData['goal'] ?? '') . ". " .
                "ANÁLISE ANTERIOR (JSON): " . json_encode($previousAnalysis) . ". " .
                
                "FEEDBACK DO PERSONAL (CRÍTICO - SIGA ISSO): " . $feedback . ". " .
                
                "TAREFA: " .
                "1. Reavalie a postura e o treino com base no feedback. Se o personal disse que não tem lordose, remova essa informação. " .
                "2. Regenere o treino completo (Microciclo Semanal) aplicando as correções pedidas. " .
                "3. Mantenha a estrutura JSON exata da resposta anterior. " .
                
                "Retorne APENAS o JSON válido atualizado.";

            $parts[] = $prompt;

            // Reenvia imagens para contexto visual se necessário
            foreach ($imagePaths as $path) {
                if ($path && Storage::disk('private')->exists($path)) {
                    $imageData = Storage::disk('private')->get($path);
                    $mimeType = Storage::disk('private')->mimeType($path);
                    $enumMimeType = MimeType::tryFrom($mimeType);
                    
                    if ($enumMimeType) {
                        $parts[] = new Blob(mimeType: $enumMimeType, data: base64_encode($imageData));
                    }
                }
            }

            // Usa o mesmo modelo estável
            Log::info('Enviando requisição de refinamento para Gemini 1.5-flash');

            $response = $this->client->generativeModel('gemini-2.5-flash')->generateContent($parts);
            $textResult = $response->text();
            
            Log::info('Resposta de refinamento Gemini recebida (primeiros 500 chars): ' . substr($textResult, 0, 500));
            
            $textResult = preg_replace('/^```json\s*|\s*```$/', '', $textResult);
            
            $jsonResult = json_decode($textResult, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                Log::info('Análise refinada com sucesso');
                return $jsonResult;
            } else {
                Log::error('Erro JSON Gemini Refine: ' . substr($textResult, 0, 1000));
                throw new \RuntimeException('A IA retornou uma resposta inválida ao refinar. Tente novamente.');
            }

        } catch (\Exception $e) {
            Log::error('Erro API Gemini Refine: ' . $e->getMessage());
            
            if (str_contains($e->getMessage(), 'Quota exceeded') || str_contains($e->getMessage(), '429')) {
                throw new \RuntimeException('Limite da API Gemini atingido. Aguarde alguns instantes e tente novamente.');
            }
            if (str_contains($e->getMessage(), 'high demand') || str_contains($e->getMessage(), 'temporarily')) {
                throw new \RuntimeException('A IA está com alta demanda no momento. Aguarde alguns instantes e tente novamente.');
            }
            throw new \RuntimeException('Erro ao refinar análise com IA: ' . $e->getMessage());
        }
    }

    private function getMockData(): array
    {
        return [
            'posture_analysis' => [
                'lordosis' => 'Leve hiperlordose (Simulado)',
                'scoliosis' => 'Ausente',
                'shoulders' => 'Ombro D mais baixo',
                'head_position' => 'Anteriorizada',
                'knees' => 'Valgo dinâmico',
                'feet' => 'Planos'
            ],
            'suggested_focus' => [
                'strengthen' => ['Core', 'Glúteo Médio'],
                'stretch' => ['Pitoral', 'Iliopsoas']
            ],
            'workout_recommendation' => [
                'type' => 'Adaptação Anatômica',
                'priority' => 'Estabilidade articular',
                'days' => [
                    [
                        'name' => 'Treino A - Fullbody',
                        'exercises' => [
                            ['name' => 'Agachamento Goblet', 'sets' => 3, 'reps' => '12-15', 'notes' => 'Foco na postura'],
                            ['name' => 'Remada Curvada', 'sets' => 3, 'reps' => '12', 'notes' => 'Contrair escápulas']
                        ]
                    ]
                ]
            ],
            'risk_factors' => [
                'low' => ['Impacto articular'],
                'medium' => [],
                'high' => []
            ]
        ];
    }

    /**
     * Gera treino SEM análise de imagens - apenas com dados do aluno
     */
    public function generateWorkoutWithoutImages(array $studentData): array
    {
        Log::info('Gerando treino sem imagens com Gemini AI');

        if (!$this->client) {
            Log::error('GEMINI_API_KEY não configurada!');
            throw new \RuntimeException('Chave API do Gemini não está configurada.');
        }

        try {
            $anamnese = $studentData['anamnese'] ?? [];
            $anamneseStr = "Histórico de Lesões: " . ($anamnese['injuries'] ?? 'Nenhuma') . ". " .
                           "Medicamentos: " . ($anamnese['medications'] ?? 'Nenhum') . ". " .
                           "Cirurgias: " . ($anamnese['surgeries'] ?? 'Nenhuma') . ". " .
                           "Dores Atuais: " . ($anamnese['pain_points'] ?? 'Nenhuma') . ". " .
                           "Hábitos: " . ($anamnese['habits'] ?? 'Não informado') . ".";

            $prompt = "Você é um Personal Trainer especialista em criação de planos de treino personalizados. 
            
DADOS DO ALUNO:
- Idade: {$studentData['age']} anos
- Gênero: {$studentData['gender']}
- Objetivo: {$studentData['goal']}
- Nível de Experiência: {$studentData['experience']}
- Descrição Física/Postural: {$studentData['description']}
- Contexto Médico: {$anamneseStr}
- Observações Adicionais: {$studentData['notes']}

TAREFA:
Analisando APENAS os dados fornecidos (sem imagens), gere um treino estruturado e personalizado que:
1. Respeite limitações e lesões conhecidas
2. Seja apropriado para o nível de experiência
3. Focalize no objetivo especificado
4. Inclua recomendações de postura e técnica

RETORNE UM JSON ESTRUTURADO com:
{
    \"posture_analysis\": {
        \"general_assessment\": \"Descrição geral baseada nos dados fornecidos\",
        \"recommendations\": [\"Recomendação 1\", \"Recomendação 2\"]
    },
    \"suggested_focus\": {
        \"strengthen\": [\"Grupo muscular 1\", \"Grupo muscular 2\"],
        \"stretch\": [\"Estrutura 1\", \"Estrutura 2\"]
    },
    \"workout_recommendation\": {
        \"type\": \"Tipo de treino (ex: Fullbody, Upper/Lower)\",
        \"priority\": \"Prioridade do treino\",
        \"days\": [
            {
                \"name\": \"Treino A\",
                \"exercises\": [
                    {\"name\": \"Exercício\", \"sets\": 3, \"reps\": \"10-12\", \"notes\": \"Notas\"}
                ]
            }
        ]
    },
    \"risk_factors\": {
        \"low\": [],
        \"medium\": [],
        \"high\": []
    }
}

Retorne SOMENTE o JSON, sem markdown ou explicações adicionais.";

            Log::info('Enviando requisição para Gemini 2.5-flash (sem imagens)');
            
            $response = $this->client->generativeModel('gemini-2.5-flash')->generateContent($prompt);
            $textResult = $response->text();
            
            Log::info('Resposta Gemini recebida (primeiros 500 chars): ' . substr($textResult, 0, 500));
            
            $textResult = preg_replace('/^```json\s*|\s*```$/', '', $textResult);
            
            $jsonResult = json_decode($textResult, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                Log::info('Treino gerado com sucesso pela IA');
                // Normalizar campos que devem ser arrays mas podem vir como string
                foreach (['strengthen', 'stretch'] as $field) {
                    if (isset($jsonResult['suggested_focus'][$field]) && is_string($jsonResult['suggested_focus'][$field])) {
                        $jsonResult['suggested_focus'][$field] = array_map('trim', explode(',', $jsonResult['suggested_focus'][$field]));
                    }
                }
                foreach (['low', 'medium', 'high'] as $level) {
                    if (isset($jsonResult['risk_factors'][$level]) && is_string($jsonResult['risk_factors'][$level])) {
                        $jsonResult['risk_factors'][$level] = array_map('trim', explode(',', $jsonResult['risk_factors'][$level]));
                    }
                }
                return $jsonResult;
            } else {
                Log::error('Erro JSON Gemini (no images): ' . substr($textResult, 0, 1000));
                throw new \RuntimeException('A IA retornou uma resposta inválida. Tente novamente.');
            }

        } catch (\Exception $e) {
            Log::error('Erro API Gemini (no images): ' . $e->getMessage());
            
            if (str_contains($e->getMessage(), 'Quota exceeded') || str_contains($e->getMessage(), '429')) {
                throw new \RuntimeException('Limite da API Gemini atingido. Aguarde alguns instantes e tente novamente.');
            }
            if (str_contains($e->getMessage(), 'high demand') || str_contains($e->getMessage(), 'temporarily')) {
                throw new \RuntimeException('A IA está com alta demanda no momento. Aguarde alguns instantes e tente novamente.');
            }
            throw new \RuntimeException('Erro ao processar análise com IA: ' . $e->getMessage());
        }
    }
}
