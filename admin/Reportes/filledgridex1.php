<?php
include ("../lib/pglib/jpgraph/src/jpgraph.php");
include ("../lib/pglib/jpgraph/src/jpgraph_line.php");

$datay1 = explode(", ",$_GET['datos']);
$datax1= explode(", ",$_GET['opciones']);
//$datay2 = array(12,9,42,8);
//$datay3 = array(5,17,32,24);

// Setup the graph
$graph = new Graph(500,450,"auto");
$graph->SetMarginColor('white');
$graph->SetScale("textlin");
$graph->SetShadow();
$graph->SetFrame(true);
$graph->SetMargin(40,50,30,110);
$graph->img->SetAntiAliasing(); 
$graph->title->Set($_GET['titulo']);
$graph->title->SetFont(FF_FONT1,FS_BOLD,7);
//$graph->xaxis->SetLabelAngle(0);
$graph->xaxis->title->Set("Periodos");
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD,4);
$graph->yaxis->title->Set($_GET['unidad']);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD,4);

$graph->yaxis->HideZeroLabel();
$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#BBCCFF@0.5');
$graph->xgrid->Show();

$graph->xaxis->SetTickLabels($datax1);

// Create the first line
$p1 = new LinePlot($datay1);
$p1->SetColor("navy");
$p1->SetCenter();
//$p1->SetLegend($_GET['titulo']);
$p1->SetWeight(2);
$p1->value->show();
$p1->value->SetFont(FF_FONT1,FS_NORMAL,7);
//$p1->value->SetFormat('$%0.2f');
$graph->Add($p1);

// Create the second line
/*$p2 = new LinePlot($datay2);
$p2->SetColor("red");
$p2->SetLegend('Line 2');
$graph->Add($p2);

// Create the third line
$p3 = new LinePlot($datay3);
$p3->SetColor("orange");
$p3->SetLegend('Line 3');
$graph->Add($p3);*/

$graph->legend->SetShadow('gray@0.4',5);
$graph->legend->SetPos(0.1,0.1,'right','top');
// Output line
$graph->Stroke();

?>


