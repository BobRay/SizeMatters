<?php
/**
 * SizeMattersProcessor snippet for SizeMatters extra
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

/**
 * Description
 * -----------
 * Handles Ajax request from SM JS. Writes data to file
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package sizematters
 **/
//C:/xampp/htdocs/addons/assets/mycomponents/sizematters/core/components/sizematters/components/sizematters/model/sizematters/sizematters.class.php
//C:\xampp\htdocs\addons\assets\mycomponents\sizematters\core\components\sizematters\model\sizematters\sizematters.class.php
/* sm paths; Set the sm. System Settings only for development  in MyComponent */
$smCorePath = $modx->getOption('sm.core_path', null, MODX_CORE_PATH . 'components/SizeMatters/');
require_once($smCorePath . 'model/sizematters/sizematters.class.php');
$smLogPath = $smCorePath . 'logs/';
$smLogFileName = $smLogPath . 'm-' . date("m");

$sm = new SizeMatters($scriptProperties);

$i = json_decode(stripslashes(file_get_contents("php://input")), true);
// $modx->log(modX::LOG_LEVEL_ERROR, 'Before Validation: ' . print_r($i, true));
if ($sm->validate($i)) {
    $v = implode(',', array_values($i)) . "\n";
    $status = $sm->saveData($v, $smLogFileName);
    if ($status !== true) {
        $modx->log(modX::LOG_LEVEL_ERROR, $status);
    }
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, '[SizeMatters] Invalid Data sent to processor');
}


return '';