<?php
/**
 * snippets transport file for SizeMatters extra
 *
 * Copyright 2016 by Bob Ray <http://bobsguides.com>
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
/* @var xPDOObject[] $snippets */


$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'SizeMatters',
  'description' => 'Injects JS to save SizeMatters data',
  'properties' => 
  array (
  ),
), '', true, true);
$snippets[1]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/sizematters.snippet.php'));

$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->fromArray(array (
  'id' => 2,
  'property_preprocess' => false,
  'name' => 'SizeMattersProcessor',
  'description' => 'Handles Ajax request from SM JS. Writes data to file',
  'properties' => 
  array (
  ),
), '', true, true);
$snippets[2]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/sizemattersprocessor.snippet.php'));

$snippets[3] = $modx->newObject('modSnippet');
$snippets[3]->fromArray(array (
  'id' => 3,
  'property_preprocess' => false,
  'name' => 'SizeMattersShowGraphs',
  'description' => 'Displays analyzed content of the the sizematters log file',
), '', true, true);
$snippets[3]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/sizemattersshowgraphs.snippet.php'));


$properties = include $sources['data'].'properties/properties.sizemattersshowgraphs.snippet.php';
$snippets[3]->setProperties($properties);
unset($properties);

return $snippets;
