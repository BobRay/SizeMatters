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

        protected $showEms;
        protected $refreshEms;
        protected $showPxs;
        protected $refreshPxs;
        protected $showFonts;
        protected $refreshFonts;
        protected $emsPictureFile = 'ems-bar-chart.png';
        protected $pxsPictureFile = 'pxs-bar-chart.png';
        protected $fontsPictureFile = 'fonts-bar-chart.png';
        protected $corePath;
        protected $modelPath;
        protected $assetsPath;
        protected $imagePath;
        protected $imageUrl;
        protected $dataDir;
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

            /* These are all base paths - no filename */
            $this->corePath = $this->modx->getOption('sm.core_path', null, MODX_CORE_PATH . 'components/sizematters/');
            $this->modelPath = $this->corePath . '/model/';
            $this->assetsPath = $this->modx->getOption('sm.assets_path', null, MODX_ASSETS_PATH . 'components/sizematters/');
            $this->imagePath = $this->assetsPath . 'images/';
            if (! is_dir($this->imagePath)) {
                die("No Image Dir: " . $this->imagePath);
            }
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

            /*echo "<br>ShowEms: " . $this->showEms;
            echo "<br>ShowPxs: " . $this->showPxs;
            echo "<br>ShowFonts: " . $this->showFonts;

            echo "<br>RefreshEms: " . $this->refreshEms;
            echo "<br>RefreshPxs: " . $this->refreshPxs;
            echo "<br>RefreshFonts: " . $this->refreshFonts . '<br><br>';*/
                        
            if ($this->refreshEms || $this->refreshPxs || $this->refreshFonts) {
                /* read data file and create appropriate arrays */
                require_once $this->modelPath . 'pChart/class/pData.class.php';
                require_once $this->modelPath . 'pChart/class/pDraw.class.php';
                require_once $this->modelPath . 'pChart/class/pImage.class.php';
                $this->createArrays();

                /*echo "<br>***************** EMs<br>" . print_r($this->ems, true);*/
                /* echo "<br>***************** Pxs<br>" . print_r($this->pxs, true); */
                echo "<br>***************** Fonts<br>" . print_r($this->fonts, true);
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

            return $this->output;

        }

        protected function createArrays() {
            define("VOID", 0.123456789);
            $dataFile = $this->dataDir . 'log.txt';
            $file = fopen($dataFile, 'r');
            if (! $file) {
                die('No Data File ' . $dataFile);
            }

            /* Fill arrays with VOID constant (0.123456789) */
            if ($this->refreshEms) {
                $this->ems = array_fill(0, 100, VOID);
            }
            if ($this->refreshPxs) {
                $this->pxs = array_fill(1, 2000, VOID);
            }
            if ($this->refreshFonts) {
                $this->fonts = array_fill(0, 40, VOID);
            }

            /* $line is an array of the csv elements */
            while (($line = fgetcsv($file)) !== FALSE) {
                /* echo "<br>LINE: " . print_r($line, true); */
                /* If final array value is VOID, make it one, else increment it */
                if ($this->refreshEms) {
                    $emsVal = $line[1];
                    if ($this->ems[$emsVal] == VOID) {
                        $this->ems[$emsVal] = 1;
                    } else {
                        $this->ems[$emsVal]++;
                    }
                }

                if ($this->refreshPxs) {
                    $pxVal = $line[0];
                    if ($this->pxs[$pxVal] == VOID) {
                        $this->pxs[$pxVal] = 1;
                    } else {
                        $this->pxs[$pxVal]++;
                    }
                }

                if ($this->refreshFonts) {
                    $fontVal = $line[2];
                    if ($this->fonts[$fontVal] == VOID) {
                        $this->fonts[$fontVal] = 1;
                    } else {
                        $this->fonts[$fontVal]++;
                    }
                }


            }
            fclose($file);

            /*echo "<br>***************** EMs<br>" . print_r($this->ems, true);
            echo "<br>***************** Pxs<br>" . print_r($this->pxs, true);
            echo "<br>***************** Fonts<br>" . print_r($this->fonts, true);*/
        }

        /* Next three methods create the image files */
        public function showEmsChart() {
            if (! $this->showEms) {
                return '';
            }
            if (! $this->refreshEms) {
               /* return Tpl */
            }


            /*     Create Ems bar chart image file     */

            /* Create and populate the pData object */
            $MyData = new pData();
            $MyData->addPoints($this->ems, "Width in Ems");
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
            // $file = $this->imagePath . 'ems-bar-chart.png';
            $url = $this->imageUrl . 'ems-bar-chart.png';
            $myPicture->render($this->imagePath . 'ems-bar-chart.png');
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
            if (!$this->refreshPxs) {
                /* return Tpl */
            }
            /*     Create Px bar chart image file */
            unset($MyData, $MyPicture, $pImage);

            /* Create and populate the pData object */
            $MyData = new pData();

            /* Add main data */
            $MyData->addPoints($this->pxs, "Width in CSS Pixels");
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
            $myPicture->render($this->imagePath . "pxs-bar-chart.png");
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
            if (!$this->refreshFonts) {
                /* return Tpl */
            }
            /*  Create Font-size bar chart image file   */

            unset($MyData, $MyPicture, $pImage);

            /* Create and populate the pData object */
            $MyData = new pData();

            /* Force Y axis to start at 0 */

            /* Add main data */
            $MyData->addPoints($this->fonts, "Font-size in Pixels");
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

            /* Render the picture (choose the best way) */
            $myPicture->render($this->imagePath . 'fonts-bar-chart.png');

            $fields = array(
                'sm.image_url' => $this->imageUrl . 'fonts-bar-chart.png',
                'sm.image_alt' => 'Font-size Chart',
            );
            $inner = $this->modx->getChunk('SizeMattersImageTpl', $fields);

            return $this->modx->getChunk('SizeMattersFontsTpl', array('sm.image' => $inner));


        }
    }
}