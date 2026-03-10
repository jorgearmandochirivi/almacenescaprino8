<?php
include("../admin/config.inc.php");
if (!class_exists('PdfModern')) {
	require_once(__DIR__ . "/../admin/lib/PdfModern.php");
}
$datos = Verifica_SesionCliente();
$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
$ID_Usuario = $datos["IDUsuario"];
$Nivel =  $datos["Nivel"];
$IVA = $datos["IVA"];
$IDPuntoVenta = $datos["IDPuntoVenta"];

$TitleMod = "Cliente";
$Table = "Cliente";
$Key = "IDCliente";

$qid = db_query(" SELECT * FROM Cliente WHERE IDCliente = '$id'  ");
$r = db_fetch_object($qid);

$sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
$qry_puntoventa = db_query($sql_puntoVenta);
$r_puntoventa = db_fetch_object($qry_puntoventa);

$filedir = $dirroot . "/files/facturas/";
$name = "Cliente" . $r->IDCliente . ".html";
$namePDF = "Cliente" . $r->IDCliente . ".pdf";
$file = "$filedir$name";
$filepdf = "$filedir$namePDF";

ob_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: 60mm 150mm;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            margin: 0;
            padding: 5mm;
            width: 50mm;
        }

        .texto {
            font-size: 7.5pt;
            line-height: 1.2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .border-top {
            border-top: 1px dotted #000;
            margin-top: 5px;
            padding-top: 5px;
        }

        .label {
            width: 40%;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="center bold texto">
        IMACAL SAS<br>
        NIT <?= get_field("NIT", "NIT", "IDNIT", 1); ?><br>
        Régimen común
    </div>

    <div class="texto border-top" style="margin-top: 10px;">
        <strong>Fecha Registro:</strong> <?= date("Y-m-d") ?><br>
        <?php if (!empty($r->FechaTrEd) && $r->FechaTrEd != "0000-00-00 00:00:00"): ?>
            <strong>Fecha Act:</strong> <?= substr($r->FechaTrEd, 0, 10) ?><br>
        <?php endif; ?>
        <strong>Vendedor:</strong> <?= get_field("Empleado", "Nombre", "IDEmpleado", $r->IDEmpleado) . " " . get_field("Empleado", "Apellidos", "IDEmpleado", $r->IDEmpleado) ?><br>
    </div>

    <div class="texto border-top" style="margin-top: 10px;">
        <strong>Nombre:</strong> <?= $r->Nombre . " " . $r->Apellido; ?><br>
        <strong>Cédula:</strong> <?= $r->Cedula ?><br>
        <strong>Nacimiento:</strong> <?= $r->Ano . "-" . $r->Mes . "-" . $r->Dia  ?><br>
        <strong>Email:</strong> <?= $r->EMail  ?><br>
        <strong>Tel/Cel:</strong> <?= $r->Telefono  ?> / <?= $r->Celular  ?><br>
        <strong>Dir:</strong> <?= str_replace(" ", "_", $r->Direccion) . "(" . get_field("Ciudad", "Descripcion", "IDCiudad", $r->IDCiudad) . ")"; ?>
    </div>

    <div class="texto border-top" style="margin-top: 10px;">
        <strong>SMS:</strong> <?= $r->AceptaSMS  ?><br>
        <strong>Email Mkt:</strong> <?= $r->AutorizaMail  ?><br>
        <strong>Términos:</strong> <?= $r->AceptaTerminos  ?><br>
        <strong>Habeas Data:</strong> <?= $r->AceptaHabeas  ?><br>
        <strong>Tarjeta:</strong> <?= $r->NumeroTarjeta  ?>
    </div>

    <div class="texto" style="margin-top: 15px; text-align: justify; font-size: 6.5pt;">
        Acepto, permito y autorizo, a calzado caprino que la información suministrada sea utilizada con fines administrativos, mercadeo y de ventas. Por favor verifique sus datos.
    </div>

    <div class="center" style="margin-top: 30px; border-top: 1px solid #000; padding-top: 5px;">
        Firma y Cédula
    </div>

    <div class="center" style="margin-top: 20px;">
        <a href="/admin/files/facturas/Cliente<?= $r->IDCliente ?>.pdf">DESCARGAR PDF</a>
    </div>

</body>

</html>
<?php
$html = ob_get_clean();
$fw = fopen($file, "w");
fputs($fw, $html);
fclose($fw);

echo $html;
PdfModern::generate($html, $filepdf, [60, 150]);
?>
