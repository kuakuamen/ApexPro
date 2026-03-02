<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Services\BodyCompositionService;

$skinfolds = [
    'subescapular' => 15,
    'tricipital' => 14,
    'bicipital' => 5,
    'toracica' => 9,
    'abdominal_fold' => 17,
    'axilar_media' => 10,
    'suprailiaca' => 8,
    'coxa_fold' => 14,
    'panturrilha_fold' => 13,
];

echo "Testando GUEDES Feminino (25 anos, 49.9 kg):\n";
$result_fem = BodyCompositionService::calculateGuedes(49.9, 25, 'Feminino', $skinfolds);

echo json_encode($result_fem, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;

echo "\nEsperado da planilha:\n";
echo json_encode([
    'density' => 1.06,
    'fat_pct' => 18.9, 
    'fat_mass' => 9.4,
    'lean_mass' => 40.5
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
