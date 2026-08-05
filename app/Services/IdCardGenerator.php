<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

class IdCardGenerator
{
    /**
     * Template photo slot (inside blue border) on id_card.jpg (992×1586).
     * Border approx L=261 R=732 T=411 B=944 — sit nearly flush so no template
     * strip remains under the photo (old H=426 ended ~y=844 and leaked).
     */
    private const PHOTO_X = 266;
    private const PHOTO_Y = 416;
    private const PHOTO_W = 461;
    private const PHOTO_H = 522;

    /**
     * Use the official SOPL ID card JPG as the base.
     * Only name, emp code, blood group (+ photo) are replaced.
     *
     * @param  array{name:string,code:string,blood_group?:string|null,photo_path?:string|null,designation?:string}  $data
     * @return string Relative path on the public disk (e.g. id_cards/SOPL_HP_260.png)
     */
    public function generate(array $data): string
    {
        $name = strtoupper(trim((string) ($data['name'] ?? '')));
        $code = strtoupper(trim((string) ($data['code'] ?? '')));
        $blood = strtoupper(trim((string) ($data['blood_group'] ?? ''))) ?: '—';
        $designation = strtoupper(trim((string) ($data['designation'] ?? 'TRAINER'))) ?: 'TRAINER';
        $photoPath = $data['photo_path'] ?? null;

        if ($name === '' || $code === '') {
            throw new RuntimeException('Name and employee code are required for ID card.');
        }

        $template = $this->templatePath();
        $img = @imagecreatefromjpeg($template);
        if (!$img) {
            throw new RuntimeException('Unable to load ID card template.');
        }

        $black = imagecolorallocate($img, 0, 0, 0);
        $white = imagecolorallocate($img, 255, 255, 255);

        // Always wipe template photo first so old face never leaks under new photo / transparency
        $this->pastePhoto($img, $photoPath, self::PHOTO_X, self::PHOTO_Y, self::PHOTO_W, self::PHOTO_H, $white, $black);

        // Clear old Name / Empl. Code / Designation text (keep signature on the right)
        imagefilledrectangle($img, 70, 970, 820, 1215, $white);
        // Extra strip on the left for Blood Group line (do not cover signature)
        imagefilledrectangle($img, 70, 1215, 470, 1285, $white);

        $font = $this->fontBold();
        $size = 22;
        $labelX = 100;
        $colonX = 310;
        $valueX = 345;
        $baselines = [1015, 1090, 1165, 1240];

        $rows = [
            ['Name', $name],
            ['Empl. Code', $code],
            ['Designation', $designation],
            ['Blood Group', $blood],
        ];

        foreach ($rows as $i => [$label, $value]) {
            $y = $baselines[$i];
            imagettftext($img, $size, 0, $labelX, $y, $black, $font, $label);
            imagettftext($img, $size, 0, $colonX, $y, $black, $font, ':');
            imagettftext(
                $img,
                $size,
                0,
                $valueX,
                $y,
                $black,
                $font,
                $this->fitText($font, $size, $value, $i === 3 ? 130 : 450)
            );
        }

        $safeCode = preg_replace('/[^A-Za-z0-9_\-]/', '_', $code);
        $relative = 'id_cards/'.$safeCode.'.png';
        Storage::disk('public')->makeDirectory('id_cards');
        $absolute = Storage::disk('public')->path($relative);

        if (!imagepng($img, $absolute, 6)) {
            imagedestroy($img);
            throw new RuntimeException('Unable to save ID card image.');
        }
        imagedestroy($img);

        return $relative;
    }

    private function templatePath(): string
    {
        $candidates = [
            storage_path('app/templates/id_card.jpg'),
            base_path('IMG-20260707-WA0031.jpg'),
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        throw new RuntimeException('ID card template image not found.');
    }

    private function pastePhoto($img, ?string $photoPath, int $x, int $y, int $w, int $h, $white, $black): void
    {
        // Wipe past paste box (covers rounded-corner leftovers under blue border)
        imagefilledrectangle($img, $x - 4, $y - 4, $x + $w + 4, $y + $h + 4, $white);

        $source = $this->loadImageOpaque($photoPath, $w, $h);
        if (!$source) {
            $font = $this->fontBold();
            $label = 'NO PHOTO';
            $box = imagettfbbox(16, 0, $font, $label);
            $tw = abs($box[2] - $box[0]);
            imagettftext(
                $img,
                16,
                0,
                (int) ($x + ($w - $tw) / 2),
                (int) ($y + $h / 2),
                $black,
                $font,
                $label
            );
            return;
        }

        imagecopy($img, $source, $x, $y, 0, 0, $w, $h);
        imagedestroy($source);
    }

    /**
     * Load photo and flatten onto opaque white canvas sized to the photo slot (cover crop).
     */
    private function loadImageOpaque(?string $path, int $targetW, int $targetH)
    {
        $raw = $this->loadImage($path);
        if (!$raw) {
            return false;
        }

        $sw = imagesx($raw);
        $sh = imagesy($raw);
        if ($sw < 1 || $sh < 1) {
            imagedestroy($raw);
            return false;
        }

        // Cover-crop: fill slot, no letterboxing (template never shows through)
        $scale = max($targetW / $sw, $targetH / $sh);
        $copyW = (int) round($sw * $scale);
        $copyH = (int) round($sh * $scale);
        $dstX = (int) (($targetW - $copyW) / 2);
        $dstY = (int) (($targetH - $copyH) / 2);

        $canvas = imagecreatetruecolor($targetW, $targetH);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, $targetW, $targetH, $white);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, false);

        imagecopyresampled($canvas, $raw, $dstX, $dstY, 0, 0, $copyW, $copyH, $sw, $sh);
        imagedestroy($raw);

        return $canvas;
    }

    private function loadImage(?string $path)
    {
        if (!$path) {
            return false;
        }

        $absolute = $path;
        if (!is_file($absolute)) {
            $absolute = Storage::disk('public')->path(ltrim(str_replace('\\', '/', $path), '/'));
        }
        if (!is_file($absolute)) {
            return false;
        }

        $info = @getimagesize($absolute);
        if (!$info) {
            return false;
        }

        $img = match ($info[2] ?? null) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($absolute),
            IMAGETYPE_PNG => @imagecreatefrompng($absolute),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($absolute) : false,
            IMAGETYPE_GIF => @imagecreatefromgif($absolute),
            default => false,
        };

        if ($img && ($info[2] ?? null) === IMAGETYPE_PNG) {
            // Flatten PNG alpha onto white so transparency never reveals the template
            $sw = imagesx($img);
            $sh = imagesy($img);
            $flat = imagecreatetruecolor($sw, $sh);
            $bg = imagecolorallocate($flat, 255, 255, 255);
            imagefilledrectangle($flat, 0, 0, $sw, $sh, $bg);
            imagealphablending($flat, true);
            imagecopy($flat, $img, 0, 0, 0, 0, $sw, $sh);
            imagedestroy($img);
            $img = $flat;
        }

        return $img;
    }

    private function fitText(string $font, float $size, string $text, int $maxWidth): string
    {
        if ($text === '') {
            return '—';
        }
        $box = imagettfbbox($size, 0, $font, $text);
        if (abs($box[2] - $box[0]) <= $maxWidth) {
            return $text;
        }

        while (mb_strlen($text) > 3) {
            $text = mb_substr($text, 0, -1);
            $try = $text.'…';
            $box = imagettfbbox($size, 0, $font, $try);
            if (abs($box[2] - $box[0]) <= $maxWidth) {
                return $try;
            }
        }

        return $text;
    }

    private function fontBold(): string
    {
        foreach ([
            'C:\\Windows\\Fonts\\arialbd.ttf',
            'C:\\Windows\\Fonts\\segoeuib.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
        ] as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        throw new RuntimeException('No usable bold TTF font found for ID card.');
    }
}
