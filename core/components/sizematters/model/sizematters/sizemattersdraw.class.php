<?php
/**
 * SizeMatters class file for SizeMatters extra
 *
 * Copyright 2016 by Bob Ray <http://bobsguides.com>
 * Created on 06-15-2016
 *
 * SizeMatters is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * SizeMatters is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * SizeMatters; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package sizematters
 */

define('SM_ARG_COUNT', 3);
define('SM_MAX_LEN', 15);

if (!class_exists('SizeMattersDraw')) {
    class SizeMattersDraw {
        /** @var $modx modX */
        public $modx;
        /** @var $props array */
        public $props;

        protected $ems = array();
        protected $pxs = array();
        protected $fonts = array();
        protected $pies = array();

        protected $showEms;
        protected $refreshEms;
        protected $showPxs;
        protected $refreshPxs;
        protected $showFonts;
        protected $refreshFonts;
        protected $showPie;
        protected $refreshPie;
        protected $pieArray;
        protected $pieValues;
        protected $pieLabels;
        protected $pieUnit;
        protected $emsPictureFile = 'ems-bar-chart.png';
        protected $pxsPictureFile = 'pxs-bar-chart.png';
        protected $fontsPictureFile = 'fonts-bar-chart.png';
        protected $corePath;
        protected $modelPath;
        protected $assetsPath;
        protected $imagePath;
        protected $imageUrl;
        protected $dataDir;
        protected $fontDir;
        protected $output = '';


        function __construct($modx, $config = array()) {
            $this->props = $config;
            $this->modx = $modx;
        }

        function init() {
            $this->showEms = $this->modx->getOption('showEms', $this->props, true, true);
            $this->refreshEms = $this->modx->getOption('refreshEms', $this->props, true, true);
            $this->showPxs = $this->modx->getOption('showPxs', $this->props, true, true);
            $this->refreshPxs = $this->modx->getOption('refreshPxs', $this->props, true, true);
            $this->showFonts = $this->modx->getOption('showFonts', $this->props, true, true);
            $this->refreshFonts = $this->modx->getOption('refreshFonts', $this->props, true, true);
            $this->showPie = $this->modx->getOption('showPie', $this->props, true, true);
            $this->refreshPie = $this->modx->getOption('refreshPie', $this->props, true, true);
            if ($this->refreshPie) {
                $this->pieArray = $this->modx->getOption('pie', $this->props, array(), true);
            }

            /* These are all base paths - no filename */
            $this->corePath = $this->modx->getOption('sm.core_path', null, MODX_CORE_PATH . 'components/sizematters/');
            $this->modelPath = $this->corePath . 'model/';
            $this->assetsPath = $this->modx->getOption('sm.assets_path', null, MODX_ASSETS_PATH . 'components/sizematters/');
            $this->imagePath = $this->assetsPath . 'images/';
            if (! is_dir($this->imagePath)) {
                die("No Image Dir: " . $this->imagePath);
            }
            $this->fontDir = $this->modelPath . 'pChart/fonts/';
            $this->imageUrl = $this->modx->getOption('sm.image_url', null, MODX_ASSETS_URL . 'components/sizematters/images/');
            $this->dataDir = $this->corePath . 'logs/';

            /* If not file exists and (show == true), set refresh true here */
            $path = $this->imagePath . $this->emsPictureFile;
            if (! file_exists($path) && $this->showEms) {
                $this->refreshEms = true;
            }

            $path = $this->imagePath . $this->pxsPictureFile;
            if (!file_exists($path) && $this->showPxs) {
                $this->refreshPxs = true;
            }

            $path = $this->imagePath . $this->fontsPictureFile;
            if (!file_exists($path) && $this->showFonts) {
                $this->refreshFonts = true;
            }

             if ($this->showEms || $this->showPxs || $this->showFonts || $this->showPie) {
                /* read data file and create appropriate arrays */
                require_once $this->modelPath . 'pChart/class/pData.class.php';
                require_once $this->modelPath . 'pChart/class/pDraw.class.php';
                require_once $this->modelPath . 'pChart/class/pImage.class.php';

                if ($this->showPie) {
                    require_once $this->modelPath . 'pChart/class/pPie.class.php';
                }
             }
        }

        public function process() {
            if ($this->showEms) {
                $this->output .= $this->showEmsChart();
            }

            if ($this->showPxs) {
                $this->output .= $this->showPxsChart();
            }

            if ($this->showFonts) {
                $this->output .= $this->showFontsChart();
            }

            if ($this->showPie) {
                $this->output .= $this->showPieChart();
            }

            return $this->output;

        }

        /* Next three methods create the image files */
        public function showEmsChart() {
            if (! $this->showEms) {
                return '';
            }
            $file = $this->dataDir . 'ems.data';

            if (! file_exists($file)) {
                return $this->modx->getChunk('SizeMattersEmsTpl', array('sm.image' => 'Insufficient Data'));
            }

            $emsArray = unserialize(file_get_contents($file));
            foreach ($emsArray as $k => $v) {
                if (empty($v)) {
                    $emsArray[$k] = VOID;
                }
            }

            if ($this->refreshEms) {
                set_time_limit(0);
                /*     Create Ems bar chart image file     */

                /* Create and populate the pData object */
                $MyData = new pData();
                $MyData->addPoints($emsArray, "Width in Ems");
                $MyData->setSerieDescription("Width in Ems", "Width in ems");

                /* Draw serie 1 in red with a 70% opacity */
                $serieSettings = array("R" => 255, "G" => 0, "B" => 0, "Alpha" => 70);
                $MyData->setPalette("Width in Ems", $serieSettings);

                $MyData->setAxisName(0, "Hits");


                /* Create the pChart object */
                $myPicture = new pImage(900, 270, $MyData);
                $myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_VERTICAL, array("StartR" => 0, "StartG" => 124, "StartB" => 180, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 100));
                $myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_HORIZONTAL, array("StartR" => 240, "StartG" => 240, "StartB" => 240, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 20));
                $myPicture->setFontProperties(array("FontName" => $this->fontDir . 'verdana.ttf', 'FontSize' => 10));


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

                /* Render the picture to .png file */

                $myPicture->render($this->imagePath . 'ems-bar-chart.png');

            }
            $fields = array(
                'sm.image_url' => $this->imageUrl . 'ems-bar-chart.png',
                'sm.image_alt' => 'Width in Ems Chart',
            );
            $inner = $this->modx->getChunk('SizeMattersImageTpl', $fields);

            return $this->modx->getChunk('SizeMattersEmsTpl', array('sm.image' => $inner));

        }

        public function showPxsChart() {

            if (!$this->showPxs) {
                return '';
            }

            $file = $this->dataDir . 'pxs.data';

            if (!file_exists($file)) {
                return $this->modx->getChunk('SizeMattersPxsTpl', array('sm.image' => 'Insufficient Data'));

            }

            $pxsArray = unserialize(file_get_contents($file));
            foreach ($pxsArray as $k => $v) {
                if (empty($v)) {
                    $pxsArray[$k] = VOID;
                }
            }
            if ($this->refreshPxs) {
                set_time_limit(0);
                /*     Create Px bar chart image file */
                unset($MyData, $MyPicture, $pImage);

                /* Create and populate the pData object */
                $MyData = new pData();

                /* Add main data */
                $MyData->addPoints($pxsArray, "Width in CSS Pixels");
                $MyData->setSerieDescription("Width CSS Pixels", "Width in CSS Pixels");

                /* Set bar color */
                $serieSettings = array("R" => 255, "G" => 0, "B" => 0, "Alpha" => 100);
                $MyData->setPalette("Width in CSS Pixels", $serieSettings);

                $MyData->setAxisName(0, "Hits");

                /* Create the pChart object */
                $myPicture = new pImage(900, 270, $MyData);
                $myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_VERTICAL, array("StartR" => 0, "StartG" => 124, "StartB" => 180, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 100));
                $myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_HORIZONTAL, array("StartR" => 240, "StartG" => 240, "StartB" => 240, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 20));
                $myPicture->setFontProperties(array("FontName" => $this->fontDir . 'verdana.ttf', 'FontSize' => 10));


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


                /* Render the picture to .png file */
                $myPicture->render($this->imagePath . "pxs-bar-chart.png");
            }

            $fields = array(
                'sm.image_url' => $this->imageUrl . 'pxs-bar-chart.png',
                'sm.image_alt' => 'Width in CSS Pixels Chart',
            );
            $inner = $this->modx->getChunk('SizeMattersImageTpl', $fields);

            return $this->modx->getChunk('SizeMattersPxsTpl', array('sm.image' => $inner));


        }

        public function showFontsChart() {
            if (!$this->showFonts) {
                return '';
            }
            $file = $this->dataDir . 'fonts.data';

            if (!file_exists($file)) {
                return $this->modx->getChunk('SizeMattersFontsTpl', array('sm.image' => 'Insufficient Data'));

            }

            $fontsArray = unserialize(file_get_contents($file));
            foreach ($fontsArray as $k => $v) {
                if (empty($v)) {
                    $fontsArray[$k] = VOID;
                }
            }
            if ($this->refreshFonts) {
                set_time_limit(0);
                /*  Create Font-size bar chart image file   */

                unset($MyData, $MyPicture, $pImage);

                /* Create and populate the pData object */
                $MyData = new pData();

                /* Force Y axis to start at 0 */

                /* Add main data */
                $MyData->addPoints($fontsArray, "Font-size in Pixels");
                $MyData->setSerieDescription("Font-size in Pixels", "Width in CSS Pixels");

                /* Set bar color */
                $serieSettings = array("R" => 255, "G" => 0, "B" => 0, "Alpha" => 70);
                $MyData->setPalette("Font-size in Pixels", $serieSettings);

                $MyData->setAxisName(0, "Hits");

                /* Create the pChart object */
                $myPicture = new pImage(900, 270, $MyData);
                $myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_VERTICAL, array("StartR" => 0, "StartG" => 124, "StartB" => 180, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 100));
                $myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_HORIZONTAL, array("StartR" => 240, "StartG" => 240, "StartB" => 240, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 20));
                $myPicture->setFontProperties(array("FontName" => $this->fontDir . 'verdana.ttf', 'FontSize' => 10));


                /* Draw the scale  */
                $myPicture->setGraphArea(50, 30, 880, 200);
                $myPicture->drawScale(array("CycleBackground" => TRUE, 'LabelRotation' => 90, "AutoAxisLabels" => FALSE, "GridR" => 0, "GridG" => 0, "GridB" => 0, "GridAlpha" => 10));
                $myPicture->drawText(450, 55, "Font-size in Pixels", array("FontSize" => 15, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));
                $myPicture->drawText(450, 250, "Pixels", array("FontSize" => 10, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));
                /* Turn on shadow computing */
                $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));

                /* Draw the chart */
                $settings = array("Gradient" => FALSE, "DisplayPos" => LABEL_POS_TOP, "DisplayValues" => FALSE, "DisplayR" => 0, "DisplayG" => 0, "DisplayB" => 0, "DisplayShadow" => FALSE,); //array("Surrounding"=>-30,"InnerSurrounding"=>30)
                $myPicture->drawBarChart($settings);

                /* Write the chart legend */
// $myPicture->drawLegend(580, 12, array("Style" => LEGEND_BOX, "Mode" => LEGEND_HORIZONTAL));

                /* Render the picture to .png file */
                $myPicture->render($this->imagePath . 'fonts-bar-chart.png');
            }
            $fields = array(
                'sm.image_url' => $this->imageUrl . 'fonts-bar-chart.png',
                'sm.image_alt' => 'Font-size Chart',
            );
            $inner = $this->modx->getChunk('SizeMattersImageTpl', $fields);

            return $this->modx->getChunk('SizeMattersFontsTpl', array('sm.image' => $inner));


        }

        public function showPieChart() {
            if (!$this->showPie) {
                return '';
            }
            $pieConfigChunk = $this->modx->getOption('pieConfigChunk', $this->props, 'SizeMattersPieConfig', true);
            $chunk = $this->modx->getChunk($pieConfigChunk);
            $pieConfig = $this->parsePie($chunk);
            $unit = $pieConfig['unit'];
            $file = $this->dataDir . $unit . 's' . '.data';
            if (! file_exists($file)) {
                return $this->modx->getChunk('SizeMattersPieTpl', array('sm.image' => 'Insufficient Data'));
            }
            array_shift($pieConfig);
            $pieValues = array();
            $pieLabels = array();
            foreach ($pieConfig as $label => $minMax) {
                $pieValues[$label] = 0;
                $pieLabels[] = $label . '('. $minMax['min'] . ' - ' . $minMax['max'] . ')';
            }

            $data = unserialize(file_get_contents($file));
            foreach($data as $key => $value) {
                foreach ($pieConfig as $label => $minMax) {
                    if ($key >= $minMax['min'] && $value <= $minMax['max']) {
                        $pieValues[$label] += $value;
                    }
                }
            }


            if ($this->refreshPie) {
                set_time_limit(0);
                /*  Create Font-size bar chart image file   */

                unset($MyData, $MyPicture, $pImage);

                /* Create and populate the pData object */
                $MyData = new pData();

                $pies = array_values($pieValues);
                /* Add main data */
                $MyData->addPoints($pies, "Visitor Percentages"); //xxx
                $MyData->setSerieDescription("Visitor Percentages", "Visitor Percentages");

                /* Define the abscissa serie */

                // $MyData->addPoints(array(" Phone", " Tablet", " Laptop", " Desktop"), "Labels");
                $MyData->addPoints($pieLabels, "Labels");
                $MyData->setAbscissa("Labels");

                /* Create the pChart object */
                $myPicture = new pImage(900, 270, $MyData);
                $myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_VERTICAL, array("StartR" => 0, "StartG" => 124, "StartB" => 180, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 100));
                $myPicture->drawGradientArea(0, 0, 900, 270, DIRECTION_HORIZONTAL, array("StartR" => 240, "StartG" => 240, "StartB" => 240, "EndR" => 180, "EndG" => 180, "EndB" => 180, "Alpha" => 20));
                $myPicture->setFontProperties(array("FontName" => $this->fontDir . 'verdana.ttf', 'FontSize' => 10));


                /* Draw the scale  */
                $myPicture->setGraphArea(50, 30, 880, 200);

                $myPicture->drawText(450, 55, "Device Percentages " . '(Unit: ' . $unit . ')', array("FontSize" => 15, "
                    Align" => TEXT_ALIGN_BOTTOMMIDDLE));

                /*$myPicture->drawText(450, 55, "Unit: " . $unit, array("FontSize" => 15, "
                    Align" => TEXT_ALIGN_BOTTOMMIDDLE));*/

                /* Turn on shadow computing */
                $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));

                $PieChart = new pPie($myPicture, $MyData);

                /* Define the slice color */

                $PieChart->setSliceColor(0, array("R" => 128, "G" => 21, "B" => 37));
                $PieChart->setSliceColor(1, array("R" => 150, "G" => 121, "B" => 9));
                $PieChart->setSliceColor(2, array("R" => 20, "G" => 72, "B" => 49));
                $PieChart->setSliceColor(3, array("R" => 25, "G" => 43, "B" => 72));
                $PieChart->setSliceColor(4, array("R" => 20, "G" => 0, "B" => 0));
                $PieChart->setSliceColor(5, array("R" => 0, "G" => 20, "B" => 0));
                $PieChart->setSliceColor(6, array("R" => 0, "G" => 0, "B" => 20));


                 /* Draw a splitted pie chart */

                $PieChart->draw3DPie(555, 155, array(
                    "WriteValues" => TRUE, "DataGapAngle" => 10,
                    "DataGapRadius" => 6, "Border" => FALSE, "ValueR" => 250, "ValueG" => 250,
                    "ValueB" => 250, "ValueAlpha" => 100
                ));

                /* Write the chart legend */
                $myPicture->setFontProperties(array("FontName" => $this->fontDir . 'verdana.ttf',
                    "FontSize" => 16, "R" => 100, "G" => 100, "B" => 100));
                $PieChart->drawPieLegend(100, 100, array("Style" => LEGEND_ROUND, "Mode" =>
                    LEGEND_VERTICAL, "BoxSize" => 15, 'Margin' => 10));

                /* Render the picture to .png file */
                $myPicture->render($this->imagePath . 'pie-chart.png');
            }
            $fields = array(
                'sm.image_url' => $this->imageUrl . 'pie-chart.png',
                'sm.image_alt' => 'Pie Chart',
            );
            $inner = $this->modx->getChunk('SizeMattersImageTpl', $fields);
            $retVal = $this->modx->getChunk('SizeMattersPieTpl', array('sm.image' => $inner));
            return $retVal;


        }

        public function parsePie($text) {
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
                    'min' => (float)$fields[1],
                    'max' => (float)$fields[2],
                );
            }
            return $pie;
        }

    }

}