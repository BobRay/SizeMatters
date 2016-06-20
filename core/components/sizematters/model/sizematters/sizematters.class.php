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
        /** @var $modx modX */
        public $modx;
        /** @var $props array */
        public $props;

        function __construct($config = array()) {
            $this->props =& $config;
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

        public function saveData($data, $path) {
            $retVal = true;
            $fp = fopen($path, 'a');
            if ($fp) {
                fwrite($fp, $data, SM_MAX_LEN);
                fclose($fp);
            } else {
                $retVal = 'Could not open file for append ' . $path;
            }
            return $retVal;
        }


    }
}