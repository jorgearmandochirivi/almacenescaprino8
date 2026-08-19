<?php


include("../admin/config.inc.php");
if (!class_exists('PdfModern')) {
  require_once(__DIR__ . "/../admin/lib/PdfModern.php");
}
Encabezado();

//$datos = Verifica_SesionCliente();

$IDPuntoVenta = $datos['IDPuntoVenta'];


if ($_GET['id']) {
  $id_bonos = explode("|", $_GET['id']);
}

require_once $libdir . 'codigobarras.php';



?>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
  <meta name="generator" content="Adobe GoLive 6">
  <title>Caprino :: Entradas</title>
  <link rel="stylesheet" href="../styles.css?1" type="text/css">
  <style type="text/css">
    @page {
      size: 74mm 190mm;
      margin: 0;
    }

    html {
      margin: 0;
      padding: 0;
    }

    body {
      font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif;
      font-size: 7pt;
      margin: 0 0 0 6mm;
      padding: 0 2mm 1mm 1mm;
      width: 62mm;
      box-sizing: border-box;
    }

    .texto {
      font-size: 7pt;
      line-height: 1.08;
      color: #000000;
    }

    .bono {
      width: 100%;
      border-collapse: collapse;
      margin: 0;
    }

    .bono td {
      vertical-align: top;
    }

    .logo {
      width: 52mm;
      height: auto;
    }

    .barcode {
      display: block;
      width: 62mm;
      height: auto;
      margin-top: 2mm;
    }

    ul {
      margin: 2mm 0 0 4mm;
      padding-left: 4mm;
    }

    li {
      margin-bottom: 1mm;
    }
  </style>
</head>

<body bgcolor="#ffffff" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">

  <?php


  if (count($id_bonos) > 0) {

    foreach ($id_bonos as $id_bono_value) {

      $qid = db_query("SELECT * FROM BonoFidelizacion WHERE IDBonoFidelizacion = '$id_bono_value' ");
      $r = db_fetch_object($qid);

      //$parametros_codigo_barras=(int)$id_bono_value*21+133;
      $valorNumericoBono = (int)$id_bono_value * 21 + 133;
      $parametros_codigo_barras = "BonoCaprino-" . $valorNumericoBono;
      $IDCliente = $r->IDCliente;
      $alto_barras = '30';
      $ImagenCodigo = generar_codigo_barras($parametros_codigo_barras, $IDCliente, $alto_barras, $libdir, $dirroot);

      $url_barras = $url . "../files/codigobarras/" . $ImagenCodigo;




      $filedir = $dirroot . "../files/bonos/";
      $name = "Bono" . $id_bono_value . ".html";
      $namePDF = "Bono" . $id_bono_value . ".pdf";
      $file = "$filedir$name";
      $filepdf = "$filedir$namePDF";
      ob_start();
  ?>

      <table class="bono">
        <tr>
          <td colspan="2"><img class="logo" src="http://www.almacenescaprino.com/images/cabezote actual-01.png" alt="" /></td>
        </tr>
        <tr>
          <td class=texto>Numero..</td>
          <td class=texto><?php echo $r->IDBonoFidelizacion; ?></td>
        </tr>
        <tr>
          <td class=texto>Fecha de Vencimiento: </td>
          <td class=texto><?php echo $r->FechaVencimiento; ?><br>
            ( <?php echo $vigencia_bonos = get_field("ParametroFidelizacion", "Valor", "IDParametroFidelizacion", 3); ?> meses a partir de hoy)
          </td>
        </tr>
        <tr>
          <td class=texto>Valor BONO</span></td>
          <td class=texto><?php echo "$" . number_format($r->Valor, 2); ?></td>
        </tr>
        <tr>
          <td class=texto>&nbsp;</td>
          <td class=texto>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" class=texto>
            Nombre Cliente: <?php echo get_field("Cliente", "Nombre", "IDCliente", $r->IDCliente) . " " . get_field("Cliente", "Apellido", "IDCliente", $r->IDCliente); ?><br>
            Documento Cliente: <?php echo get_field("Cliente", "Cedula", "IDCliente", $r->IDCliente); ?><br><br><br>
            Firma Cliente:______________
            <br><br>
            Vendedor Redime Bono:______________
            <br><br>
            Firma Vendedor:______________

            <br>

            <img class="barcode" src="<?php echo $url_barras ?>" alt="Código de barras">


          </td>
        </tr>

        <tr>
          <td colspan="2" class=texto>
            <ul>
              <li>Cada bono tiene un c&oacute;digo &uacute;nico que est&aacute; asociado a tu documento de identidad y s&oacute;lo podr&aacute; ser redimido una vez. </li>
              <li>Puedes utilizar los bonos como medio de pago en cualquiera de nuestras tiendas Caprino o por compras en nuestra tienda virtual.</li>
              <li>Debes llenar el bono con tus datos y firma para poderlo redimir y hacerlo v&aacute;lido. Este bono podr&aacute;s transferirlo a un tercero, siempre y cuando venga diligenciado con tus datos. </li>
              <li>Si no usas el bono durante el periodo de vigencia, &eacute;ste se vencer&aacute; y no podr&aacute; ser utilizado.</li>
              <li>Bono redimible hasta por el 50% del valor de la compra o compras superiores a $100.000.</li>
              <li>No acumulable con otras promociones</li>
            </ul>
          </td>
        </tr>
        <tr>
          <td colspan="2" class=texto>

            <?php

            $page = ob_get_contents();
            $pdf_page = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>'
              . '@page{size:74mm 190mm;margin:0;}'
              . 'html,body{margin:0;padding:0;} body{font-family:DejaVu Sans Condensed,DejaVu Sans,sans-serif;font-size:7pt;margin:0 0 0 6mm;padding:0 2mm 1mm 1mm;width:62mm;box-sizing:border-box;}'
              . '.texto{font-size:7pt;line-height:1.08;color:#000;}.bono{width:100%;border-collapse:collapse;margin:0;}.bono td{vertical-align:top;}'
              . '.logo{width:52mm;height:auto;}.barcode{display:block;width:62mm;height:auto;margin-top:2mm;}ul{margin:2mm 0 0 4mm;padding-left:4mm;}li{margin-bottom:1mm;}'
              . '</style></head><body>' . $page . '</body></html>';

            $fw = fopen($file, "w");
            fputs($fw, $pdf_page, strlen($pdf_page));
            fclose($fw);
            ob_end_clean();
            echo $page;
            PdfModern::generate($pdf_page, $filepdf, [74, 190]);

            $filedir_download = $url . "../files/bonos/";
            $namePDF = "Bono" . $id_bono_value . ".pdf";
            ?>
            <a href="<?php echo $filedir_download . $namePDF ?>">Abrir pdf</a>

          </td>
        </tr>



      </table>

  <?php


    }
  } ?>
</body>

</html>

<?php
if (!empty($_GET['correo'])) {
  envia_bono_cliente($_GET['id_cliente'], $id_bonos);
}
?>
