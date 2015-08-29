<?php
/**
* @version		2.0
* @package		DJ Classifieds
* @subpackage	DJ Classifieds Search Module
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
$config = JFactory::getConfig();
	/*$menus	= JSite::getMenu();	
	$menu_item = $menus->getItems('link','index.php?option=com_djclassifieds&view=items&cid=0',1);
	$menu_item_blog = $menus->getItems('link','index.php?option=com_djclassifieds&view=items&layout=blog&cid=0',1);
			
	$itemid = ''; 
	if($menu_item){
		$itemid='&Itemid='.$menu_item->id;
	}else if($menu_item_blog){
		$itemid='&Itemid='.$menu_item_blog->id;
	}	*/


	$document= JFactory::getDocument();
	if($config->get('force_ssl',0)==2){
		$document->addScript("https://maps.google.com/maps/api/js?sensor=false");
	}else{
		$document->addScript("http://maps.google.com/maps/api/js?sensor=false");
	}
	$document->addScript(JURI::base().'/components/com_djclassifieds/assets/mapclustering/src/markerclusterer.js');
	
$par = JComponentHelper::getParams( 'com_djclassifieds' );
	$gm_cat_icons=array();
	if($par->get('gm_icon',1)==1){
		if(file_exists(JPATH_BASE.'/images/djcf_gmicon.png')){
			$gm_icon_default = JURI::base().'/images/djcf_gmicon.png';
			$icon_size = getimagesize(JPATH_BASE.'/images/djcf_gmicon.png');
			$gm_icon_default_w = $icon_size[0];
			$gm_icon_default_h = $icon_size[0];
			$gm_icon_default_a = $icon_size[0]/2;  	
		}else{
			$gm_icon_default = JURI::base().'components/com_djclassifieds/assets/images/djcf_gmicon.png';
			$icon_size = getimagesize(JPATH_BASE.'/components/com_djclassifieds/assets/images/djcf_gmicon.png');
			$gm_icon_default_w = $icon_size[0];
			$gm_icon_default_h = $icon_size[0];
			$gm_icon_default_a = $icon_size[0]/2;	
		}		
		

		if ($handle = opendir(JPATH_BASE.'/images/')) {		
		    while (false !== ($icon_f = readdir($handle))) {
		    	if(strstr($icon_f, 'djcf_gmicon')){
		    		$icon_n = (int)str_ireplace('djcf_gmicon_','', $icon_f);
					if($icon_n>0){
						$icon_size = getimagesize(JPATH_BASE.'/images/'.$icon_f);
						$gm_cat_icons[$icon_n] = array();
						$gm_cat_icons[$icon_n]['img'] = JURI::base().'images/'.$icon_f;
						$gm_cat_icons[$icon_n]['width'] = $icon_size[0];
						$gm_cat_icons[$icon_n]['height'] = $icon_size[1];
						
					}
		    	}
		    }		
		    closedir($handle);
		}
    }else{ 
  		$gm_icon_default = '';  	
    }	

?>
<div class="dj_cf_maps">
	<div id="djmod_map_box<?php echo $module->id;?>" style="display:none;">
		 <div id='djmod_map<?php echo $module->id;?>' class="djmod_map" style='width: <?php echo $params->get('map_width');?>; height: <?php echo $params->get('map_height');?>; border: 1px solid #666; '>						  
		 </div>      
	</div>
</div>

<script type="text/javascript">


window.addEvent('domready', function(){
		djmodMapaClusterStart<?php echo $module->id;?>();
});

         var djmod_map<?php echo $module->id;?>;
         var djmod_map_marker<?php echo $module->id;?> = new google.maps.InfoWindow();
         var djmod_geokoder<?php echo $module->id;?> = new google.maps.Geocoder();
		 var djmarkers<?php echo $module->id;?> = new Array();		
		    	
		function djmodMarker(position,txt,icon)
		{			
		    var MarkerOptions =  
		    { 
		        position: position, 
		        icon: icon
		    } 
		    var marker = new google.maps.Marker(MarkerOptions);
		    marker.txt=txt;
		     
		    google.maps.event.addListener(marker,"click",function()
		    {
		        djmod_map_marker<?php echo $module->id;?>.setContent(marker.txt);
		        djmod_map_marker<?php echo $module->id;?>.open(djmod_map<?php echo $module->id;?>,marker);
		    });
		    return marker;
		}   	
		    	
		 function djmodMapaClusterStart<?php echo $module->id;?>()    
		 {   		 

			djmod_geokoder<?php echo $module->id;?>.geocode({address: '<?php echo $params->get('start_address');?>'}, function (results, status)
			{
			    if(status == google.maps.GeocoderStatus.OK)
			    {			    
				 document.getElementById("djmod_map_box<?php echo $module->id;?>").style.display='block';
				    var opcjeMapy = {
				        zoom: <?php echo $params->get('start_zoom');?>,
				  		center: results[0].geometry.location,
				  		mapTypeId: google.maps.MapTypeId.<?php echo $params->get('gm_type','ROADMAP');?>,
				  		navigationControl: <?php echo $params->get('enable_zoom','true');?>,
				  		scrollwheel: <?php echo $params->get('enable_scrolling','true');?>,
				  		styles:[{
					        featureType:"poi",
					        elementType:"labels",
					        stylers:[{
					            visibility:"off"
					        }]
					    }]
				    };
				    djmod_map<?php echo $module->id;?> = new google.maps.Map(document.getElementById("djmod_map<?php echo $module->id;?>"), opcjeMapy); 				    				    
					 var size = new google.maps.Size(32,32);
	                 var start_point = new google.maps.Point(0,0);
	                 var anchor_point = new google.maps.Point(0,16);	            			 
					<?php 
					$ia=0;
					foreach($items as $item){						
						if(!$item->alias){
							$item->alias = DJClassifiedsSEO::getAliasName($item->name);
						}
						if(!$item->c_alias){
							$item->c_alias = DJClassifiedsSEO::getAliasName($item->c_name);					
						}
						if(isset($gm_cat_icons[$item->cat_id])){
							$gm_icon_a = $gm_cat_icons[$item->cat_id]['width']/2; ?>
			                var size = new google.maps.Size(<?php echo $gm_cat_icons[$item->cat_id]['width'].','.$gm_cat_icons[$item->cat_id]['height'];?>);
		             		var start_point = new google.maps.Point(0,0);
		             		var anchor_point = new google.maps.Point(<?php echo $gm_icon_a.','.$gm_cat_icons[$item->cat_id]['height'];?>);
		             		var icon = new google.maps.MarkerImage("<?php echo $gm_cat_icons[$item->cat_id]['img'];?>", size, start_point, anchor_point); 
						<?php }else if($gm_icon_default){?>
							var size = new google.maps.Size(<?php echo $gm_icon_default_w.','.$gm_icon_default_h;?>);
		             		var start_point = new google.maps.Point(0,0);
		             		var anchor_point = new google.maps.Point(<?php echo $gm_icon_default_a.','.$gm_icon_default_h;?>);
		             		var icon = new google.maps.MarkerImage("<?php echo $gm_icon_default;?>", size, start_point, anchor_point);
						<?php }else{ ?>
							var icon = '';
						<?php } ?>																													
							<?php								
				    			$marker_txt = '<div style="width:200px;margin-bottom:0px"><div style="margin-bottom:5px;">';
								$marker_txt .= '<a style="text-decoration:none !important;" href="'.JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias)).' ">';
									if($item->img_path && $item->img_name && $item->img_ext){									
										$item_img = JURI::base().$item->img_path.$item->img_name.'_ths.'.$item->img_ext;									
										$marker_txt .= '<img style="float:left;margin:5px 10px 0 0;"  width="60px" src="'.$item_img.'" /> ';									
									}
				    				$marker_txt .= '<strong>'.addslashes($item->name).'</strong><br />';
									$marker_txt .= '<span style="color:#333333">'.addslashes(str_replace(array("\n","\r","\r\n"), '',$item->intro_desc)).'</span>';																						
								$marker_txt .='</a></div></div>';	
																											
									if($item->latitude!='0.000000000000000' && $item->longitude!='0.000000000000000'){ ?>
										var adLatlng = new google.maps.LatLng(<?php echo $item->latitude.','.$item->longitude; ?>);
										djmarkers<?php echo $module->id;?>.push(djmodMarker(adLatlng,'<?php echo $marker_txt; ?>',icon));
				    				<?php 
									 }?>							
				    	<?php } ?>	
				    	var mcOptions = {gridSize: 50, maxZoom: 14,styles: [{
						height: 53,url: "<?php echo JURI::base()?>components/com_djclassifieds/assets/mapclustering/images/m1.png",width: 53},
						{height: 56,url: "<?php echo JURI::base()?>components/com_djclassifieds/assets/mapclustering/images/m2.png",width: 56},
						{height: 66,url: "<?php echo JURI::base()?>components/com_djclassifieds/assets/mapclustering/images/m3.png",width: 66},
						{height: 78,url: "<?php echo JURI::base()?>components/com_djclassifieds/assets/mapclustering/images/m4.png",width: 78},
						{height: 90,url: "<?php echo JURI::base()?>components/com_djclassifieds/assets/mapclustering/images/m5.png",width: 90}]};
				    	var markerCluster = new MarkerClusterer(djmod_map<?php echo $module->id;?>, djmarkers<?php echo $module->id;?>,mcOptions);																																									    
			    	}
				});					    
		    }  
</script>