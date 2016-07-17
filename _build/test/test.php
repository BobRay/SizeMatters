<?php

/* pChart library inclusions */
include "C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/class/pData.class.php";
include "C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/class/pDraw.class.php";
include "C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/class/pImage.class.php";
include "C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/model/pChart/class/pPie.class.php";

include 'C:\xampp\htdocs\addons\core\model\modx\modx.class.php';

$modx = new modX();
$modx->initialize('web');
$modx->getService('error', 'error.modError');

function parsePie($text) {
    $pie = array();
    $lines = explode("\n", $text);
    $unit = substr($lines[0], 5);
    $pie['unit'] = trim($unit);
    for ($i = 1; $i < count($lines); $i++) {
        if (empty($lines[$i])) {
            continue;
        }
        $fields = explode(':', $lines[$i]);
        $label = trim($fields[0]);
        $pie[$label] = array(
            'min' => (float) $fields[1],
            'max' => (float) $fields[2],
        );
    }
    return $pie;
}

$chunk =
    'unit:em
Phones:0:29.99
Tablets:30:47.999
Laptops:48:63.99
Desktops:64:9999
Other:10000:99999';


$chunk = $modx->getChunk('SizeMattersPieConfig');

$scriptProperties = array();

// $scriptProperties['pie'] = parsePie($chunk);
$scriptProperties['showPxs'] = true;
$scriptProperties['showEms'] = true;
$scriptProperties['showFonts'] = true;
$scriptProperties['showPie'] = true;

$scriptProperties['refreshPxs'] = true;
$scriptProperties['refreshEms'] = true;
$scriptProperties['refreshFonts'] = true;
$scriptProperties['refreshPie'] = true;


include 'C:\xampp\htdocs\addons\assets\mycomponents\sizematters\core\components\sizematters\model\sizematters\sizemattersdraw.class.php';
$sm = new SizeMattersDraw($modx, $scriptProperties);

$sm->init();
echo $sm->process();



exit();


// $dataFile = 'C:\xampp\htdocs\addons\assets\mycomponents\sizematters\core\components\sizematters\logs\log.txt';
// $file = fopen($dataFile, 'r');



