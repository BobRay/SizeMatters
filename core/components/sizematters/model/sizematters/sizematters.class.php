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
            /*$fp = fopen($logFileName, 'w');
            if ($fp) {
                fclose($fp);
            }*/
            /* Clear lock */
            unlink($truncateLockFileName);

        }
        protected function updateDataFiles($logFileName) {
            $path = $this->logDir;

            if (file_exists($path . 'ems.data')) {
                $emsData = unserialize(file_get_contents($path . 'ems.data'));
            } else {
                $emsData = array_fill(0, 100, 0);
            }
            if (file_exists($path . 'pxs.data')) {
                $pxsData = unserialize(file_get_contents($path . 'pxs.data'));
            } else {
                $pxsData = array_fill(0, 2000, 0);
            }
            if (file_exists($path . 'fonts.data')) {
                $fontsData = unserialize(file_get_contents($path . 'fonts.data'));
            } else {
                $fontsData = array_fill(0, 40, 0);
            }


            $fp = fopen($logFileName, 'r');

            if ($fp) {
                while ($line = fgetcsv($fp, 25)) {
                    $px = (int) $line[0];
                    // $px = $px > 1000? 1000 : $px;
                    $pxsData[$px]++;
                    $em = (int) $line[1];
                    $emsData[$em]++;
                    $font = (int) rtrim($line[2]);
                    $fontsData[$font]++;
                }
            }
            fclose($fp);

            file_put_contents($path . 'ems.data', serialize($emsData));
            file_put_contents($path . 'pxs.data', serialize($pxsData));
            file_put_contents($path . 'fonts.data', serialize($fontsData));
        }
    }
}