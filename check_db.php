<?php
require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use App\Models\BodyMeasurement;

$latest = BodyMeasurement::latest()->first();

if ($latest) {
    echo "=== ÚLTIMO REGISTRO SALVO ===\n";
    echo "ID: " . $latest->id . "\n";
    echo "Subescapular: " . $latest->subescapular . "\n";
    echo "Tricipital: " . $latest->tricipital . "\n";
    echo "Bicipital: " . $latest->bicipital . "\n";
    echo "Torácica: " . $latest->toracica . "\n";
    echo "Abdominal: " . $latest->abdominal_fold . "\n";
    echo "Axilar Média: " . $latest->axilar_media . "\n";
    echo "Suprailiaca: " . $latest->suprailiaca . "\n";
    echo "Coxa: " . $latest->coxa_fold . "\n";
    echo "Panturrilha: " . $latest->panturrilha_fold . "\n";
    echo "Soma das dobras: " . $latest->sum_skinfolds . "\n";
    echo "\nResultados calculados:\n";
    echo "GUEDES Density: " . $latest->guedes_density . " | Fat%: " . $latest->guedes_fat_pct . "%\n";
    echo "POLLOCK3 Density: " . $latest->pollock3_density . " | Fat%: " . $latest->pollock3_fat_pct . "%\n";
    echo "POLLOCK7 Density: " . $latest->pollock7_density . " | Fat%: " . $latest->pollock7_fat_pct . "%\n";
} else {
    echo "Nenhum registro encontrado\n";
}
