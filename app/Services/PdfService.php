<?php

namespace App\Services;

use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * Generar PDF desde una vista Blade
     */
    public function generateFromView(string $view, array $data, string $filename, array $options = []): string
    {
        // Renderizar la vista
        $html = View::make($view, $data)->render();

        // Configuración por defecto
        $defaultOptions = [
            'format' => 'A4',
            'landscape' => false,
            'margin' => [
                'top' => 10,      // ← Números sin unidad
                'right' => 10,
                'bottom' => 10,
                'left' => 10,
            ],
        ];

        $config = array_merge($defaultOptions, $options);

        // Ruta temporal para guardar el PDF
        $path = storage_path('app/temp/' . $filename);

        // Asegurar que el directorio existe
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Generar PDF
        $browsershot = Browsershot::html($html)
            ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
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

        $browsershot->save($path);

        return $path;
    }

    /**
     * Generar y descargar PDF
     */
    public function downloadFromView(string $view, array $data, string $filename, array $options = [])
    {
        $path = $this->generateFromView($view, $data, $filename, $options);

        return response()->download($path, $filename, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Generar PDF y retornar como respuesta inline
     */
    public function streamFromView(string $view, array $data, string $filename, array $options = [])
    {
        $path = $this->generateFromView($view, $data, $filename, $options);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}