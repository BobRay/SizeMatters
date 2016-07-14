<?php
/**
 * SizeMattersShowGraphs snippet for SizeMatters extra
 *
 * Copyright 2016 by Bob Ray <http://bobsguides.com>
 * Created on 06-19-2016
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
 * Displays analyzed content of the the sizematters log file
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package sizematters
 **/

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
            'min' => (float)$fields[1],
            'max' => (float)$fields[2],
        );
    }
    return $pie;
}

$chunk = $modx->getChunk('SizeMattersPieConfig');


$smCorePath = $modx->getOption('sm.core_path', null, MODX_CORE_PATH . 'components/SizeMatters/');
require_once($smCorePath . 'model/sizematters/sizemattersdraw.class.php');

$showPie = $modx->getOption('showPie', $scriptProperties, true, true);
$showPie = true;
if ($showPie) {
    $scriptProperties['pie'] = parsePie($chunk);
}


$smGraph = new SizeMattersDraw($modx, $scriptProperties);

$smGraph->init();
return $smGraph->process();