<?php

/* This is the processor class */
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

if (! class_exists('SizeMatters')) {
    class SizeMatters {
        private $debug = false;
        /** @var $modx modX */
        public $modx;
        /** @var $props array */
        public $props;

        public $logDir;

        public $dataFiles = array();

        function __construct($logDir, $config = array(), $modx = null) {
            $this->props =& $config;
            $this->logDir = $logDir;
            $this->modx = $modx;
        }


        public function validate($data = array()) {
            $valid = true;

            if (count($data) !== SM_ARG_COUNT) {
                $valid =  false;
            } else {
                for ($i = 0; $i < SM_ARG_COUNT; $i++) {
                    if (!is_numeric($data[$i])) {
                        $valid = false;
                    }
                }
            }

            return $valid;

        }

        public function saveData($data, $logFileName, $truncateLockFileName) {
            $success = false;

            for ($tries = 1; $tries <= 3; $tries++) {
                if (file_exists($truncateLockFileName)) {
                    sleep($tries * 2);
                } else {
                    $fp = fopen($logFileName, 'a');
                    if ($fp) {
                        fwrite($fp, $data, SM_MAX_LEN);
                        fclose($fp);
                    }
                    $success = true;
                    break;
                }
            }

            return $success? true : '[SizeMatters] Could not open file for append ' . $logFileName;
        }

        public function truncateLogFile($logFileName, $truncateLockFileName) {
            if ($this->debug) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'In Truncate');
            }
            /* Abort if another process is already truncating */
            if (file_exists($truncateLockFileName)) {
                if ($this->debug) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Locked');
                }
                return;
            }
            /* Abort if file size is too small */
            if (filesize($logFileName) < 1000) {
                if ($this->debug) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Small file');
                }
                return;
            }
            if ($this->debug) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Updating?');
            }
            /* Create lock file to block other processes */
            $xp = fopen($truncateLockFileName, 'w');
            if ($xp) {
                fclose($xp);
            }

            $this->updateDataFiles($logFileName);

             /* Truncate main log file */
            $fp = fopen($logFileName, 'w');
            if ($fp) {
                fclose($fp);
            }
            /* Clear lock */
            unlink($truncateLockFileName);

        }
        /**
         * Removes empty elements from end of array
         * */
        function truncateArray($arr, $index) {
            return array_slice($arr, 0, $index + 2);
        }

        protected function updateDataFiles($logFileName) {
            $path = $this->logDir;
            $emsMax = 0;
            $pxsMax = 0;
            $fontsMax = 0;
            $emsData = array_fill(0, 250, 0);
            $pxsData = array_fill(0, 3500, 0);
            $fontsData = array_fill(0, 40, 0);

            if (file_exists($path . 'ems.data')) {
                $data = unserialize(file_get_contents($path . 'ems.data'));
                $emsData = array_merge($emsData, $data);
            }
            if (file_exists($path . 'pxs.data')) {
                $data = unserialize(file_get_contents($path . 'pxs.data'));
                $pxsData = array_merge($pxsData, $data);
            }

            if (file_exists($path . 'fonts.data')) {
                $data = unserialize(file_get_contents($path . 'fonts.data'));
                $fontsData = array_merge($fontsData, $data);
            }

            unset($data);


            $fp = fopen($logFileName, 'r');

            if ($fp) {
                while ($line = fgetcsv($fp, 25)) {

                    $px = (int) $line[0];
                    // $px = $px > 1000? 1000 : $px;
                    $pxsMax = $px > $pxsMax ? $px : $pxsMax;
                    $pxsData[$px]++;

                    $em = (int) $line[1];
                    $emsMax = $em > $emsMax ? $em : $emsMax;
                    $emsData[$em]++;

                    $font = (int) rtrim($line[2]);
                    $fontsMax = $font > $fontsMax ? $font : $fontsMax;
                    $fontsData[$font]++;
                }
            }
            fclose($fp);

            file_put_contents($path . 'ems.data', serialize($this->truncateArray($emsData, $emsMax + 4)));
            file_put_contents($path . 'pxs.data', serialize($this->truncateArray($pxsData, $pxsMax + 48)));
            file_put_contents($path . 'fonts.data', serialize($this->truncateArray($fontsData, $fontsMax)));
        }
    }
}