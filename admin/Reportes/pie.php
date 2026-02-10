<?php
include ("../lib/jpgraph/src/jpgraph.php");
include ("../lib/jpgraph/src/jpgraph_pie.php");
include ("../lib/jpgraph/src/jpgraph_pie3d.php");

$data = explode(", ",$_GET['datos']);
$leyenda=explode(", ",$_GET['leyenda']);
$graph = new PieGraph(500,300,"auto");
$graph->SetShadow();

//$graph->title->Set($_GET['titulo']);
$graph->title->SetFont(FF_FONT1,FS_BOLD);
//BackgroundImage
//$graph->SetBackgroundImage("../../images/logocm.jpg",3); 
$p1 = new PiePlot3D($data);
$p1->SetSize(.4); 
$p1->ExplodeSlice(1);
$p1->SetCenter(0.40,0.60);
$p1->SetStartAngle(20); 
$p1->SetAngle(35);
$p1->SetLegends($leyenda);

$graph->Add($p1);
$graph->Stroke();

?>


