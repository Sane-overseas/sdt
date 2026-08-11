<?php

namespace App\Services;

use App\Models\AsignedSchool;
use App\Models\District;
use App\Models\School;
use App\Models\State;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AuthorizationLetterGenerator
{
    private ?string $fontRegular = null;

    private ?string $fontBold = null;

    private bool $fontsResolved = false;

    public function templatePath(): ?string
    {
        $candidates = [
            resource_path('auth-letter/SOPL-Auth letter.docx'),
            resource_path('auth-letter/SOPL-Auth-letter.docx'),
            base_path('SOPL-Auth letter.docx'),
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Build a filled authorization letter as PDF on the public disk.
     * Returns relative storage path (auth_letters/....pdf).
     */
    public function generate(AsignedSchool $assignment, User $trainer, School $school): string
    {
        $school->loadMissing('district.state');
        $trainer->loadMissing('state');

        $trainerName = trim((string) ($trainer->instructor_name ?: 'Trainer'));
        $mobile = trim((string) ($trainer->instructor_number ?: '—'));
        $code = trim((string) ($trainer->instructor_code ?: '—'));
        $schoolName = trim((string) ($school->school_name ?: '—'));
        $district = $this->resolveDistrictName($assignment, $school);
        $stateName = $this->resolveStateName($trainer, $school);

        $relative = 'auth_letters/'.$assignment->id.'_authorization.pdf';
        $absolute = Storage::disk('public')->path($relative);
        $dir = dirname($absolute);
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException('Cannot create auth_letters directory: '.$dir);
        }
        if (!is_writable($dir)) {
            throw new \RuntimeException('auth_letters directory is not writable: '.$dir);
        }

        $data = [
            'trainerName' => $trainerName,
            'mobile' => $mobile,
            'trainerCode' => $code,
            'schoolName' => $schoolName,
            'district' => $district,
            'stateName' => $stateName !== '' ? $stateName : '—',
            'refNo' => 'SDTP/SOPL/01/8/'.now()->format('Y'),
            'letterDate' => now()->format('d-m-Y'),
        ];

        $written = false;

        // 1) Dompdf HTML letter (best look) when package is complete on server
        if ($this->dompdfAvailable()) {
            try {
                $this->renderPdfFromHtml($absolute, $data);
                $written = is_file($absolute) && filesize($absolute) > 0;
            } catch (\Throwable $e) {
                Log::warning('Dompdf auth letter failed for #'.$assignment->id.': '.$e->getMessage());
            }
        }

        // 2) GD letter with logo + signature (works without Dompdf; images from resources/auth-letter/media)
        if (!$written) {
            try {
                $this->renderPdfWithGd($absolute, $data);
                $written = is_file($absolute) && filesize($absolute) > 0;
            } catch (\Throwable $e) {
                Log::warning('GD auth letter failed for #'.$assignment->id.': '.$e->getMessage());
            }
        }

        if (!$written) {
            throw new \RuntimeException('Authorization PDF was not written to disk.');
        }

        $oldDocx = 'auth_letters/'.$assignment->id.'_authorization.docx';
        if (Storage::disk('public')->exists($oldDocx)) {
            Storage::disk('public')->delete($oldDocx);
        }

        return $relative;
    }

    public function ensureForAssignment(AsignedSchool $assignment, bool $force = false): ?string
    {
        $status = $assignment->approval_status ?? AsignedSchool::APPROVAL_APPROVED;
        if ($status !== AsignedSchool::APPROVAL_APPROVED && $status !== 'approved') {
            return null;
        }

        if (!$force && !empty($assignment->auth_letter_path)) {
            $absolute = Storage::disk('public')->path($assignment->auth_letter_path);
            if (is_file($absolute) && filesize($absolute) > 0) {
                return $assignment->auth_letter_path;
            }
        }

        $trainer = User::find($assignment->user_id);
        $school = School::find($assignment->school_name);
        if (!$trainer || !$school) {
            Log::warning('Auth letter skipped: missing trainer/school for assignment '.$assignment->id);

            return null;
        }

        try {
            $path = $this->generate($assignment, $trainer, $school);
            $assignment->auth_letter_path = $path;
            $assignment->save();

            return $path;
        } catch (\Throwable $e) {
            Log::error('Auth letter generate failed for assignment '.$assignment->id.': '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return null;
        }
    }

    private function resolveDistrictName(AsignedSchool $assignment, School $school): string
    {
        if ($school->district && !empty($school->district->district)) {
            return trim((string) $school->district->district);
        }

        if (!empty($assignment->district)) {
            $district = District::find($assignment->district);
            if ($district && !empty($district->district)) {
                return trim((string) $district->district);
            }
        }

        return '—';
    }

    private function resolveStateName(User $trainer, School $school): string
    {
        if ($trainer->state && !empty($trainer->state->name)) {
            return trim((string) $trainer->state->name);
        }

        if (!empty($trainer->state_id)) {
            $state = State::find($trainer->state_id);
            if ($state && !empty($state->name)) {
                return trim((string) $state->name);
            }
        }

        $school->loadMissing('district.state');
        if ($school->district && $school->district->state && !empty($school->district->state->name)) {
            return trim((string) $school->district->state->name);
        }

        return '—';
    }

    private function dompdfAvailable(): bool
    {
        $autoload = base_path('vendor/autoload.php');
        if (is_file($autoload)) {
            require_once $autoload;
        }

        return class_exists(\Dompdf\Dompdf::class, true)
            && class_exists(\Dompdf\Options::class, true);
    }

    private function renderPdfFromHtml(string $absolute, array $data): void
    {
        $data['logoPath'] = $this->mediaDataUri('image2.png');
        $data['letterheadPath'] = $this->mediaDataUri('letterhead.png') ?: $data['logoPath'];
        $data['signPath'] = $this->mediaDataUri('image1.png');

        $html = view('pdf.authorization-letter', $data)->render();

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->setChroot(base_path());

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        if ($output === '' || $output === null) {
            throw new \RuntimeException('Dompdf produced empty output.');
        }

        if (file_put_contents($absolute, $output) === false) {
            throw new \RuntimeException('Could not write authorization PDF file.');
        }
    }

    /**
     * Formatted letter via GD (logo + signature + layout) embedded as full-page JPEG PDF.
     * Used when Dompdf is missing on the server.
     */
    private function renderPdfWithGd(string $absolute, array $data): void
    {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('GD extension is required for auth letter fallback.');
        }

        $this->resolveFonts();

        // A4 @ ~150 DPI
        $w = 1240;
        $h = 1754;
        $img = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 17, 17, 17);
        $muted = imagecolorallocate($img, 34, 34, 34);
        $blue = imagecolorallocate($img, 0, 51, 160);
        imagefilledrectangle($img, 0, 0, $w, $h, $white);

        $margin = 70;
        $y = 52; // halka niche from top edge

        // Watermark (faint logo)
        $logoPath = $this->mediaFilePath('image2.png');
        if ($logoPath) {
            $logo = @imagecreatefrompng($logoPath);
            if ($logo) {
                $lw = imagesx($logo);
                $lh = imagesy($logo);
                $wmW = (int) ($w * 0.55);
                $wmH = (int) ($lh * ($wmW / max(1, $lw)));
                $wmX = (int) (($w - $wmW) / 2);
                $wmY = (int) (($h - $wmH) / 2);
                $this->imageCopyMergedAlpha($img, $logo, $wmX, $wmY, $wmW, $wmH, 12);
                imagedestroy($logo);
            }
        }

        // Full letterhead banner image (company name + logo + CIN)
        $letterheadPath = $this->mediaFilePath('letterhead.png');
        if ($letterheadPath) {
            $lhImg = @imagecreatefrompng($letterheadPath);
            if ($lhImg) {
                $lw = imagesx($lhImg);
                $lh = imagesy($lhImg);
                // Trim empty white padding at bottom of letterhead PNG
                $lh = $this->trimImageBottom($lhImg, $lh);
                $drawW = $w - 2 * $margin;
                $drawH = (int) round($lh * ($drawW / max(1, $lw)));
                $maxH = 130;
                if ($drawH > $maxH) {
                    $drawH = $maxH;
                    $drawW = (int) round($lw * ($drawH / max(1, $lh)));
                }
                $drawX = (int) (($w - $drawW) / 2);
                imagecopyresampled($img, $lhImg, $drawX, $y, 0, 0, $drawW, $drawH, $lw, $lh);
                imagedestroy($lhImg);
                $y += $drawH + 2; // tight gap before line
            }
        } else {
            // Fallback if letterhead.png missing
            $this->drawText($img, 24, $margin, $y + 40, $black, 'SANE OVERSEAS PRIVATE LIMITED', true);
            if ($logoPath) {
                $logo = @imagecreatefrompng($logoPath);
                if ($logo) {
                    $lw = imagesx($logo);
                    $lh = imagesy($logo);
                    $dw = 110;
                    $dh = (int) ($lh * ($dw / max(1, $lw)));
                    imagecopyresampled($img, $logo, $w - $margin - $dw, $y, 0, 0, $dw, $dh, $lw, $lh);
                    imagedestroy($logo);
                }
            }
            $y += 100;
        }

        imageline($img, $margin, $y, $w - $margin, $y, $black);
        $y += 28;

        $this->drawText($img, 14, $margin, $y, $black, 'Ref. No. '.($data['refNo'] ?? '—'), true);
        $this->drawText($img, 14, $w - $margin, $y, $black, 'Date: '.($data['letterDate'] ?? '—'), true, false, true);
        $y += 40;

        $this->drawText($img, 14, $margin, $y, $black, 'To', true);
        $y += 22;
        $this->drawText($img, 14, $margin, $y, $black, 'The Principal', true);
        $y += 22;
        $this->drawText($img, 14, $margin, $y, $black, (string) ($data['schoolName'] ?? '—'), true);
        $y += 22;
        $this->drawText($img, 14, $margin, $y, $black, (string) ($data['district'] ?? '—'), true);
        $y += 36;

        $state = (string) ($data['stateName'] ?? '—');
        $subject = 'Subject: Conduct of Rani Laxmi Bai Atam Raksha Prashikshan (Self-Defence Training and Awareness Programme) for Girl Students of GMSs/GHSs/GSSSs/PM SHRI Schools in '.$state;
        $y = $this->drawWrapped($img, 14, $margin, $y, $w - 2 * $margin, $blue, $subject, true);
        $y += 18;

        $this->drawText($img, 14, $margin, $y, $black, 'Sir/Madam,');
        $y += 28;

        $paras = [
            'This is with reference to the Memorandum of Understanding (MoU) for the academic session executed between Samagra Shiksha, '.$state.', and Sane Overseas Private Limited for the implementation of the Rani Laxmi Bai Atam Raksha Prashikshan (Self-Defence Training and Awareness Programme) for girl students studying in Government Middle Schools (GMSs), Government High Schools (GHSs), Government Senior Secondary Schools (GSSSs), and PM SHRI Schools across '.$state.'.',
            'Under this programme, Sane Overseas Private Limited, Mohali, the agency empaneled by Samagra Shiksha, '.$state.', has been entrusted with conducting self-defence training and awareness sessions for girl students of Classes VI to XII.',
            'You are, therefore, requested to extend your cooperation in the successful implementation of this programme by ensuring the following:',
        ];

        foreach ($paras as $para) {
            $y = $this->drawWrapped($img, 13, $margin, $y, $w - 2 * $margin, $black, $para, false);
            $y += 16;
        }

        $bullets = [
            '1. Make suitable time available for the training programme during regular school hours.',
            '2. Encourage and ensure the participation of all eligible girl students (Classes VI to XII).',
            '3. Ensure that the Physical Education Teacher and one Lady Teacher remain present throughout the training sessions for proper coordination and supervision.',
            '4. Extend all necessary support to facilitate the smooth conduct of the programme during the academic session.',
        ];
        foreach ($bullets as $b) {
            $y = $this->drawWrapped($img, 13, $margin + 10, $y, $w - 2 * $margin - 10, $black, $b, false);
            $y += 10;
        }
        $y += 10;

        $y = $this->drawWrapped(
            $img,
            13,
            $margin,
            $y,
            $w - 2 * $margin,
            $black,
            'You are further requested to permit the following authorised trainer(s) deputed by Sane Overseas Private Limited to conduct the training programme in your school.',
            false
        );
        $y += 14;
        $y = $this->drawWrapped(
            $img,
            13,
            $margin,
            $y,
            $w - 2 * $margin,
            $black,
            'Your cooperation in the effective implementation of this important initiative aimed at empowering and ensuring the safety of girl students will be highly appreciated.',
            false
        );
        $y += 28;

        // Trainer table
        $cols = [80, 420, 280, 280];
        $headers = ['S. No.', 'Trainer Name', 'Mobile No.', 'Trainer Code'];
        $rowH = 42;
        $tableW = array_sum($cols);
        $tx = $margin;
        // header
        imagerectangle($img, $tx, $y, $tx + $tableW, $y + $rowH, $black);
        $cx = $tx;
        foreach ($headers as $i => $header) {
            imageline($img, $cx, $y, $cx, $y + $rowH, $black);
            $this->drawText($img, 13, $cx + $cols[$i] / 2, $y + 28, $black, $header, true, true);
            $cx += $cols[$i];
        }
        $y += $rowH;
        $values = ['1', (string) ($data['trainerName'] ?? '—'), (string) ($data['mobile'] ?? '—'), (string) ($data['trainerCode'] ?? '—')];
        imagerectangle($img, $tx, $y, $tx + $tableW, $y + $rowH, $black);
        $cx = $tx;
        foreach ($values as $i => $val) {
            imageline($img, $cx, $y, $cx, $y + $rowH, $black);
            $this->drawText($img, 13, $cx + $cols[$i] / 2, $y + 28, $black, $val, false, true);
            $cx += $cols[$i];
        }
        $y += $rowH + 40;

        $this->drawText($img, 14, $margin, $y, $black, 'Thanking you.');

        // Signature
        $signPath = $this->mediaFilePath('image1.png');
        $signX = $w - $margin - 220;
        $signY = $y - 10;
        if ($signPath) {
            $sign = @imagecreatefrompng($signPath);
            if ($sign) {
                $sw = imagesx($sign);
                $sh = imagesy($sign);
                $dw = 200;
                $dh = (int) ($sh * ($dw / max(1, $sw)));
                imagecopyresampled($img, $sign, $signX, $signY, 0, 0, $dw, $dh, $sw, $sh);
                imagedestroy($sign);
            }
        }

        // Footer line
        $fy = $h - 70;
        imageline($img, $margin, $fy, $w - $margin, $fy, $black);
        $this->drawText(
            $img,
            11,
            $w / 2,
            $fy + 28,
            $muted,
            'REGISTERED OFFICE: PLOT NO-1634, SECTOR-82, INDUSTRIAL AREA MOHALI, PUNJAB',
            false,
            true
        );

        $tmpJpg = $absolute.'.jpg';
        imagejpeg($img, $tmpJpg, 90);
        imagedestroy($img);

        $this->jpegToPdf($tmpJpg, $absolute, $w, $h);
        @unlink($tmpJpg);
    }

    private function jpegToPdf(string $jpegPath, string $pdfPath, int $imgW, int $imgH): void
    {
        $jpeg = file_get_contents($jpegPath);
        if ($jpeg === false || $jpeg === '') {
            throw new \RuntimeException('Could not read generated letter image.');
        }

        // A4 points
        $pageW = 595;
        $pageH = 842;

        $objects = [];
        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
        $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 '.$pageW.' '.$pageH.'] /Contents 4 0 R /Resources << /XObject << /Im0 5 0 R >> >> >>';
        $stream = "q\n".$pageW.' 0 0 '.$pageH." 0 0 cm\n/Im0 Do\nQ";
        $objects[] = '<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream";
        $objects[] = '<< /Type /XObject /Subtype /Image /Width '.$imgW.' /Height '.$imgH
            .' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length '.strlen($jpeg)
            ." >>\nstream\n".$jpeg."\nendstream";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $i => $obj) {
            $offsets[$i + 1] = strlen($pdf);
            $pdf .= ($i + 1)." 0 obj\n".$obj."\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref\n0 '.(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= 'trailer << /Size '.(count($objects) + 1)." /Root 1 0 R >>\n";
        $pdf .= "startxref\n".$xref."\n%%EOF";

        if (file_put_contents($pdfPath, $pdf) === false) {
            throw new \RuntimeException('Could not write GD authorization PDF.');
        }
    }

    private function resolveFonts(): void
    {
        if ($this->fontsResolved) {
            return;
        }
        $this->fontsResolved = true;

        $regular = [
            resource_path('fonts/DejaVuSans.ttf'),
            base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf'),
            resource_path('fonts/arial.ttf'),
            public_path('fonts/DejaVuSans.ttf'),
            'C:\\Windows\\Fonts\\arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
        ];
        $bold = [
            resource_path('fonts/DejaVuSans-Bold.ttf'),
            base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf'),
            resource_path('fonts/arialbd.ttf'),
            public_path('fonts/DejaVuSans-Bold.ttf'),
            'C:\\Windows\\Fonts\\arialbd.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
        ];

        foreach ($regular as $path) {
            if (is_file($path)) {
                $this->fontRegular = $path;
                break;
            }
        }
        foreach ($bold as $path) {
            if (is_file($path)) {
                $this->fontBold = $path;
                break;
            }
        }
        if (!$this->fontBold) {
            $this->fontBold = $this->fontRegular;
        }
        if (!$this->fontRegular) {
            $this->fontRegular = $this->fontBold;
        }
    }

    private function drawText(
        $img,
        float $size,
        float $x,
        float $y,
        int $color,
        string $text,
        bool $bold = false,
        bool $center = false,
        bool $right = false
    ): void {
        $font = $bold ? $this->fontBold : $this->fontRegular;
        if ($font && function_exists('imagettftext')) {
            if ($center || $right) {
                $box = imagettfbbox($size, 0, $font, $text);
                $tw = abs($box[2] - $box[0]);
                if ($center) {
                    $x -= $tw / 2;
                } elseif ($right) {
                    $x -= $tw;
                }
            }
            imagettftext($img, $size, 0, (int) $x, (int) $y, $color, $font, $text);

            return;
        }

        // Built-in font fallback
        $fontId = 5;
        $tw = imagefontwidth($fontId) * strlen($text);
        if ($center) {
            $x -= $tw / 2;
        } elseif ($right) {
            $x -= $tw;
        }
        imagestring($img, $fontId, (int) $x, (int) ($y - 12), $text, $color);
    }

    private function drawWrapped(
        $img,
        float $size,
        float $x,
        float $y,
        float $maxWidth,
        int $color,
        string $text,
        bool $bold = false
    ): float {
        $font = $bold ? $this->fontBold : $this->fontRegular;
        $lineHeight = $size + 8;
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $line = '';

        foreach ($words as $word) {
            $try = $line === '' ? $word : $line.' '.$word;
            $width = $this->measureText($size, $try, $bold);
            if ($width > $maxWidth && $line !== '') {
                $this->drawText($img, $size, $x, $y, $color, $line, $bold);
                $y += $lineHeight;
                $line = $word;
            } else {
                $line = $try;
            }
        }
        if ($line !== '') {
            $this->drawText($img, $size, $x, $y, $color, $line, $bold);
            $y += $lineHeight;
        }

        return $y;
    }

    private function measureText(float $size, string $text, bool $bold = false): float
    {
        $font = $bold ? $this->fontBold : $this->fontRegular;
        if ($font && function_exists('imagettfbbox')) {
            $box = imagettfbbox($size, 0, $font, $text);

            return (float) abs($box[2] - $box[0]);
        }

        return (float) (imagefontwidth(5) * strlen($text));
    }

    /** Trim nearly-white bottom padding from letterhead so line sits closer. */
    private function trimImageBottom($img, int $height): int
    {
        $width = imagesx($img);
        $threshold = 248;
        for ($y = $height - 1; $y > (int) ($height * 0.4); $y--) {
            $hasInk = false;
            for ($x = 0; $x < $width; $x += 3) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                if ($r < $threshold || $g < $threshold || $b < $threshold) {
                    $hasInk = true;
                    break;
                }
            }
            if ($hasInk) {
                return min($height, $y + 4);
            }
        }

        return $height;
    }

    private function imageCopyMergedAlpha($dst, $src, int $dx, int $dy, int $dw, int $dh, int $pct): void
    {
        $tmp = imagecreatetruecolor($dw, $dh);
        imagealphablending($tmp, false);
        imagesavealpha($tmp, true);
        $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
        imagefilledrectangle($tmp, 0, 0, $dw, $dh, $transparent);
        imagealphablending($tmp, true);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $dw, $dh, imagesx($src), imagesy($src));
        imagecopymerge($dst, $tmp, $dx, $dy, 0, 0, $dw, $dh, max(0, min(100, $pct)));
        imagedestroy($tmp);
    }

    private function mediaFilePath(string $filename): ?string
    {
        $candidates = [
            resource_path('auth-letter/media/'.$filename),
            base_path('resources/auth-letter/media/'.$filename),
            public_path('auth-letter/media/'.$filename),
        ];
        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        Log::warning('Auth letter media missing: '.$filename);

        return null;
    }

    private function mediaDataUri(string $filename): ?string
    {
        $path = $this->mediaFilePath($filename);
        if (!$path) {
            return null;
        }

        $mime = str_ends_with(strtolower($filename), '.png') ? 'image/png' : 'image/jpeg';

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($path));
    }

    private function xmlSafe(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
