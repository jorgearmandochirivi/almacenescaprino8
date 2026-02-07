<?php
include ("../jpgraph/src/jpgraph.php");
include ("../jpgraph/src/jpgraph_pie.php");
include ("../jpgraph/src/jpgraph_pie3d.php");

$data = split(",",$_GET['datos']);
$leyenda=split(",",$_GET['leyenda']);
$graph = new PieGraph(330,200,"auto");
$graph->SetShadow();

$graph->title->Set($_GET['titulo']);
$graph->title->SetFont(FF_FONT1,FS_BOLD);
//BackgroundImage
$graph->SetBackgroundImage("../images/logocm.jpg",3); 
$p1 = new PiePlot3D($data);
$p1->ExplodeSlice(1);
$p1->SetCenter(0.45);
$p1->SetLegends($leyenda);

$graph->Add($p1);
$graph->Stroke();

?>


