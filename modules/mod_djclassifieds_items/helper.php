<?php
/**
* @version 2.0
* @package DJ Classifieds Menu Module
* @subpackage DJ Classifieds Component
* @copyright Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
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

class modDjClassifiedsItems{
	public static function getItems($params){
		
		$date_time 	= JFactory::getDate();
		$date_exp	= $date_time->toSQL();
		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$ord 		= "i.date_start DESC";
	
		if($params->get('items_ord')==1){
			$ord = "i.display DESC"; 
		}else if($params->get('items_ord')==2){
			$ord = "rand()";
		}else if($params->get('items_ord')==3){
			$ord = "i.name";
		}		
		
		$promoted='';
		$prom_list = array();
		if($params->get('only_p_special','0')==1){
			$prom_list[] = " i.promotions LIKE '%p_special%' "; 
		}
		if($params->get('only_p_first','0')==1){
			$prom_list[] = " i.promotions LIKE '%p_first%' "; 
		}
		if($params->get('only_p_bold','0')==1){
			$prom_list[] = " i.promotions LIKE '%p_bold%' "; 
		}
		if($params->get('only_p_border','0')==1){
			$prom_list[] = " i.promotions LIKE '%p_border%' "; 
		}
		if($params->get('only_p_bg','0')==1){
			$prom_list[] = " i.promotions LIKE '%p_bg%' "; 
		}
		
		if(count($prom_list)==1){
			$promoted=' AND '.$prom_list[0].' ';	
		}else if(count($prom_list)>1){
			$promoted=' AND ('.implode(' OR ', $prom_list).') ';
		}
		
		$item_ids = $params->get('items_ids','');
		if($item_ids){
			$item_ids = ' AND i.id IN ('.$item_ids.')';
		}else{
			$item_ids = '';
		}				
		
		$users_ids = $params->get('users_ids','');
		if($users_ids){
			$users_ids = ' AND i.user_id IN ('.$users_ids.')';
		}else{
			$users_ids = '';
		}

		$types_ids = $params->get('type_id','');
		if(is_array($types_ids)){
			$types_ids = ' AND i.type_id IN ('.implode(',', $types_ids).')';
		}else{
			$types_ids = '';
		}		
		
		$cat_ids = $params->get('cat_id','0');
		$cid = JRequest::getInt('cid','0');
		$fallow_cat= '';
		$cat_list= '';				
		
		if($params->get('fallow_category')==1 && JRequest::getVar('option','')=='com_djclassifieds' && $cid>0){		
			$djcfcatlib = new DJClassifiedsCategory();
			$cats= $djcfcatlib->getSubCat($cid,1);				
			$catlist= $cid;			
			foreach($cats as $c){
				$catlist .= ','. $c->id;
			}
			$fallow_cat = ' AND i.cat_id IN ('.$catlist.') ';				
		}else if(is_array($cat_ids)){
			if(count($cat_ids)>1){
				$cat_list = ' AND i.cat_id IN ('.implode(',', $cat_ids).') ';				
			}else if($cat_ids[0]>0){
				$cat_list = ' AND i.cat_id = '.$cat_ids[0].' ';
			}
		}
		


		$reg_ids = $params->get('region_id','0');		
		$fallow_region= '';
		$region_list= '';				
		
		if($params->get('fallow_region','0')==1 && JRequest::getVar('option','')=='com_djclassifieds' && JRequest::getVar('view','')=='item'){			
			$djcfreglib = new DJClassifiedsRegion();
			$id = JRequest::getInt('id','0');
								
				$query = "SELECT i.region_id FROM #__djcf_items i "
						."WHERE i.id=".$id." LIMIT 1";
				$db->setQuery($query);
				$region_id=$db->loadResult();
				
			if($region_id){
				$regs= $djcfreglib->getSubReg($region_id);				
				$reglist= $region_id;			
				foreach($regs as $r){
					$reglist .= ','. $r->id;
				}
				$fallow_region = ' AND i.region_id IN ('.$reglist.') ';	
			}
							
		}
		if(is_array($reg_ids) && $fallow_region==''){
			if(count($reg_ids)>1){
				$region_list = ' AND i.region_id IN ('.implode(',', $reg_ids).') ';				
			}else if($reg_ids[0]>0){
				$region_list = ' AND i.region_id = '.$reg_ids[0].' ';
			}
		}
		
		$only_img='';
		if($params->get('only_with_img','0')==1){
			$only_img = " AND img.name !='' ";
		}
		
		$source = '';
		$fav_lj = '';
		$fav_s = '';
		if($user->id){
			if($params->get('items_source','0')==1){
				$source = ' AND i.user_id='.$user->id.' ';
			}else if($params->get('items_source','0')==2){
				$fav_lj = "LEFT JOIN ( SELECT * FROM #__djcf_favourites WHERE user_id=".$user->id.") f ON i.id=f.item_id ";
				$fav_s = ',f.id as f_id ';
				$source =  " AND f.id IS NOT NULL ";				
			}	
		}
		
		
		
		$query = "SELECT i.*,c.id as c_id, c.name as c_name,c.alias as c_alias,c.icon_url as c_icon_url, r.name as r_name, "
						." img.path as img_path, img.name as img_name, img.ext as img_ext,img.caption as img_caption ".$fav_s
				."FROM #__djcf_categories c, #__djcf_items i "
				."LEFT JOIN #__djcf_regions r ON r.id=i.region_id ".$fav_lj
				."LEFT JOIN ( SELECT img.id, img.item_id, img.name, img.path, img.ext, img.ordering, img.caption 
		 					  FROM (SELECT * FROM #__djcf_images WHERE type='item' ORDER BY ordering) img GROUP BY img.item_id ) AS img ON img.item_id=i.id "
				."WHERE i.date_exp > '".$date_exp."' AND i.published = 1 AND c.published = 1 AND i.cat_id=c.id "
				.$promoted.$item_ids.$users_ids.$fallow_cat.$cat_list.$fallow_region.$region_list.$types_ids.$only_img.$source
				."ORDER BY ".$ord." limit ".$params->get('items_nr');
		$db->setQuery($query);
		$items=$db->loadObjectList();
		//echo '<pre>';print_r($db);die();
		return $items;
	}
	
	static function getCatImages(){
		$db= JFactory::getDBO();
		$query = "SELECT * FROM #__djcf_images WHERE type='category' ORDER BY item_id ";
		$db->setQuery($query);
		$cat_images=$db->loadObjectList('item_id');
		
		//echo '<pre>';print_r($cat_images);die();
		return $cat_images;
	}
	
	static function getTypes(){						
		$db= JFactory::getDBO();
		$query = "SELECT * FROM #__djcf_types WHERE published=1";
		$db->setQuery($query);
		$types=$db->loadObjectList('id');			
			foreach($types as $type){
				$registry = new JRegistry();		
				$registry->loadString($type->params);
				$type->params = $registry->toObject();
			}
		//echo '<pre>';print_r($types);die();		
		return $types;
	}

}
?>
