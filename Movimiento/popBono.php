<?php


include("../admin/config.inc.php");
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
    .texto {
      font-family: Verdana, Arial, Helvetica, sans-serif;
      font-size: 6.5px;
      color: #000000;
    }
  </style>
</head>

<body bgcolor="#ffffff" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">

  <?php


  if (count($id_bonos) > 0) {

    foreach ($id_bonos as $id_bono_value) {

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

      $qid = db_query("SELECT * FROM BonoFidelizacion WHERE IDBonoFidelizacion = '$id_bono_value' ");
      $r = db_fetch_object($qid);
  ?>

      <table width="500" align="center">
        <tr>
          <td colspan="2"><img src="http://www.almacenescaprino.com/images/cabezote actual-01.png" alt="" width="215" height="107" /></td>
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

            <img src=<?php echo $url_barras ?> width="500">


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
            $fw = fopen($file, "w");
            fputs($fw, $page, strlen($page));
            fclose($fw);
            ob_end_clean();
            echo $page;
            passthru("/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf");

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