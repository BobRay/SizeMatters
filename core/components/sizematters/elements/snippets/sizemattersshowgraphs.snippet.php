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

$smCorePath = $modx->getOption('sm.core_path', null, MODX_CORE_PATH . 'components/SizeMatters/');
$smAssetPath = $modx->getOption('sm.assets_url', null, MODX_ASSETS_URL . 'components/SizeMatters/');
require_once($smCorePath . 'model/sizematters/sizemattersdraw.class.php');
$modx->regClientCSS($smAssetPath . 'css/sizematters.css');


$smGraph = new SizeMattersDraw($modx, $scriptProperties);

$smGraph->init();
return $smGraph->process();