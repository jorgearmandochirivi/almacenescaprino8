<?php

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfModern
{
    /**
     * Genera un PDF a partir de una cadena HTML.
     * 
     * @param string $html Contenido HTML completo.
     * @param string $outputFile Ruta absoluta donde se guardará el PDF.
     * @param array $paperSize [ancho_mm, alto_mm] o nombre del papel.
     * @return bool
     */
    public static function generate($html, $outputFile, $paperSize = [60, 120])
    {
        // Asegurarse de que el autoloader esté cargado si se usa independientemente
        $autoload_root = dirname(__FILE__) . '/../../vendor/autoload.php';
        $autoload_local = dirname(__FILE__) . '/dompdf/autoload.inc.php';

        if (file_exists($autoload_local)) {
            require_once $autoload_local;
        } elseif (file_exists($autoload_root)) {
            require_once $autoload_root;
        } else {
            error_log("PdfModern Error: No se encontró el autoloader en " . $autoload_local . " ni en " . $autoload_root);
            return false;
        }

        try {
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true); // Para imágenes externas
            $options->set('defaultFont', 'Arial');

            $dompdf = new Dompdf($options);

            // Convertir mm a puntos (1mm = 2.83465 pt)
            if (is_array($paperSize)) {
                $width = $paperSize[0] * 2.83465;
                $height = $paperSize[1] * 2.83465;
                $dompdf->setPaper([0, 0, $width, $height]);
            } else {
                $dompdf->setPaper($paperSize);
            }

            // Detectar encoding y convertir a UTF-8 si es necesario.
            // Si mbstring no esta disponible, continuar sin conversion.
            if (function_exists('mb_detect_encoding') && function_exists('mb_convert_encoding')) {
                $encoding = mb_detect_encoding($html, mb_detect_order(), true);
                if ($encoding != 'UTF-8') {
                    $html = mb_convert_encoding($html, 'UTF-8', $encoding ?: 'ISO-8859-1');
                }
            }

            // También podemos inyectar un meta tag de charset si no existe
            if (!str_contains($html, '<meta charset')) {
                $html = str_replace('<head>', '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>', $html);
            }

            // Cargar el HTML
            $dompdf->loadHtml($html);

            // Renderizar
            $dompdf->render();

            // Guardar en archivo
            file_put_contents($outputFile, $dompdf->output());

            return true;
        } catch (\Throwable $e) {
            error_log("PdfModern Exception: " . $e->getMessage());
            return false;
        }
    }
}
