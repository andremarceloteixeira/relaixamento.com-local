<?php
/**
* @version		2.0
* @package		DJ Classifieds
* @subpackage	DJ Classifieds Component
* @copyright	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license		http://www.gnu.org/licenses GNU/GPL
* @autor url    http://design-joomla.eu
* @autor email  contact@design-joomla.eu
* @Developer    Lukasz Ciastek - lukasz.ciastek@design-joomla.eu
* 
* 
* DJ Classifieds is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* DJ Classifieds is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with DJ Classifieds. If not, see <http://www.gnu.org/licenses/>.
* 
*/
defined ('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

class DjclassifiedsModelUserItems extends JModelLegacy{	
	
	
	function getItems(){
		$par 		= JComponentHelper::getParams( 'com_djclassifieds' );
		$limit		= JRequest::getVar('limit', $par->get('limit_djitem_show'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		$user 		= JFactory::getUser();
		$db			= JFactory::getDBO();
			 
			 $order = JRequest::getCmd('order', $par->get('items_ordering','date_e'));
			 $ord_t = JRequest::getCmd('ord_t', $par->get('items_ordering_dir','desc'));
			
			$ord="i.date_exp ";
			
			if($order=="title"){
				$ord="i.name ";
			}elseif($order=="cat"){
				$ord="c.name ";
			}elseif($order=="loc"){
				$ord="r.name ";
			}elseif($order=="price"){
				$ord="i.price ";
			}elseif($order=="display"){
				$ord="i.display ";
			}elseif($order=="date_a"){
				$ord="i.date_start ";
			}elseif($order=="date_e"){
				$ord="i.date_exp ";
			}elseif($order=="published"){
				$ord="i.published ";
			}		
		
			if($ord_t == 'desc'){
				$ord .= 'DESC';
			}else{
				$ord .= 'ASC';
			}
			
			if($order=="active"){
				if($ord_t == 'desc'){
					$ord="i.published DESC, s_active DESC";					
				}else{
					$ord="i.published ASC, s_active ASC";
				}				
			}
			
			$query = "SELECT i.*,c.id as c_id, c.name AS c_name, c.alias AS c_alias,r.id as r_id, r.name as r_name, i.date_start <= NOW() AND i.date_exp >= NOW() AS s_active FROM #__djcf_items i "
					."LEFT JOIN #__djcf_categories c ON i.cat_id = c.id "
					."LEFT JOIN #__djcf_regions r ON i.region_id = r.id "
					."WHERE i.user_id='".$user->id."' "
					."ORDER BY  ".$ord."";
		
			$items = $this->_getList($query, $limitstart, $limit);	
			
			if(count($items)){
				$id_list= '';
				foreach($items as $item){
					if($id_list){
						$id_list .= ','.$item->id;
					}else{
						$id_list .= $item->id;
					}
				}												
			
				$query = "SELECT img.* FROM #__djcf_images img "
						."WHERE img.item_id IN (".$id_list.") AND img.type='item' "
								."ORDER BY img.item_id, img.ordering";
				$db->setQuery($query);
				$items_img=$db->loadObjectList();
			
				for($i=0;$i<count($items);$i++){										
					$img_found =0;
					$items[$i]->images = array();
					foreach($items_img as $img){
						if($items[$i]->id==$img->item_id){
							$img_found =1;
							$img->thumb_s = $img->path.$img->name.'_ths.'.$img->ext;
							$img->thumb_m = $img->path.$img->name.'_thm.'.$img->ext;
							$img->thumb_b = $img->path.$img->name.'_thb.'.$img->ext;
							$items[$i]->images[]=$img;
						}else if($img_found){
							break;
						}
					}
				}
			}
						
				//$db= JFactory::getDBO();$db->setQuery($query);$items=$db->loadObjectList();
				//echo '<pre>';print_r($db);print_r($items);echo '<pre>';die();	
			return $items;
	}
	
	function getCountItems(){
					
			$user = JFactory::getUser();
			$query = "SELECT count(i.id)FROM #__djcf_items i "
					."WHERE i.user_id='".$user->id."' ";				
						
				$db= JFactory::getDBO();
				$db->setQuery($query);
				$items_count=$db->loadResult();
				
				//echo '<pre>';print_r($db);print_r($items_count);echo '<pre>';die();	
			return $items_count;
	}	
	
	
	
}

