<?php

namespace App\Services;

use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PdfService
{
    /**
     * Generar PDF desde una vista Blade
     */
    private function generatePdf(string $view, array $data, string $filename, array $options = []): string
    {
        $html = View::make($view, $data)->render();

        $cleanTitle = $data['title'] ?? str_replace(['.pdf', '_', '-'], ['', ' ', ' '], $filename);
        
        if (preg_match('/<title>(.*?)<\/title>/i', $html)) {
            $html = preg_replace('/<title>(.*?)<\/title>/i', "<title>{$cleanTitle}</title>", $html);
        } elseif (str_contains($html, '<head>')) {
            $html = str_replace('<head>', "<head><title>{$cleanTitle}</title>", $html);
        }

        $defaultOptions = [
            'format' => 'A4',
            'landscape' => false,
            'margin' => ['top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10],
        ];
        $config = array_merge($defaultOptions, $options);

        $sanitizedName = $this->sanitizeFilename($filename);
        if (!str_ends_with(strtolower($sanitizedName), '.pdf')) {
            $sanitizedName .= '.pdf';
        }
        $tempDir = storage_path('app/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $uniqueName = time() . '_' . Str::random(8) . '_' . $sanitizedName;
        $fullPath = $tempDir . DIRECTORY_SEPARATOR . $uniqueName;

        $browsershot = Browsershot::html($html)
            ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu'])
            ->format($config['format'])
            ->margins(
                (float) $config['margin']['top'],
                (float) $config['margin']['right'],
                (float) $config['margin']['bottom'],
                (float) $config['margin']['left']
            )
            ->waitUntilNetworkIdle()
            ->timeout(120);

        if ($config['landscape']) {
            $browsershot->landscape();
        }

        $browsershot->save($fullPath);

        return $fullPath;
    }

    /**
     * Generar y DESCARGAR PDF
     */
    public function downloadFromView(string $view, array $data, string $filename, array $options = [])
    {
        $path = $this->generatePdf($view, $data, $filename, $options);

        $sanitizedName = $this->sanitizeFilename($filename);
        if (!str_ends_with(strtolower($sanitizedName), '.pdf')) {
            $sanitizedName .= '.pdf';
        }
        if (!File::exists($path)) {
            abort(500, 'Error al generar el PDF');
        }

        return response()->download($path, $sanitizedName, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Generar y VISUALIZAR PDF
     */
    public function streamFromView(string $view, array $data, string $filename, array $options = [])
    {
        $path = $this->generatePdf($view, $data, $filename, $options);

        $sanitizedName = $this->sanitizeFilename($filename);
        if (!str_ends_with(strtolower($sanitizedName), '.pdf')) {
            $sanitizedName .= '.pdf';
        }
        if (!File::exists($path)) {
            abort(500, 'Error al generar el PDF');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $sanitizedName . '"',
        ]);
    }

    /**
     * Sanitizar nombre de archivo
     */
    private function sanitizeFilename(string $filename): string
    {
        $name = preg_replace('/\.pdf$/i', '', $filename);
        
        $name = Str::ascii($name);
        
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
        
        $name = preg_replace('/_+/', '_', $name);
        
        $name = trim($name, '_');
        
        $name = substr($name, 0, 100);

        return $name ?: 'documento';
    }
}