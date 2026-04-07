<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageHelper
{
    /**
     * Comprime e armazena uma imagem.
     * Redimensiona para no máximo 1920px de largura e aplica qualidade 75%.
     *
     * @param UploadedFile $file
     * @param string       $directory  Ex: 'assessments', 'profile-photos'
     * @param string       $disk       Ex: 'private', 'public'
     * @return string  Caminho relativo salvo
     */
    public static function compressAndStore(UploadedFile $file, string $directory, string $disk = 'private'): string
    {
        $filename  = Str::uuid() . '.jpg';
        $path      = $directory . '/' . $filename;

        $image = Image::read($file->getPathname())
            ->scaleDown(width: 1920, height: 1920)
            ->toJpeg(quality: 75);

        Storage::disk($disk)->put($path, $image);

        return $path;
    }
}
