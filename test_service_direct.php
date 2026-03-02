<?php

require 'bootstrap/app.php';

use App\Services\BodyCompositionService;

$weight = 99.1;
$age = 31;
$gender = 'masculino';

// Teste 1: Valores corretos
$skinfolds_correto = [
    'tricipital' => 10,
    'suprailiaca' => 15,
    'coxa_fold' => 0,
    'bicipital' => 5,
    'toracica' => 16,
    'abdominal_fold' => 27,
    'axilar_media' => 10,
    'subescapular' => 20,
    'panturrilha_fold' => 14,
];

echo "======== TESTE DO POLLOCK 3 ========\n";
echo "Entrada:\n";
echo "  tricipital: " . $skinfolds_correto['tricipital'] . "\n";
echo "  suprailiaca: " . $skinfolds_correto['suprailiaca'] . "\n";
echo "  coxa_fold: " . $skinfolds_correto['coxa_fold'] . "\n";
echo "  Esperado: soma = 25\n\n";

$resultado = BodyCompositionService::calculatePollock3($weight, $age, $gender, $skinfolds_correto);

if ($resultado) {
    echo "RESULTADO:\n";
    echo "  Density: " . $resultado['density'] . "\n";
    echo "  Fat %: " . $resultado['fat_pct'] . "%\n";
    echo "  Fat Mass: " . $resultado['fat_mass'] . "kg\n";
    echo "  Lean Mass: " . $resultado['lean_mass'] . "kg\n\n";
    
    echo "VALIDAÇÃO:\n";
    if ($resultado['density'] == 1.0817) {
        echo "  ✓ Correto! (Density = 1.0817)\n";
    } else {
        echo "  ✗ ERRADO! Esperado 1.0817, recebido " . $resultado['density'] . "\n";
    }
} else {
    echo "ERRO: Resultado NULL\n";
}

echo "\n\n======== TESTE DO POLLOCK 7 ========\n";
echo "Entrada (7 dobras):\n";
echo "  tricipital: " . $skinfolds_correto['tricipital'] . "\n";
echo "  subescapular: " . $skinfolds_correto['subescapular'] . "\n";
echo "  toracica: " . $skinfolds_correto['toracica'] . "\n";
echo "  axilar_media: " . $skinfolds_correto['axilar_media'] . "\n";
echo "  abdominal_fold: " . $skinfolds_correto['abdominal_fold'] . "\n";
echo "  suprailiaca: " . $skinfolds_correto['suprailiaca'] . "\n";
echo "  coxa_fold: " . $skinfolds_correto['coxa_fold'] . "\n";
echo "  Esperado: soma = 98\n\n";

$resultado7 = BodyCompositionService::calculatePollock7($weight, $age, $gender, $skinfolds_correto);

if ($resultado7) {
    echo "RESULTADO:\n";
    echo "  Density: " . $resultado7['density'] . "\n";
    echo "  Fat %: " . $resultado7['fat_pct'] . "%\n";
    echo "  Fat Mass: " . $resultado7['fat_mass'] . "kg\n";
    echo "  Lean Mass: " . $resultado7['lean_mass'] . "kg\n\n";
    
    echo "VALIDAÇÃO:\n";
    if ($resultado7['density'] == 1.0657) {
        echo "  ✓ Correto! (Density = 1.0657)\n";
    } else {
        echo "  ✗ ERRADO! Esperado 1.0657, recebido " . $resultado7['density'] . "\n";
    }
} else {
    echo "ERRO: Resultado NULL\n";
}
