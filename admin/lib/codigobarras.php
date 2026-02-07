<?php

	function generar_codigo_barras($parametros_codigo_barras, $IDCliente, $alto_barras = '',$libdir,$dirroot)
    {
        // Including all required classes
        $dircodigos=$dirroot."../files/codigobarras/";
		require_once $libdir . 'barcodegen/class/BCGFontFile.php';
        require_once $libdir . 'barcodegen/class/BCGColor.php';
        require_once $libdir . 'barcodegen/class/BCGDrawing.php';
        // Including the barcode technology
        require_once $libdir . 'barcodegen/class/BCGcode128.barcode.php';
        // Loading Font
        $font = new BCGFontFile($libdir . 'barcodegen/font/Arial.ttf', 18);

        
        // Don't forget to sanitize user inputs
        //$text = isset($_GET['text']) ? $_GET['text'] : 'SOCIO';

        // The arguments are R, G, B for color.
        $color_black = new BCGColor(0, 0, 0);
        $color_white = new BCGColor(255, 255, 255);

        if ($alto_barras == "") {
            $alto_barras = 30;
        }

        

        $drawException = null;
        try {
            $code = new BCGcode128();
            $code->setScale(8); // Resolution
            //$code->setThickness(30); // Thickness
            $code->setThickness($alto_barras); // Thickness
            $code->setForegroundColor($color_black); // Color of bars
            $code->setBackgroundColor($color_white); // Color of spaces
            $code->setFont($font); // Font (or 0)

            $code->parse($parametros_codigo_barras); // Text
        } catch (Exception $exception) {
            $drawException = $exception;
        }
		
        /* Here is the list of the arguments
        1 - Filename (empty : display on screen)
        2 - Background color */
        $nombre_archivo = $parametros_codigo_barras.'.png';
        $ruta_archivo = $dircodigos . $nombre_archivo;


        $drawing = new BCGDrawing($ruta_archivo, $color_white);
        if ($drawException) {
            $drawing->drawException($drawException);
        } else {
            $drawing->setBarcode($code);
            $drawing->draw();
        }

        // Header that says it is an image (remove it if you save the barcode to a file)
        //header('Content-Type: image/png');
        //header('Content-Disposition: inline; filename="barcode.png"');
        // Draw (or save) the image into PNG format.
        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

        return $nombre_archivo;
    }
?>