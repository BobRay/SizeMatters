<?php
/* CAT:Bar Chart */

/* pChart library inclusions */
include("C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/class/pData.class.php");
include("C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/class/pDraw.class.php");
include("C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/class/pImage.class.php");

$dataFile = 'C:\xampp\htdocs\addons\assets\mycomponents\sizematters\core\components\sizematters\logs\m-06';
$file = fopen($dataFile, 'r');

$emsMax = 0;
$ems = array_fill(0, 100, VOID);
$pxs = array_fill(1, 2000, VOID);
$fonts = array_fill(4, 40, VOID);

//$line is an array of the csv elements
while (($line = fgetcsv($file)) !== FALSE) {
    $emsVal = $line[1];
    $ems[(int)$emsVal]++;
    
    $pxVal = $line[0];
    $pxs[(int)$pxVal]++;

    $fontVal = $line[2];
    $fonts[(int)$fontVal]++;


}
fclose($file);

/* ********************** */
/*      Ems bar chart      */
/* ********************** */

/* Create and populate the pData object */
$MyData = new pData();
$MyData->addPoints($ems, "Width in Ems");
$MyData->setSerieDescription("Width in Ems", "Width in ems");

/* Draw serie 1 in red with a 80% opacity */

$serieSettings = array("R" => 255, "G" => 0, "B" => 0, "Alpha" => 70);
$MyData->setPalette("Width in Ems", $serieSettings);

$MyData->setAxisName(0, "Hits");


/* Create the pChart object */
$myPicture = new pImage(700, 230, $MyData);
$myPicture->drawGradientArea(0, 0, 700, 230, DIRECTION_VERTICAL, array("StartR" => 0, "StartG" => 124, "StartB" => 180, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 100));
$myPicture->drawGradientArea(0, 0, 700, 230, DIRECTION_HORIZONTAL, array("StartR" => 240, "StartG" => 240, "StartB" => 240, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 20));
$myPicture->setFontProperties(array("FontName" => "C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/fonts/verdana.ttf", "FontSize" => 10));



/* Draw the scale  */
$myPicture->setGraphArea(50, 30, 680, 200);
$myPicture->drawScale(array("CycleBackground" => TRUE, "LabelSkip" => 4, "LabelRotation" => 90, "DrawSubTicks" => TRUE, "GridR" => 0, "GridG" => 0, "GridB" => 0, "GridAlpha" => 10));
$myPicture->drawText(350, 55, "Viewport Width in Ems", array("FontSize" => 15, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));
/* Turn on shadow computing */
$myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));

/* Draw the chart */
$settings = array("Gradient" =>FALSE, "DisplayPos" => LABEL_POS_TOP, "DisplayValues" => FALSE, "DisplayR" => 0, "DisplayG" => 0, "DisplayB" => 0, "DisplayShadow" => TRUE, ); //array("Surrounding"=>-30,"InnerSurrounding"=>30)
$myPicture->drawBarChart($settings);

/* Write the chart legend */
// $myPicture->drawLegend(580, 12, array("Style" => LEGEND_BOX, "Mode" => LEGEND_HORIZONTAL));

/* Render the picture (choose the best way) */
$myPicture->autoOutput("ems-bar-chart.shaded.png");

/* ********************** */
/*      Px bar chart      */
/* ********************** */
unset($MyData, $MyPicture, $pImage);

/* Create and populate the pData object */
$MyData = new pData();

/* Force Y axis to start at 0 */
// $pxs[0] = 1;
$MyData->addPoints(array(1), "Dummy");
$serieSettings = array("R" => 255, "G" => 0, "B" => 0, "Alpha" => 0);
$MyData->setPalette("Dummy", $serieSettings);

$MyData->addPoints($pxs, "Width in CSS Pixels");
$MyData->setSerieDescription("Width CSS Pixels", "Width in CSS Pixels");

/* Draw serie 1 in red with a 80% opacity */

$serieSettings = array("R" => 255, "G" => 0, "B" => 0, "Alpha" => 70);
$MyData->setPalette("Width in CSS Pixels", $serieSettings);

// $MyData->setAbscissa("Width in Ems");
// $MyData->addPoints(array(150, 220, 300, -250, -420, -200, 300, 200, 100), "Server A");
// $MyData->addPoints(array(140, 0, 340, -300, -320, -300, 200, 100, 50), "Server B");
$MyData->setAxisName(0, "Hits");

/* Create the pChart object */
$myPicture = new pImage(700, 330, $MyData);
$myPicture->drawGradientArea(0, 0, 700, 330, DIRECTION_VERTICAL, array("StartR" => 0, "StartG" => 124, "StartB" => 180, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 100));
$myPicture->drawGradientArea(0, 0, 700, 330, DIRECTION_HORIZONTAL, array("StartR" => 240, "StartG" => 240, "StartB" => 240, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 20));
$myPicture->setFontProperties(array("FontName" => "C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/fonts/verdana.ttf", "FontSize" => 10));


/* Draw the scale  */
$myPicture->setGraphArea(50, 30, 680, 280);
$myPicture->drawScale(array("CycleBackground" => TRUE, 'LabelRotation' => 90, "LabelSkip" => 49, "DrawSubTicks" => FALSE, "AutoAxisLabels"=>FALSE, "GridR" => 0, "GridG" => 0, "GridB" => 0, "GridAlpha" => 10));
$myPicture->drawText(350, 55, "Viewport Width CSS Pixels", array("FontSize" => 15, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));
/* Turn on shadow computing */
$myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));

/* Draw the chart */
$settings = array("Gradient" => FALSE, "DisplayPos" => LABEL_POS_TOP, "DisplayValues" => FALSE, "DisplayR" => 0, "DisplayG" => 0, "DisplayB" => 0, "DisplayShadow" => FALSE,); //array("Surrounding"=>-30,"InnerSurrounding"=>30)
$myPicture->drawBarChart($settings);

/* Write the chart legend */
// $myPicture->drawLegend(580, 12, array("Style" => LEGEND_BOX, "Mode" => LEGEND_HORIZONTAL));

/* Render the picture (choose the best way) */
$myPicture->autoOutput("pxs-bar-chart.shaded.png");



