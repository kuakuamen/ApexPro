<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestGeminiApi extends Command
{
    protected $signature = 'test:gemini';
    protected $description = 'Testa a conexão com a API Gemini';

    public function handle()
    {
        $this->info('🔍 Testando API Gemini...');
        $this->line('');

        // 1. Verificar chave de API
        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            $this->error('❌ GEMINI_API_KEY não está configurada!');
            $this->line('Adicione a chave no arquivo .env:');
            $this->line('GEMINI_API_KEY=sua_chave_aqui');
            return 1;
        }

        $this->info('✅ Chave da API encontrada');
        $this->line('Chave: ' . substr($apiKey, 0, 10) . '...' . substr($apiKey, -5));
        $this->line('');

        // 2. Testar conexão
        $this->info('🌐 Testando conexão com Gemini...');
        
        try {
            $client = \Gemini::client($apiKey);
            
            $prompt = "Responda apenas com a palavra 'OK' se você receber esta mensagem.";
            $response = $client->generativeModel('gemini-2.5-flash')->generateContent($prompt);
            $result = $response->text();
            
            if (strtoupper(trim($result)) === 'OK' || str_contains($result, 'OK')) {
                $this->info('✅ Conexão com Gemini bem-sucedida!');
                $this->line('Resposta: ' . trim($result));
                $this->line('');
                
                // 3. Testar com JSON
                $this->info('📝 Testando resposta JSON...');
                
                $jsonPrompt = 'Retorne um JSON válido com {"status": "ok", "message": "Funcionando"}. Apenas JSON, sem explicações.';
                $jsonResponse = $client->generativeModel('gemini-2.5-flash')->generateContent($jsonPrompt);
                $jsonText = $jsonResponse->text();
                $jsonData = json_decode($jsonText, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->info('✅ Teste JSON bem-sucedido!');
                    $this->line('Resposta: ' . json_encode($jsonData, JSON_PRETTY_PRINT));
                } else {
                    $this->error('❌ Erro ao processar JSON da IA');
                    $this->line('Resposta bruta: ' . $jsonText);
                }
                
                return 0;
            } else {
                $this->error('❌ Resposta inesperada da IA');
                $this->line('Resposta: ' . $result);
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Erro na conexão com Gemini:');
            $this->line($e->getMessage());
            
            if (str_contains($e->getMessage(), 'Quota exceeded') || str_contains($e->getMessage(), '429')) {
                $this->warn('⚠️  Limite de requisições atingido. Aguarde alguns minutos.');
            }
            
            return 1;
        }
    }
}
