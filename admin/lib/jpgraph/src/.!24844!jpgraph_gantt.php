<?php
/*=======================================================================
// File:	JPGRAPH_GANTT.PHP
// Description:	JpGraph Gantt plot extension
// Created: 	2001-11-12
// Author:	Johan Persson (johanp@aditus.nu)
// Ver:		$Id: jpgraph_gantt.php,v 1.46.2.36 2003/11/15 17:06:35 aditus Exp $
//
// License:	This code is released under QPL 
// Copyright (c) 2002 Johan Persson
//========================================================================
*/
 
// Scale Header types
DEFINE("GANTT_HDAY",1);
DEFINE("GANTT_HWEEK",2);
DEFINE("GANTT_HMONTH",4);
DEFINE("GANTT_HYEAR",8);
DEFINE("GANTT_HHOUR",16);
DEFINE("GANTT_HMIN",32);

// Bar patterns
DEFINE("GANTT_RDIAG",BAND_RDIAG);	// Right diagonal lines
DEFINE("GANTT_LDIAG",BAND_LDIAG); // Left diagonal lines
DEFINE("GANTT_SOLID",BAND_SOLID); // Solid one color
DEFINE("GANTT_VLINE",BAND_VLINE); // Vertical lines
DEFINE("GANTT_HLINE",BAND_HLINE);  // Horizontal lines
DEFINE("GANTT_3DPLANE",BAND_3DPLANE);  // "3D" Plane
DEFINE("GANTT_HVCROSS",BAND_HVCROSS);  // Vertical/Hor crosses
DEFINE("GANTT_DIAGCROSS",BAND_DIAGCROSS); // Diagonal crosses

// Conversion constant
DEFINE("SECPERDAY",3600*24);

// Locales. ONLY KEPT FOR BACKWARDS COMPATIBILITY
// You should use the proper locale strings directly 
// from now on. 
DEFINE("LOCALE_EN","en_UK");
DEFINE("LOCALE_SV","sv_SE");

// Layout of bars
DEFINE("GANTT_EVEN",1);
DEFINE("GANTT_FROMTOP",2);

// Style for minute header
DEFINE("MINUTESTYLE_MM",0);		// 15
DEFINE("MINUTESTYLE_CUSTOM",2);		// Custom format


// Style for hour header
DEFINE("HOURSTYLE_HM24",0);		// 13:10
DEFINE("HOURSTYLE_HMAMPM",1);		// 1:10pm
DEFINE("HOURSTYLE_H24",2);		// 13
DEFINE("HOURSTYLE_HAMPM",3);		// 1pm
DEFINE("HOURSTYLE_CUSTOM",4);		// User defined

// Style for day header
DEFINE("DAYSTYLE_ONELETTER",0);		// "M"
DEFINE("DAYSTYLE_LONG",1);		// "Monday"
DEFINE("DAYSTYLE_LONGDAYDATE1",2);	// "Monday 23 Jun"
DEFINE("DAYSTYLE_LONGDAYDATE2",3);	// "Monday 23 Jun 2003"
DEFINE("DAYSTYLE_SHORT",4);		// "Mon"
DEFINE("DAYSTYLE_SHORTDAYDATE1",5);	// "Mon 23/6"
DEFINE("DAYSTYLE_SHORTDAYDATE2",6);	// "Mon 23 Jun"
DEFINE("DAYSTYLE_SHORTDAYDATE3",7);	// "Mon 23"
DEFINE("DAYSTYLE_SHORTDATE1",8);	// "23/6"
DEFINE("DAYSTYLE_SHORTDATE2",9);	// "23 Jun"
DEFINE("DAYSTYLE_SHORTDATE3",10);	// "Mon 23"
DEFINE("DAYSTYLE_CUSTOM",11);		// "M"

// Styles for week header
DEFINE("WEEKSTYLE_WNBR",0);
DEFINE("WEEKSTYLE_FIRSTDAY",1);
DEFINE("WEEKSTYLE_FIRSTDAY2",2);
DEFINE("WEEKSTYLE_FIRSTDAYWNBR",3);
DEFINE("WEEKSTYLE_FIRSTDAY2WNBR",4);

// Styles for month header
DEFINE("MONTHSTYLE_SHORTNAME",0);
DEFINE("MONTHSTYLE_LONGNAME",1);
DEFINE("MONTHSTYLE_LONGNAMEYEAR2",2);
DEFINE("MONTHSTYLE_SHORTNAMEYEAR2",3);
DEFINE("MONTHSTYLE_LONGNAMEYEAR4",4);
DEFINE("MONTHSTYLE_SHORTNAMEYEAR4",5);


// Types of constrain links
DEFINE('CONSTRAIN_STARTSTART',0);
DEFINE('CONSTRAIN_STARTEND',1);
DEFINE('CONSTRAIN_ENDSTART',2);
DEFINE('CONSTRAIN_ENDEND',3);

// Arrow direction for constrain links
DEFINE('ARROW_DOWN',0);
DEFINE('ARROW_UP',1);
DEFINE('ARROW_LEFT',2);
DEFINE('ARROW_RIGHT',3);

// Arrow type for constrain type
DEFINE('ARROWT_SOLID',0);
DEFINE('ARROWT_OPEN',1);

// Arrow size for constrain lines
DEFINE('ARROW_S1',0);
DEFINE('ARROW_S2',1);
DEFINE('ARROW_S3',2);
DEFINE('ARROW_S4',3);
DEFINE('ARROW_S5',4);

// Activity types for use with utility method CreateSimple()
DEFINE('ACTYPE_NORMAL',0);
DEFINE('ACTYPE_GROUP',1);
DEFINE('ACTYPE_MILESTONE',2);

DEFINE('ACTINFO_3D',1);
DEFINE('ACTINFO_2D',0);


// Check if array_fill() exists
if (!function_exists('array_fill')) {
    function array_fill($iStart, $iLen, $vValue) {
	$aResult = array();
	for ($iCount = $iStart; $iCount < $iLen + $iStart; $iCount++) {
	    $aResult[$iCount] = $vValue;
	}
	return $aResult;
    }
}

//===================================================
// CLASS GanttActivityInfo
// Description: 
//===================================================
class GanttActivityInfo {
    var $iColor='black';
    var $iBackgroundColor='lightgray';
    var $iFFamily=FF_FONT1,$iFStyle=FS_NORMAL,$iFSize=10,$iFontColor='black';
    var $iTitles=array();
    var $iWidth=array(),$iHeight=-1;
    var $iLeftColMargin=4,$iRightColMargin=1,$iTopColMargin=1,$iBottomColMargin=3;
    var $iTopHeaderMargin = 4;
    var $vgrid = null;
    var $iStyle=1;
    var $iShow=true;
    var $iHeaderAlign='center';

    function GanttActivityInfo() {
	$this->vgrid = new LineProperty();
    }

    function Hide($aF=true) {
	$this->iShow=!$aF;
    }

    function Show($aF=true) {
	$this->iShow=$aF;
    }

    // Specify font
    function SetFont($aFFamily,$aFStyle=FS_NORMAL,$aFSize=10) {
	$this->iFFamily = $aFFamily;
	$this->iFStyle	 = $aFStyle;
	$this->iFSize	 = $aFSize;
    }

    function SetStyle($aStyle) {
	$this->iStyle = $aStyle;
    }

    function SetColumnMargin($aLeft,$aRight) {
	$this->iLeftColMargin = $aLeft;
	$this->iRightColMargin = $aRight;
    }

    function SetFontColor($aFontColor) {
	$this->iFontColor = $aFontColor;
    }

    function SetColor($aColor) {
	$this->iColor = $aColor;
    }

    function SetBackgroundColor($aColor) {
	$this->iBackgroundColor = $aColor;
    }

    function SetColTitles($aTitles,$aWidth=null) {
	$this->iTitles = $aTitles;
	$this->iWidth = $aWidth;
    }

    function SetMinColWidth($aWidths) {
	$n = min(count($this->iTitles),count($aWidths));
	for($i=0; $i < $n; ++$i ) {
	    if( !empty($aWidths[$i]) ) {
		if( empty($this->iWidth[$i]) ) {
		    $this->iWidth[$i] = $aWidths[$i];
		}
		else {
		    $this->iWidth[$i] = max($this->iWidth[$i],$aWidths[$i]);
		}
	    }
	}
    }

    function GetWidth($aImg) {
	$txt = new TextProperty();
	$txt->SetFont($this->iFFamily,$this->iFStyle,$this->iFSize);
	$n = count($this->iTitles) ;
	$rm=$this->iRightColMargin;
	$w = 0;
	for($h=0, $i=0; $i < $n; ++$i ) {
	    $w += $this->iLeftColMargin;
	    $txt->Set($this->iTitles[$i]);
	    if( !empty($this->iWidth[$i]) ) {
		$w1 = max($txt->GetWidth($aImg)+$rm,$this->iWidth[$i]);
	    }
	    else {
		$w1 = $txt->GetWidth($aImg)+$rm;
	    }
	    $this->iWidth[$i] = $w1;
	    $w += $w1;
	    $h = max($h,$txt->GetHeight($aImg));
	}
	$this->iHeight = $h+$this->iTopHeaderMargin;
        $txt='';
	return $w;
    }
    
    function GetColStart($aImg,&$ioStart,$aAddLeftMargin=false) {
	$n = count($this->iTitles) ;
	$adj = $aAddLeftMargin ? $this->iLeftColMargin : 0;
	$ioStart=array($aImg->left_margin+$adj);
	for( $i=1; $i < $n; ++$i ) {
	    $ioStart[$i] = $ioStart[$i-1]+$this->iLeftColMargin+$this->iWidth[$i-1];
	}
    }
    
    // Adjust headers left, right or centered
    function SetHeaderAlign($aAlign) {
	$this->iHeaderAlign=$aAlign;
    }

    function Stroke($aImg,$aXLeft,$aYTop,$aXRight,$aYBottom,$aUseTextHeight=false) {

	if( !$this->iShow ) return;

	$txt = new TextProperty();
	$txt->SetFont($this->iFFamily,$this->iFStyle,$this->iFSize);
	$txt->SetColor($this->iFontColor);
	$txt->SetAlign($this->iHeaderAlign,'top');
	$n=count($this->iTitles);

	if( $n == 0 ) 
	    return;
	
	$x = $aXLeft;
	$h = $this->iHeight;
	$yTop = $aUseTextHeight ? $aYBottom-$h-$this->iTopColMargin-$this->iBottomColMargin : $aYTop ;

	if( $h < 0 ) {
	    JpGraphError::Raise('Internal error. Height for ActivityTitles is < 0');
	}

	$aImg->SetLineWeight(1);
	// Set background color
	$aImg->SetColor($this->iBackgroundColor);
	$aImg->FilledRectangle($aXLeft,$yTop,$aXRight,$aYBottom-1);

	if( $this->iStyle == 1 ) {
	    // Make a 3D effect
	    $aImg->SetColor('white');
	    $aImg->Line($aXLeft,$yTop+1,
			$aXRight,$yTop+1);
	}
	
	for($i=0; $i < $n; ++$i ) {
	    if( $this->iStyle == 1 ) {
		// Make a 3D effect
		$aImg->SetColor('white');
		$aImg->Line($x+1,$yTop,$x+1,$aYBottom);
	    }
	    $x += $this->iLeftColMargin;
	    $txt->Set($this->iTitles[$i]);
	    
	    // Adjust the text anchor position according to the choosen alignment
	    $xp = $x;
	    if( $this->iHeaderAlign == 'center' ) {
		$xp = (($x-$this->iLeftColMargin)+($x+$this->iWidth[$i]))/2;
	    }
	    elseif( $this->iHeaderAlign == 'right' ) {
		$xp = $x +$this->iWidth[$i]-$this->iRightColMargin;
	    }
		    
	    $txt->Stroke($aImg,$xp,$yTop+$this->iTopHeaderMargin);
	    $x += $this->iWidth[$i];
	    if( $i < $n-1 ) {
		$aImg->SetColor($this->iColor);
		$aImg->Line($x,$yTop,$x,$aYBottom);
	    }
	}

	$aImg->SetColor($this->iColor);
	$aImg->Line($aXLeft,$yTop, $aXRight,$yTop);

	// Stroke vertical column dividers
	$cols=array();
	$this->GetColStart($aImg,$cols);
	$n=count($cols);
	for( $i=1; $i < $n; ++$i ) {
	    $this->vgrid->Stroke($aImg,$cols[$i],$aYBottom,$cols[$i],
				    $aImg->height - $aImg->bottom_margin);
	}
    }
}


//===================================================
// CLASS GanttGraph
// Description: Main class to handle gantt graphs
//===================================================
class GanttGraph extends Graph {
    var $scale;							// Public accessible
    var $iObj=array();				// Gantt objects
    var $iLabelHMarginFactor=0.2;	// 10% margin on each side of the labels
    var $iLabelVMarginFactor=0.4;	// 40% margin on top and bottom of label
    var $iLayout=GANTT_FROMTOP;	// Could also be GANTT_EVEN
    var $iSimpleFont = FF_FONT1,$iSimpleFontSize=11;
    var $iSimpleStyle=GANTT_RDIAG,$iSimpleColor='yellow',$iSimpleBkgColor='red';
    var $iSimpleProgressBkgColor='gray',$iSimpleProgressColor='darkgreen';
    var $iSimpleProgressStyle=GANTT_SOLID;
//---------------
// CONSTRUCTOR	
    // Create a new gantt graph
    function GanttGraph($aWidth=0,$aHeight=0,$aCachedName="",$aTimeOut=0,$aInline=true) {

	// Backward compatibility
	if( $aWidth == -1 ) $aWidth=0;
	if( $aHeight == -1 ) $aHeight=0;

	if( $aWidth<  0 || $aHeight < 0 ) {
	    JpgraphError::Raise("You can't specify negative sizes for Gantt graph dimensions. Use 0 to indicate that you want the library to automatically determine a dimension.");
	}
	Graph::Graph($aWidth,$aHeight,$aCachedName,$aTimeOut,$aInline);		
	$this->scale = new GanttScale($this->img);
	if( $aWidth > 0 )
		$this->img->SetMargin($aWidth/17,$aWidth/17,$aHeight/7,$aHeight/10);
		
	$this->scale->ShowHeaders(GANTT_HWEEK|GANTT_HDAY);
	$this->SetBox();
    }
	
//---------------
// PUBLIC METHODS

    // 

    function SetSimpleFont($aFont,$aSize) {
	$this->iSimpleFont = $aFont;
	$this->iSimpleFontSize = $aSize;
    }

    function SetSimpleStyle($aBand,$aColor,$aBkgColor) {
	$this->iSimpleStyle = $aBand;
	$this->iSimpleColor = $aColor;
	$this->iSimpleBkgColor = $aSimpleBkgColor;
    }

    // A utility function to help create basic Gantt charts
    function CreateSimple($data,$constrains=array(),$progress=array()) {
	
	for( $i=0; $i < count($data); ++$i) {
	    switch( $data[$i][1] ) {
		case ACTYPE_GROUP:
		    // Create a slightly smaller height bar since the
		    // "wings" at the end will make it look taller
		    $a = new GanttBar($data[$i][0],$data[$i][2],$data[$i][3],$data[$i][4],'',8);
		    $a->title->SetFont($this->iSimpleFont,FS_BOLD,$this->iSimpleFontSize);		
		    $a->rightMark->Show();
		    $a->rightMark->SetType(MARK_RIGHTTRIANGLE);
		    $a->rightMark->SetWidth(8);
		    $a->rightMark->SetColor('black');
		    $a->rightMark->SetFillColor('black');
	    
		    $a->leftMark->Show();
		    $a->leftMark->SetType(MARK_LEFTTRIANGLE);
		    $a->leftMark->SetWidth(8);
		    $a->leftMark->SetColor('black');
		    $a->leftMark->SetFillColor('black');
	    
		    $a->SetPattern(BAND_SOLID,'black');
		    $csimpos = 6;
		    break;
		
		case ACTYPE_NORMAL:
		    $a = new GanttBar($data[$i][0],$data[$i][2],$data[$i][3],$data[$i][4],'',10);
		    $a->title->SetFont($this->iSimpleFont,FS_NORMAL,$this->iSimpleFontSize);
		    $a->SetPattern($this->iSimpleStyle,$this->iSimpleColor);
		    $a->SetFillColor($this->iSimpleBkgColor);
		    // Check if this activity should have a constrain line
		    $n = count($constrains);
		    for( $j=0; $j < $n; ++$j ) {
			if( $constrains[$j][0]==$data[$i][0] ) {
			    $a->SetConstrain($constrains[$j][1],$constrains[$j][2],'black',ARROW_S2,ARROWT_SOLID);    
			    break;
			}
		    }

		    // Check if this activity have a progress bar
		    $n = count($progress);
		    for( $j=0; $j < $n; ++$j ) {
			if( $progress[$j][0]==$data[$i][0] ) {
			    $a->progress->Set($progress[$j][1]);
			    $a->progress->SetPattern($this->iSimpleProgressStyle,
						     $this->iSimpleProgressColor);
			    $a->progress->SetFillColor($this->iSimpleProgressBkgColor);
			    //$a->progress->SetPattern($progress[$j][2],$progress[$j][3]);
			    break;
			}
		    }
		    $csimpos = 6;
		    break;

		case ACTYPE_MILESTONE:
		    $a = new MileStone($data[$i][0],$data[$i][2],$data[$i][3]);
		    $a->title->SetFont($this->iSimpleFont,FS_NORMAL,$this->iSimpleFontSize);
		    $csimpos = 5;
		    break;
		default:
		    die('Unknown activity type');
		    break;
	    }

	    // Setup caption
	    $a->caption->Set($data[$i][$csimpos-1]);

