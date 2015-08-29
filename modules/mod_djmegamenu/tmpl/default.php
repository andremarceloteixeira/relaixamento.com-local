<?php
/**
 * @version $Id: default.php 25 2014-08-21 13:09:46Z szymon $
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

defined('_JEXEC') or die;
//echo "<pre>".print_r($list,true)."</pre>";
// Note. It is important to remove spaces between elements.
?>
<ul id="dj-megamenu<?php echo $module->id; ?>" class="dj-megamenu <?php echo $class_sfx; ?>">
<?php
$first = true;
foreach ($list as $i => &$item) :
	$class = '';
	$aclass = '';

	if($item->level == $startLevel) {
		$class .= 'dj-up';
		$aclass.= 'dj-up_a ';
	}
	$class .= ' itemid'.$item->id;
	if($first) {
		$class .= ' first';
		$first = false;
	} else if($item->level > $startLevel+1 && $params->get('expand',0)) {
		// don't break into column in expanded submenu tree
	} else if($item->level > $startLevel && $item->params->get('djmegamenu-column_break',0)) { // start new column if break point is set
		echo '</ul></div><div class="dj-subcol" style="width:'.(int)$item->params->get('djmegamenu-column_width',$params->get('column_width')).'px"><ul class="dj-submenu">';
		$class .= ' first';
	}
	if ($item->id == $active_id) {
		$class .= ' current';
	}
	if (in_array($item->id, $path)) {
		$class .= ' active';
		$aclass .= ($item->level > $startLevel && $item->parent ? '-active active' : 'active');
	}
	elseif ($item->type == 'alias') {
		$aliasToId = $item->params->get('aliasoptions');
		if (in_array($aliasToId, $path)) {
			$class .= ' active';
			$aclass .= ($item->level > $startLevel && $item->parent ? '-active active' : 'active');
		}
	}

	if ($item->deeper) {
		//$class .= ' deeper';
	}

	if ($item->parent && (!$endLevel || $item->level < $endLevel)) {
		$class .= ' parent';
		if($item->level > $startLevel) {
			$aclass = 'dj-more'.$aclass;
		}
	}
	
	if($item->type=='separator') {
		$class .= ' separator';
	}

	if(isset($item->modules)){
		$class .= ' withmodule';
	}
	
	if (!empty($class)) {
		$class = ' class="'.trim($class) .'"';
	}
	
	echo '<li'.$class.'>';
	
	if(!isset($item->modules) || $item->params->get('djmegamenu-module_show_link',0)) {
		// Render the menu item.
		require JModuleHelper::getLayoutPath('mod_djmegamenu', 'default_url');
	}
	if(isset($item->modules)) {
		echo '<div class="modules-wrap">'.$item->modules.'</div>';
	}
	//echo $item->level;
	// The next item is deeper.
	if ($item->deeper) {
		if($item->level > $startLevel && $params->get('expand',0)) {
			echo '<ul class="dj-subtree">';
		} else {
			echo '<div class="dj-subwrap '.($subcols[$item->id] > 1 ? 'multiple_cols':'single_column').' subcols'.$subcols[$item->id].'"><div class="dj-subwrap-in" style="width:'.$subwidth[$item->id].'px">';
			echo '<div class="dj-subcol" style="width:'.(int)$item->params->get('djmegamenu-first_column_width',$params->get('column_width')).'px"><ul class="dj-submenu">';
		}
		$first = true;
	}
	// The next item is shallower.
	elseif ($item->shallower) {
		echo '</li>';		
		if($item->level > $startLevel+1 && $params->get('expand',0)) {
			if($item->level - $item->level_diff > $startLevel) {
				echo str_repeat('</ul></li>', $item->level_diff);
			} else {
				echo str_repeat('</ul></li>', $item->level_diff - 1);
				echo str_repeat('</ul></div><div style="clear:both;height:0"></div></div></div></li>', 1);
			}
			
		} else {
			echo str_repeat('</ul></div><div style="clear:both;height:0"></div></div></div></li>', $item->level_diff);
		}
	}
	// The next item is on the same level.
	else {
		echo '</li>';
	}
endforeach;
?></ul>
