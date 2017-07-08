<?php
/**
 * chunks transport file for SizeMatters extra
 *
 * Copyright 2016-2017 Bob Ray <https://bobsguides.com>
 * Created on 06-15-2016
 *
 * @package sizematters
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $chunks */


$chunks = array();

$chunks[1] = $modx->newObject('modChunk');
$chunks[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'SizeMattersPieConfig',
  'description' => 'Configures pie chart. Unit is required, then an arbitrary number of lines in the form: Label:min:max.',
  'properties' => 
  array (
  ),
), '', true, true);
$chunks[1]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/sizematterspieconfig.chunk.html'));

$chunks[2] = $modx->newObject('modChunk');
$chunks[2]->fromArray(array (
  'id' => 2,
  'property_preprocess' => false,
  'name' => 'SizeMattersPieConfig2',
  'description' => 'Configures pie chart. Unit is required, then an arbitrary number of lines in the form: Label:min:max.',
  'properties' => 
  array (
  ),
), '', true, true);
$chunks[2]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/sizematterspieconfig2.chunk.html'));

$chunks[3] = $modx->newObject('modChunk');
$chunks[3]->fromArray(array (
  'id' => 3,
  'property_preprocess' => false,
  'name' => 'SizeMattersFormTpl',
  'description' => 'Tpl for SizeMatters ShowGraphs page',
  'properties' => NULL,
), '', true, true);
$chunks[3]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/sizemattersformtpl.chunk.html'));

$chunks[4] = $modx->newObject('modChunk');
$chunks[4]->fromArray(array (
  'id' => 4,
  'property_preprocess' => false,
  'name' => 'SizeMattersImageTpl',
  'description' => 'Tpl for a single image on the SizeMatters ShowGraphs page',
  'properties' => 
  array (
  ),
), '', true, true);
$chunks[4]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/sizemattersimagetpl.chunk.html'));

$chunks[5] = $modx->newObject('modChunk');
$chunks[5]->fromArray(array (
  'id' => 5,
  'property_preprocess' => false,
  'name' => 'SizeMattersEmsTpl',
  'description' => 'Tpl for Ems graph on SizeMatters ShowGraphs page',
  'properties' => 
  array (
  ),
), '', true, true);
$chunks[5]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/sizemattersemstpl.chunk.html'));

$chunks[6] = $modx->newObject('modChunk');
$chunks[6]->fromArray(array (
  'id' => 6,
  'property_preprocess' => false,
  'name' => 'SizeMattersPxsTpl',
  'description' => 'Tpl for Pxs graph on SizeMatters ShowGraphs page',
  'properties' => 
  array (
  ),
), '', true, true);
$chunks[6]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/sizematterspxstpl.chunk.html'));

$chunks[7] = $modx->newObject('modChunk');
$chunks[7]->fromArray(array (
  'id' => 7,
  'property_preprocess' => false,
  'name' => 'SizeMattersFontsTpl',
  'description' => 'Tpl for Fonts graph on SizeMatters ShowGraphs page',
  'properties' => 
  array (
  ),
), '', true, true);
$chunks[7]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/sizemattersfontstpl.chunk.html'));

$chunks[8] = $modx->newObject('modChunk');
$chunks[8]->fromArray(array (
  'id' => 8,
  'property_preprocess' => false,
  'name' => 'SizeMattersPieTpl',
  'description' => 'Tpl for Fonts graph on SizeMatters ShowGraphs page',
  'properties' => 
  array (
  ),
), '', true, true);
$chunks[8]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/sizematterspietpl.chunk.html'));

$chunks[9] = $modx->newObject('modChunk');
$chunks[9]->fromArray(array (
  'id' => 9,
  'property_preprocess' => false,
  'name' => 'SizeMattersJS',
  'description' => 'JavaScript injected by SizeMatters snippet',
  'properties' => 
  array (
  ),
), '', true, true);
$chunks[9]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/sizemattersjs.chunk.html'));

return $chunks;
