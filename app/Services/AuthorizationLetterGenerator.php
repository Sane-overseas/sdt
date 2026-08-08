<?php

namespace App\Services;

use App\Models\AsignedSchool;
use App\Models\District;
use App\Models\School;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class AuthorizationLetterGenerator
{
    public function templatePath(): string
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

        throw new \RuntimeException('Authorization letter template not found.');
    }

    /**
     * Build a filled authorization letter as PDF on the public disk.
     * Returns relative storage path (auth_letters/....pdf).
     */
    public function generate(AsignedSchool $assignment, User $trainer, School $school): string
    {
        $school->loadMissing('district');

        $trainerName = trim((string) ($trainer->instructor_name ?: 'Trainer'));
        $mobile = trim((string) ($trainer->instructor_number ?: '—'));
        $code = trim((string) ($trainer->instructor_code ?: '—'));
        $schoolName = trim((string) ($school->school_name ?: '—'));
        $district = $this->resolveDistrictName($assignment, $school);

        $relative = 'auth_letters/'.$assignment->id.'_authorization.pdf';
        $absolute = Storage::disk('public')->path($relative);
        $dir = dirname($absolute);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Prefer PDF from filled Word template when a converter is available;
        // otherwise render the matching HTML letter to PDF.
        $docxAbsolute = $this->buildFilledDocx($assignment, $trainerName, $mobile, $code, $schoolName, $district);
        $converted = $this->convertDocxToPdf($docxAbsolute, $absolute);
        @unlink($docxAbsolute);

        if (!$converted) {
            $this->renderPdfFromHtml($absolute, [
                'trainerName' => $trainerName,
                'mobile' => $mobile,
                'trainerCode' => $code,
                'schoolName' => $schoolName,
                'district' => $district,
                'refNo' => 'SDTP/SOPL/01/8/2026',
                'letterDate' => '01-08-2026',
                'logoPath' => $this->mediaDataUri('image2.png'),
                'signPath' => $this->mediaDataUri('image1.png'),
            ]);
        }

        // Remove old docx if present from earlier versions
        $oldDocx = 'auth_letters/'.$assignment->id.'_authorization.docx';
        if (Storage::disk('public')->exists($oldDocx)) {
            Storage::disk('public')->delete($oldDocx);
        }

        return $relative;
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

    private function buildFilledDocx(
        AsignedSchool $assignment,
        string $trainerName,
        string $mobile,
        string $code,
        string $schoolName,
        string $district
    ): string {
        $tempRelative = 'auth_letters/'.$assignment->id.'_authorization_tmp.docx';
        $absolute = Storage::disk('public')->path($tempRelative);
        $dir = dirname($absolute);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!copy($this->templatePath(), $absolute)) {
            throw new \RuntimeException('Could not copy authorization letter template.');
        }

        $zip = new ZipArchive();
        if ($zip->open($absolute) !== true) {
            throw new \RuntimeException('Could not open authorization letter for writing.');
        }

        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            $zip->close();
            throw new \RuntimeException('Invalid authorization letter template.');
        }

        $xml = str_replace(
            ['__TRAINER_NAME__', '__MOBILE__', '__TRAINER_CODE__', '__SCHOOL_NAME__', '__DISTRICT__'],
            [
                $this->xmlSafe($trainerName),
                $this->xmlSafe($mobile),
                $this->xmlSafe($code),
                $this->xmlSafe($schoolName),
                $this->xmlSafe($district),
            ],
            $xml
        );

        $zip->addFromString('word/document.xml', $xml);
        $zip->close();

        return $absolute;
    }

    private function convertDocxToPdf(string $docxAbsolute, string $pdfAbsolute): bool
    {
        $soffice = $this->findSoffice();
        if (!$soffice) {
            return false;
        }

        $outDir = dirname($pdfAbsolute);
        $cmd = escapeshellarg($soffice)
            .' --headless --nologo --nofirststartwizard --convert-to pdf --outdir '
            .escapeshellarg($outDir).' '
            .escapeshellarg($docxAbsolute);

        @exec($cmd, $output, $code);
        if ($code !== 0) {
            return false;
        }

        $produced = preg_replace('/\.docx$/i', '.pdf', $docxAbsolute);
        if ($produced && is_file($produced) && realpath($produced) !== realpath($pdfAbsolute)) {
            @rename($produced, $pdfAbsolute);
        }

        return is_file($pdfAbsolute);
    }

    private function findSoffice(): ?string
    {
        $candidates = [
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
            '/usr/bin/soffice',
            '/usr/bin/libreoffice',
        ];
        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    private function ensureDompdfAutoload(): void
    {
        if (class_exists(Dompdf::class)) {
            return;
        }

        $map = [
            'Dompdf\\' => base_path('vendor/dompdf/dompdf/src/'),
            'FontLib\\' => base_path('vendor/dompdf/php-font-lib/src/FontLib/'),
            'Svg\\' => base_path('vendor/dompdf/php-svg-lib/src/Svg/'),
            'Masterminds\\' => base_path('vendor/masterminds/html5/src/'),
        ];

        spl_autoload_register(function ($class) use ($map) {
            foreach ($map as $prefix => $dir) {
                if (!str_starts_with($class, $prefix)) {
                    continue;
                }
                $relative = str_replace('\\', '/', substr($class, strlen($prefix))).'.php';
                $file = $dir.$relative;
                if (is_file($file)) {
                    require_once $file;
                }
            }
        });
    }

    private function renderPdfFromHtml(string $absolute, array $data): void
    {
        $this->ensureDompdfAutoload();

        $html = view('pdf.authorization-letter', $data)->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        file_put_contents($absolute, $dompdf->output());
    }

    private function mediaDataUri(string $filename): ?string
    {
        $path = resource_path('auth-letter/media/'.$filename);
        if (!is_file($path)) {
            return null;
        }

        $mime = str_ends_with(strtolower($filename), '.png') ? 'image/png' : 'image/jpeg';

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
    }

    private function xmlSafe(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
