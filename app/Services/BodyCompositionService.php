<?php

namespace App\Services;

use Carbon\Carbon;

class BodyCompositionService
{
    private static function toPositiveFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        $number = (float) $value;

        if ($number <= 0) {
            return null;
        }

        return $number;
    }

    private static function buildResult(float $density, float $weight): ?array
    {
        if (!is_finite($density) || $density <= 0) {
            return null;
        }

        $fatPct = ((4.95 / $density) - 4.5) * 100;

        if (!is_finite($fatPct)) {
            return null;
        }

        $fatMass = ($fatPct / 100) * $weight;
        $leanMass = $weight - $fatMass;

        if (!is_finite($fatMass) || !is_finite($leanMass)) {
            return null;
        }

        return [
            'density' => round($density, 4),
            'fat_pct' => round($fatPct, 2),
            'fat_mass' => round($fatMass, 2),
            'lean_mass' => round($leanMass, 2),
        ];
    }

    private static function isMale($gender): bool
    {
        if ($gender === null) {
            return false;
        }

        $normalized = mb_strtolower(trim((string) $gender), 'UTF-8');
        $normalized = str_replace(['á', 'à', 'â', 'ã'], 'a', $normalized);
        $normalized = str_replace(['é', 'ê'], 'e', $normalized);
        $normalized = str_replace(['í'], 'i', $normalized);
        $normalized = str_replace(['ó', 'ô', 'õ'], 'o', $normalized);
        $normalized = str_replace(['ú'], 'u', $normalized);
        $normalized = str_replace(['ç'], 'c', $normalized);

        return in_array($normalized, ['male', 'masculino', 'homem', 'm'], true);
    }

    /**
     * Calcula composição corporal usando GUEDES (3 dobras: subescapular, suprailíaca, coxa)
     * Masculino e Feminino têm fórmulas diferentes (constante diferente)
     */
    public static function calculateGuedes($weight, $age, $gender, $skinfolds)
    {
        // Dobras: subescapular, suprailíaca, coxa
        $subescapular = self::toPositiveFloat($skinfolds['subescapular'] ?? null);
        $suprailiaca = self::toPositiveFloat($skinfolds['suprailiaca'] ?? null);
        $coxa = self::toPositiveFloat($skinfolds['coxa_fold'] ?? null);

        // Se algum valor é null, retorna null (não calcula)
        if ($subescapular === null || $suprailiaca === null || $coxa === null) {
            return null;
        }

        $sum = $subescapular + $suprailiaca + $coxa;

        // Fórmula de densidade de Guedes
        // Masculino: Densidade = 1.17136 - 0.06706 * LOG10(sum)
        // Feminino:  Densidade = 1.16055 - 0.06706 * LOG10(sum)
        if (self::isMale($gender)) {
            $density = 1.17136 - (0.06706 * log10($sum));
        } else {
            $density = 1.16055 - (0.06706 * log10($sum));
        }

        return self::buildResult($density, (float) $weight);
    }

    /**
     * Calcula composição corporal usando POLLOCK 3.
     * Masculino: torácica, abdominal, coxa.
     * Feminino: tricipital, suprailíaca, coxa.
     */
    public static function calculatePollock3($weight, $age, $gender, $skinfolds)
    {
        // Dobras necessárias para as variações do protocolo
        $toracica = self::toPositiveFloat($skinfolds['toracica'] ?? null);
        $abdominal = self::toPositiveFloat($skinfolds['abdominal_fold'] ?? null);
        $tricipital = self::toPositiveFloat($skinfolds['tricipital'] ?? null);
        $suprailiaca = self::toPositiveFloat($skinfolds['suprailiaca'] ?? null);
        $coxa = self::toPositiveFloat($skinfolds['coxa_fold'] ?? null);

        // Fórmula Pollock 3 para HOMENS (planilha Excel)
        // Densidade = 1.10938 - 0.0008267*sum + 0.0000016*sum² - 0.0002574*idade
        if (self::isMale($gender)) {
            // Masculino: torácica + abdominal + coxa
            if ($toracica === null || $abdominal === null || $coxa === null) {
                return null;
            }

            $sum = $toracica + $abdominal + $coxa;
            $density = 1.10938 - (0.0008267 * $sum) + (0.0000016 * pow($sum, 2)) - (0.0002574 * $age);
        } else {
            // Fórmula Pollock 3 para MULHERES
            // Feminino: tricipital + suprailíaca + coxa
            if ($tricipital === null || $suprailiaca === null || $coxa === null) {
                return null;
            }

            $sum = $tricipital + $suprailiaca + $coxa;
            $density = 1.0994921 - (0.0009929 * $sum) + (0.0000023 * pow($sum, 2)) - (0.0001392 * $age);
        }

        return self::buildResult($density, (float) $weight);
    }

    /**
     * Calcula composição corporal usando POLLOCK 7 (7 dobras)
     * Dobras: tricipital, subescapular, torácica, axilar média, abdominal, suprailíaca, coxa
     * NÃO inclui panturrilha
     */
    public static function calculatePollock7($weight, $age, $gender, $skinfolds)
    {
        // Dobras: tricipital, subescapular, torácica, axilar_media, abdominal_fold, suprailiaca, coxa_fold
        // NÃO inclui: bicipital, panturrilha_fold
        $tricipital = self::toPositiveFloat($skinfolds['tricipital'] ?? null);
        $subescapular = self::toPositiveFloat($skinfolds['subescapular'] ?? null);
        $toracica = self::toPositiveFloat($skinfolds['toracica'] ?? null);
        $axilar_media = self::toPositiveFloat($skinfolds['axilar_media'] ?? null);
        $abdominal = self::toPositiveFloat($skinfolds['abdominal_fold'] ?? null);
        $suprailiaca = self::toPositiveFloat($skinfolds['suprailiaca'] ?? null);
        $coxa = self::toPositiveFloat($skinfolds['coxa_fold'] ?? null);

        // Verifica se todos os valores obrigatórios estão disponíveis
        if ($tricipital === null || $subescapular === null || $abdominal === null || $suprailiaca === null || $coxa === null) {
            return null;
        }

        // IMPORTANTE: soma apenas as 7 dobras especificadas, NÃO inclui panturrilha
        $sum = $tricipital + $subescapular + $toracica + $axilar_media + $abdominal + $suprailiaca + $coxa;

        // Fórmula Pollock 7 para HOMENS (planilha Excel)
        // Densidade = 1.112 - 0.00043499*sum + 0.00000055*sum² - 0.0002882*idade  
        if (self::isMale($gender)) {
            $density = 1.112 - (0.00043499 * $sum) + (0.00000055 * pow($sum, 2)) - (0.0002882 * $age);
        } else {
            // Fórmula Pollock 7 para MULHERES
            $density = 1.097 - (0.00046971 * $sum) + (0.00000056 * pow($sum, 2)) - (0.00012828 * $age);
        }

        return self::buildResult($density, (float) $weight);
    }

    /**
     * Calcula idade a partir da data de nascimento
     */
    public static function calculateAge($birthDate)
    {
        if (!$birthDate) {
            return null;
        }

        return Carbon::parse($birthDate)->age;
    }

    /**
     * Classifica o percentual de gordura para homens
     */
    public static function classifyFatPercentageMale($fatPct, $age)
    {
        $classifications = [
            '18-25' => [
                'competitive' => [4, 6],
                'excellent' => [6, 13],
                'good' => [14, 17],
                'average' => [18, 24],
                'below_average' => [25, 31],
                'poor' => [32, 100],
            ],
            '26-35' => [
                'competitive' => [6, 15],
                'excellent' => [16, 20],
                'good' => [21, 24],
                'average' => [25, 31],
                'below_average' => [32, 36],
                'poor' => [37, 100],
            ],
            '36-45' => [
                'competitive' => [8, 17],
                'excellent' => [18, 21],
                'good' => [22, 27],
                'average' => [28, 32],
                'below_average' => [33, 37],
                'poor' => [38, 100],
            ],
        ];

        $ageRange = self::getAgeRange($age);
        if (!isset($classifications[$ageRange])) {
            return 'N/A';
        }

        foreach ($classifications[$ageRange] as $classification => $range) {
            if ($fatPct >= $range[0] && $fatPct <= $range[1]) {
                return ucfirst(str_replace('_', ' ', $classification));
            }
        }

        return 'N/A';
    }

    /**
     * Retorna a faixa etária
     */
    private static function getAgeRange($age)
    {
        if ($age < 18) {
            return 'under18';
        } elseif ($age >= 18 && $age <= 25) {
            return '18-25';
        } elseif ($age >= 26 && $age <= 35) {
            return '26-35';
        } elseif ($age >= 36 && $age <= 45) {
            return '36-45';
        } elseif ($age >= 46 && $age <= 55) {
            return '46-55';
        } else {
            return '56-65';
        }
    }
}
