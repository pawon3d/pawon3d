<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait CompressesImages
{
    /**
     * Convert an uploaded file to WebP and store it in the public disk.
     *
     * @param  TemporaryUploadedFile  $file  Livewire temporary upload
     * @param  string  $folder  Relative path within public storage (e.g. 'store_profiles')
     * @param  int  $quality  WebP quality 0–100 (default 82)
     * @param  int|null  $maxWidth  Resize so width never exceeds this value; null = no resize
     * @return string Relative path stored (e.g. 'store_profiles/uuid.webp')
     */
    protected function storeAsWebP(
        TemporaryUploadedFile $file,
        string $folder,
        int $quality = 82,
        ?int $maxWidth = 1400,
    ): string {
        $mime = $file->getMimeType();
        $sourcePath = $file->getRealPath();

        $image = match ($mime) {
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default => imagecreatefromjpeg($sourcePath), // jpg / jpeg
        };

        if ($image === false) {
            // Fallback: store raw when GD cannot read the file
            return $file->store($folder, 'public');
        }

        // Optional downscale – never upscale
        if ($maxWidth !== null) {
            $origW = imagesx($image);
            $origH = imagesy($image);

            if ($origW > $maxWidth) {
                $newH = (int) round($origH * ($maxWidth / $origW));
                $resized = imagecreatetruecolor($maxWidth, $newH);

                // Preserve transparency for PNG inputs
                imagealphablending($resized, false);
                imagesavealpha($resized, true);

                imagecopyresampled($resized, $image, 0, 0, 0, 0, $maxWidth, $newH, $origW, $origH);
                imagedestroy($image);
                $image = $resized;
            }
        }

        $filename = Str::uuid().'.webp';
        $directory = storage_path("app/public/{$folder}");

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        imagewebp($image, "{$directory}/{$filename}", $quality);
        imagedestroy($image);

        return "{$folder}/{$filename}";
    }
}
