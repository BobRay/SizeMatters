<?php
/* CAT:Bar Chart */

/* pChart library inclusions */
include("C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/class/pData.class.php");
include("C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/class/pDraw.class.php");
include("C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/class/pImage.class.php");

$dataFile = 'C:\xampp\htdocs\addons\assets\mycomponents\sizematters\core\components\sizematters\logs\m-06';
$file = fopen($dataFile, 'r');

/* Fill arrays with VOID constant (0.123456789) */
$ems = array_fill(0, 100, VOID);
$pxs = array_fill(1, 2000, VOID);
$fonts = array_fill(4, 40, VOID);

/* $line is an array of the csv elements */
while (($line = fgetcsv($file)) !== FALSE) {
    /* If final array value is VOID, make it one, else increment it */
    $emsVal = $line[1];
    if ($ems[$emsVal] == VOID) {
        $ems[$emsVal] = 1;
    } else {
        $ems[$emsVal]++;
    }

    $pxVal = $line[0];
    if ($pxs[$pxVal] == VOID) {
        $pxs[$pxVal] = 1;
    } else {
        $pxs[$pxVal]++;
    }

    $fontVal = $line[2];
    if ($fonts[$fontVal] == VOID) {
        $fonts[$fontVal] = 1;
    } else {
        $fonts[$fontVal]++;
    }


}
fclose($file);

    /* ********************** */
    /*      Ems bar chart      */
    /* ********************** */

    /* Create and populate the pData object */
    $MyData = new pData();
    $MyData->addPoints($ems, "Width in Ems");
    $MyData->setSerieDescription("Width in Ems", "Width in ems");

    /* Draw serie 1 in red with a 70% opacity */

    $serieSettings = array("R" => 255, "G" => 0, "B" => 0, "Alpha" => 70);
    $MyData->setPalette("Width in Ems", $serieSettings);

    $MyData->setAxisName(0, "Hits");


    /* Create the pChart object */
    $myPicture = new pImage(900, 270, $MyData);
    $myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_VERTICAL, array("StartR" => 0, "StartG" => 124, "StartB" => 180, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 100));
    $myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_HORIZONTAL, array("StartR" => 240, "StartG" => 240, "StartB" => 240, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 20));
    $myPicture->setFontProperties(array("FontName" => "C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/fonts/verdana.ttf", "FontSize" => 10));


    /* Draw the scale  */
    $myPicture->setGraphArea(50, 30, 880, 200);
    $myPicture->drawScale(array("CycleBackground" => TRUE, "LabelSkip" => 4, "LabelRotation" => 90, "DrawSubTicks" => TRUE, "GridR" => 0, "GridG" => 0, "GridB" => 0, "GridAlpha" => 10));
    $myPicture->drawText(450, 55, "Viewport Width in Ems", array("FontSize" => 15, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));
    $myPicture->drawText(450, 245, "Ems", array("FontSize" => 10, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));
    /* Turn on shadow computing */
    $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));

    /* Draw the chart */
    $settings = array("Gradient" => FALSE, "DisplayPos" => LABEL_POS_TOP, "DisplayValues" => FALSE, "DisplayR" => 0, "DisplayG" => 0, "DisplayB" => 0, "DisplayShadow" => TRUE,); //array("Surrounding"=>-30,"InnerSurrounding"=>30)
    $myPicture->drawBarChart($settings);

    /* Write the chart legend */
// $myPicture->drawLegend(580, 12, array("Style" => LEGEND_BOX, "Mode" => LEGEND_HORIZONTAL));

    /* Render the picture (choose the best way) */
    $myPicture->autoOutput("ems-bar-chart.png");



    /* ********************** */
    /*      Px bar chart      */
    /* ********************** */
    unset($MyData, $MyPicture, $pImage);

    /* Create and populate the pData object */
    $MyData = new pData();

    /* Add main data */
    $MyData->addPoints($pxs, "Width in CSS Pixels");
    $MyData->setSerieDescription("Width CSS Pixels", "Width in CSS Pixels");

    /* Set bar color */
    $serieSettings = array("R" => 255, "G" => 0, "B" => 0, "Alpha" => 100);
    $MyData->setPalette("Width in CSS Pixels", $serieSettings);

    $MyData->setAxisName(0, "Hits");

    /* Create the pChart object */
    $myPicture = new pImage(900, 270, $MyData);
    $myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_VERTICAL, array("StartR" => 0, "StartG" => 124, "StartB" => 180, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 100));
    $myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_HORIZONTAL, array("StartR" => 240, "StartG" => 240, "StartB" => 240, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 20));
    $myPicture->setFontProperties(array("FontName" => "C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/fonts/verdana.ttf", "FontSize" => 10));


    /* Draw the scale  */
    $myPicture->setGraphArea(50, 30, 880, 200);
    $myPicture->drawScale(array("CycleBackground" => TRUE, 'LabelRotation' => 90, "LabelSkip" => 49, "DrawSubTicks" => FALSE, "AutoAxisLabels" => FALSE, "GridR" => 0, "GridG" => 0, "GridB" => 0, "GridAlpha" => 10));
    $myPicture->drawText(450, 55, "Viewport Width in CSS Pixels", array("FontSize" => 15, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));
    $myPicture->drawText(450, 250, "CSS Pixels", array("FontSize" => 10, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));
    /* Turn on shadow computing */
    $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));

    /* Draw the chart */
    $settings = array("Gradient" => FALSE, "DisplayPos" => LABEL_POS_TOP, "DisplayValues" => FALSE, "DisplayR" => 0, "DisplayG" => 0, "DisplayB" => 0, "DisplayShadow" => FALSE,); //array("Surrounding"=>-30,"InnerSurrounding"=>30)
    $myPicture->drawBarChart($settings);

    /* Write the chart legend */
// $myPicture->drawLegend(580, 12, array("Style" => LEGEND_BOX, "Mode" => LEGEND_HORIZONTAL));

    /* Render the picture (choose the best way) */
    $myPicture->autoOutput("pxs-bar-chart.png");



/* ********************** */
/*      Font-size bar chart      */
/* ********************** */
unset($MyData, $MyPicture, $pImage);

/* Create and populate the pData object */
$MyData = new pData();

/* Force Y axis to start at 0 */

/* Add main data */
$MyData->addPoints($fonts, "Font-size in Pixels");
$MyData->setSerieDescription("Font-size in Pixels", "Width in CSS Pixels");

/* Set bar color */
$serieSettings = array("R" => 255, "G" => 0, "B" => 0, "Alpha" => 70);
$MyData->setPalette("Font-size in Pixels", $serieSettings);

$MyData->setAxisName(0, "Hits");

/* Create the pChart object */
$myPicture = new pImage(900, 270, $MyData);
$myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_VERTICAL, array("StartR" => 0, "StartG" => 124, "StartB" => 180, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 100));
$myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_HORIZONTAL, array("StartR" => 240, "StartG" => 240, "StartB" => 240, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 20));
$myPicture->setFontProperties(array("FontName" => "C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/fonts/verdana.ttf", "FontSize" => 10));


/* Draw the scale  */
$myPicture->setGraphArea(50, 30, 880, 200);
$myPicture->drawScale(array("CycleBackground" => TRUE, 'LabelRotation' => 90,  "AutoAxisLabels" => FALSE, "GridR" => 0, "GridG" => 0, "GridB" => 0, "GridAlpha" => 10));
$myPicture->drawText(450, 55, "Font-size in Pixels", array("FontSize" => 15, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));
$myPicture->drawText(450, 250, "Pixels", array("FontSize" => 10, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));
/* Turn on shadow computing */
$myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));

/* Draw the chart */
$settings = array("Gradient" => FALSE, "DisplayPos" => LABEL_POS_TOP, "DisplayValues" => FALSE, "DisplayR" => 0, "DisplayG" => 0, "DisplayB" => 0, "DisplayShadow" => FALSE,); //array("Surrounding"=>-30,"InnerSurrounding"=>30)
$myPicture->drawBarChart($settings);

/* Write the chart legend */
// $myPicture->drawLegend(580, 12, array("Style" => LEGEND_BOX, "Mode" => LEGEND_HORIZONTAL));

/* Render the picture (choose the best way) */
$myPicture->autoOutput("fonts-bar-chart.png");
