<?php
/* CAT:Bar Chart */

/* pChart library inclusions */
include("C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/class/pData.class.php");
include("C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/class/pDraw.class.php");
include("C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/class/pImage.class.php");

$dataFile = 'C:\xampp\htdocs\addons\assets\mycomponents\sizematters\core\components\sizematters\logs\m-06';
$file = fopen($dataFile, 'r');

$emsMax = 0;
$ems = array_fill(10, 100, 0);

//$line is an array of the csv elements
while (($line = fgetcsv($file)) !== FALSE) {
    $emsVal = $line[1];
    $emsVal = $emsVal == 0? VOID : $emsVal;
    $ems[(int)$emsVal]++;
}
fclose($file);

foreach ($ems as $k => $v) {
    if ($v == 0) {
        $ems[$k] = VOID;
    }
}

$emsMax = max($ems) + 1;
/* Create and populate the pData object */
$MyData = new pData();
$MyData->addPoints($ems, "Width in Ems");
$MyData->setSerieDescription("Width in Ems", "Width in ems");

// $MyData->setAbscissa("Width in Ems");
// $MyData->addPoints(array(150, 220, 300, -250, -420, -200, 300, 200, 100), "Server A");
// $MyData->addPoints(array(140, 0, 340, -300, -320, -300, 200, 100, 50), "Server B");
$MyData->setAxisName(0, "Hits");







/* Create the pChart object */
$myPicture = new pImage(700, 230, $MyData);
$myPicture->drawGradientArea(0, 0, 700, 230, DIRECTION_VERTICAL, array("StartR" => 240, "StartG" => 240, "StartB" => 240, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 100));
$myPicture->drawGradientArea(0, 0, 700, 230, DIRECTION_HORIZONTAL, array("StartR" => 240, "StartG" => 240, "StartB" => 240, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 20));
$myPicture->setFontProperties(array("FontName" => "C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/fonts/verdana.ttf", "FontSize" => 10));



/* Draw the scale  */
$myPicture->setGraphArea(50, 30, 680, 200);
$myPicture->drawScale(array("CycleBackground" => TRUE, "LabelSkip" => 9, "DrawSubTicks" => TRUE, "GridR" => 0, "GridG" => 0, "GridB" => 0, "GridAlpha" => 10));
$myPicture->drawText(350, 55, "Viewport Width in Ems", array("FontSize" => 20, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));
/* Turn on shadow computing */
$myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));

/* Draw the chart */
$settings = array("Gradient" =>FALSE, "DisplayPos" => LABEL_POS_TOP, "DisplayValues" => FALSE, "DisplayR" => 0, "DisplayG" => 0, "DisplayB" => 0, "DisplayShadow" => TRUE, ); //array("Surrounding"=>-30,"InnerSurrounding"=>30)
$myPicture->drawBarChart($settings);

/* Write the chart legend */
// $myPicture->drawLegend(580, 12, array("Style" => LEGEND_BOX, "Mode" => LEGEND_HORIZONTAL));

/* Render the picture (choose the best way) */
$myPicture->autoOutput("drawBarChart.shaded.png");
