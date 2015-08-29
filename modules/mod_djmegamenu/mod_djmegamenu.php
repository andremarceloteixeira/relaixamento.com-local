<?php 
/**
 * @version $Id: mod_djmegamenu.php 25 2014-08-21 13:09:46Z szymon $
 * @package DJ-MegaMenu
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 * DJ-MegaMenu is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-MegaMenu is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-MegaMenu. If not, see <http://www.gnu.org/licenses/>.
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// Include the syndicate functions only once
require_once (dirname(__FILE__) . DS . 'helper.php');

$params->def('menutype', $params->get('menu','mainmenu'));
$params->def('startLevel', 1);
$params->def('endLevel', 0);
$params->def('showAllChildren', 1);
$params->set('column_width', (int)$params->get('column_width',200));
$startLevel = $params->get('startLevel');
$endLevel = $params->get('endLevel');

$list		= modDJMegaMenuHelper::getList($params);
$subwidth	= modDJMegaMenuHelper::getSubWidth($params);
$subcols	= modDJMegaMenuHelper::getSubCols($params);
$active		= modDJMegaMenuHelper::getActive($params);
$active_id 	= $active->id;
$path		= $active->tree;

$showAll	= $params->get('showAllChildren');
$class_sfx	= ($params->get('hasSubtitles') ? 'hasSubtitles ':'') . htmlspecialchars($params->get('class_sfx'));

if(!count($list)) return;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$direction = $doc->direction;

$version = new JVersion;
$jquery = version_compare($version->getShortVersion(), '3.0.0', '>=');

if ($jquery) {
	JHTML::_('jquery.framework');
} else {
	JHTML::_('behavior.framework');
}

// direction integration with joomla monster templates
if ($app->input->get('direction') == 'rtl'){
	$direction = 'rtl';
} else if ($app->input->get('direction') == 'ltr') {
	$direction = 'ltr';
} else {
	if (isset($_COOKIE['jmfdirection'])) {
		$direction = $_COOKIE['jmfdirection'];
	} else {
		$direction = $app->input->get('jmfdirection', $direction);
	}
}

if($params->get('select',0)) {
	$doc->addScript(JURI::root(true).'/modules/mod_djmegamenu/assets/js/'.($jquery ? 'jquery.':'').'djselect.js');
	if ($jquery) {
		$doc->addScriptDeclaration("jQuery(document).ready(function(){jQuery('#dj-megamenu$module->id').addClass('allowHide')});");
	} else {
		$doc->addScriptDeclaration("window.addEvent('domready',function(){document.id('dj-megamenu$module->id').addClass('allowHide')});");
	}
	
	$doc->addStyleDeclaration("
		#dj-megamenu$module->id"."select {display: none;margin:10px;padding:5px;font-size:1.5em;max-width:95%;height:auto;}
		@media (max-width: ".$params->get('width',979)."px) {
  			#dj-megamenu$module->id.allowHide, #dj-megamenu$module->id"."sticky, #dj-megamenu$module->id"."placeholder { display: none; }
  			#dj-megamenu$module->id"."select { display: inline-block; }
		}
	");
}

if($params->get('theme')!='_override') {
	$css = 'modules/mod_djmegamenu/themes/'.$params->get('theme','default').'/css/djmegamenu.css';
} else {
	$css = 'templates/'.$app->getTemplate().'/css/djmegamenu.css';
}

if($direction == 'rtl') { // load rtl css if exists in theme or joomla template
	$css_rtl = JFile::stripExt($css).'_rtl.css';
	if(JFile::exists($css_rtl)) $css = $css_rtl;
}

$doc->addStyleSheet(JURI::root(true).'/'.$css);

if($params->get('moo',1)) {	
	
	$doc->addStyleSheet(JURI::root(true).'/modules/mod_djmegamenu/assets/css/animations.css');
	$doc->addStyleSheet(JURI::root(true).'/media/djextensions/css/animate.min.css');
	$doc->addStyleSheet(JURI::root(true).'/media/djextensions/css/animate.ext.css');
	$doc->addScript(JURI::root(true).'/modules/mod_djmegamenu/assets/js/'.($jquery ? 'jquery.':'').'djmegamenu.js');
	
	if(!is_numeric($delay = $params->get('delay'))) $delay = 500;
	$wrapper_id = $params->get('wrapper');
	$animIn = $params->get('animation_in');
	$animOut = $params->get('animation_out');
	$animSpeed = $params->get('animation_speed');
	$open_event = $params->get('event','mouseenter');
	$fixed = $params->get('fixed',0);
	$fixed_offset = $params->get('fixed_offset',0);
	
	$options = "{wrap: '$wrapper_id', animIn: '$animIn', animOut: '$animOut', animSpeed: '$animSpeed', delay: $delay, 
		event: '$open_event', fixed: $fixed, offset: $fixed_offset }";
	
	$js = $jquery ?	"jQuery(document).ready( function(){ new DJMegaMenu(jQuery('#dj-megamenu$module->id'), $options); } );"
				:	"window.addEvent('domready',function(){ new DJMegaMenu(document.id('dj-megamenu$module->id'), $options); });";
	
	$doc->addScriptDeclaration($js);
	
}

require(JModuleHelper::getLayoutPath('mod_djmegamenu', $params->get('layout', 'default')));

?>