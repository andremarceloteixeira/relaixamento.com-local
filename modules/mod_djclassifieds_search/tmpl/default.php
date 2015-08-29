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
JHTML::_('behavior.calendar');
	$app = JFactory::getApplication();
	$config = JFactory::getConfig();
	$menus	= $app->getMenu('site');
	$menu_item = $menus->getItems('link','index.php?option=com_djclassifieds&view=items',1);
			
	$menu_item = $menus->getItems('link','index.php?option=com_djclassifieds&view=items&cid=0',1);
	$menu_item_blog = $menus->getItems('link','index.php?option=com_djclassifieds&view=items&layout=blog&cid=0',1);
	
	$menu_custom = '';
	if($params->get('results_itemid',0)){
		$menu_custom = $menus->getItem($params->get('results_itemid',0));		
	}	
			
	$itemid = ''; 
	$itemid_url = '';
	$link_reset='index.php?option=com_djclassifieds&view=items&cid=0';
	
	if($params->get('result_view','0')==1 && $menu_item_blog){
		$itemid=$menu_item_blog->id;
		$link_reset='index.php?option=com_djclassifieds&view=items&layout=blog&cid=0&Itemid='.$itemid;
		$itemid_url = $menu_item_blog->link;
	}
	if($menu_custom){
		$itemid=$menu_custom->id;
		$link_reset=$menu_custom->link;
		$itemid_url = $menu_custom->link;
	}
		
	if(!$itemid){
		if($menu_item){
			$itemid=$menu_item->id;
			$link_reset .= '&Itemid='.$itemid;
			$itemid_url = $menu_item->link;
		}else if($menu_item_blog){
			$itemid=$menu_item_blog->id;
			$link_reset='index.php?option=com_djclassifieds&view=items&layout=blog&cid=0&Itemid='.$itemid;
			$itemid_url = $menu_item_blog->link;
		}
	}	

	
	$link_reset .= '&reset=1';
	
	$cid=0;	
	if($params->get('fallow_cat','1')==1 && JRequest::getVar('option') == 'com_djclassifieds'){
		$cid = JRequest::getInt('cid','0');
	}	
	$layout_cl = '';
	if($params->get('search_layout',0)){
		$layout_cl = ' dj_cf_search_horizontal';
	}
?>
<div id="mod_djcf_search<?php echo $module->id;?>" class="dj_cf_search<?php echo $layout_cl;?>">
<form action="<?php echo JRoute::_($itemid_url.'&Itemid='.$itemid.'&se=1');?>" method="get" name="" id="form-search">
	<?php if($config->get('sef')!=1 || !$itemid){ ?>
		<input type="hidden" name="option" value="com_djclassifieds" />
	   	<input type="hidden" name="view" value="items" />
	    <input type="hidden" name="Itemid" value="<?php echo $itemid;?>" /> 
   	<?php } ?>
   	<input type="hidden" name="se" value="1" />	   
   	<?php
   	if($params->get('result_view','0')==1){
   		echo '<input type="hidden" name="layout" value="blog" />';
   	}   	
   	
   	if($params->get('show_input','1')==1){   	
	   	 $s_value = JRequest::getVar('search',JText::_('COM_DJCLASSIFIEDS_SEARCH'));
	   	// onblur="if(this.value=='') this.value='<?php echo JText::_('COM_DJCLASSIFIEDS_SEARCH'); ? >';"
	   	?>
		<input type="text" size="12" name="search" class="inputbox first_input" value="<?php echo $s_value; ?>" onfocus="if(this.value=='<?php echo JText::_('COM_DJCLASSIFIEDS_SEARCH'); ?>') this.value='';" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_DJCLASSIFIEDS_SEARCH'); ?>';" />
<?php }
if($params->get('show_postcode','0')==1 && $params->get('radius_list','')){		
		$s_postcode_value = JRequest::getVar('se_postcode',JText::_('COM_DJCLASSIFIEDS_SEARCH_POSTCODE')); 
		$radius_l = explode(',', $params->get('radius_list',''));
		$radius_list = array();
		$radius_unit = $params->get('radius_unit','km');
		foreach($radius_l as $radius){
			if($radius_unit=='mile'){
				$radius_label = $radius.' '.JText::_('COM_DJCLASSIFIEDS_SEARCH_MILES');
			}else{
				$radius_label = $radius.' '.JText::_('COM_DJCLASSIFIEDS_SEARCH_KM');
			}
			$radius_list[] = array('value'=>$radius,'text'=>$radius_label,'disabled'=>0);
		}?>
		<div class="search_radius">
			<input type="text" size="12" name="se_postcode" class="inputbox" value="<?php echo $s_postcode_value; ?>" onfocus="if(this.value=='<?php echo JText::_('COM_DJCLASSIFIEDS_SEARCH_POSTCODE'); ?>') this.value='';" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_DJCLASSIFIEDS_SEARCH_POSTCODE'); ?>';" />
			<input type="hidden" name="se_postcode_c"  value="<?php echo $params->get('postcode_country',''); ?>"  />
			<input type="hidden" name="se_radius_unit"  value="<?php echo $radius_unit; ?>"  />
			<select autocomplete="off" name="se_radius" class="inputbox" >
				<option value=""><?php echo JText::_('COM_DJCLASSIFIEDS_SEARCH_SEARCH_RANGE');?></option>
				<?php echo JHtml::_('select.options', $radius_list, 'value', 'text', JRequest::getFloat('se_radius',$params->get('default_radius','50')), true);?>
			</select>
		</div>
<?php }
 if($params->get('show_loc','1')==1){	?>
<div class="search_regions">	
	<?php 
	
	$reg_sel = '<select autocomplete="off" class="inputbox" id="se'.$module->id.'_reg_0" name="se_regs[]" onchange="se'.$module->id.'_new_reg(0,this.value);"><option value="0">'.JText::_('COM_DJCLASSIFIEDS_SELECT_LOCALIZATION').'</option>';
				$parent_id=0;	
				$lc=0;
				$lcount = count($regions);
				
				foreach($regions as $l){
					$lc++;
					if($parent_id!=$l->parent_id){
						$reg_sel .= '</select>';
						echo $reg_sel;
						break;
					}	
					$reg_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';
					
					if($parent_id==$l->parent_id && $lc==$lcount){
						$reg_sel .= '</select>';
						echo $reg_sel;
						break;
					}
				}
				
				?>

				<div id="se<?php echo $module->id;?>_after_reg_0"></div>
				<script type="text/javascript">
					var se<?php echo $module->id;?>_regs=new Array();
					
				<?php
				$reg_sel = '<select class="inputbox" name="se_regs[]" id="se'.$module->id.'_reg_0" onchange="se'.$module->id.'_new_reg(0,this.value);">';
				$parent_id=0;	
				
				foreach($regions as $l){
					if($parent_id!=$l->parent_id){
						$reg_sel .= '</select>';
						echo "se".$module->id."_regs[$parent_id]='$reg_sel<div id=\"se".$module->id."_after_reg_$parent_id\"></div>';";
						$parent_id=$l->parent_id;
						$reg_sel = '<select class="inputbox" name="se_regs[]" id="se'.$module->id.'_reg_'.$l->parent_id.'" onchange="se'.$module->id.'_new_reg('.$parent_id.',this.value);">';
						$reg_sel .= '<option value="'.$parent_id.'">'.JTEXT::_('MOD_DJCLASSIFIEDS_SEARCH_LOCATION_SELECTOR_EMPTY_VALUE').'</option>';		
					}	
					$reg_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';
				}
				$reg_sel .= '</select>';	
				echo "se".$module->id."_regs[$parent_id]='$reg_sel<div id=\"se".$module->id."_after_reg_$parent_id\"></div>';";
				
				
					/*$se_url = '';
					foreach ($_GET as $k => $v) {						
						if(strstr($k,'se_')){
							$se_url .= '&'.$k.'='.$v;		
						}
					}*/
				?>	
				var se<?php echo $module->id;?>_current=0;
				
				function se<?php echo $module->id;?>_new_reg(parent,a_parent){
					if(se<?php echo $module->id;?>_regs[a_parent]){
						//alert(cats[v]);
						if(parent==a_parent){
							document.id('se<?php echo $module->id;?>_after_reg_'+parent).innerHTML = '';
						}else{
							document.id('se<?php echo $module->id;?>_after_reg_'+parent).innerHTML = se<?php echo $module->id;?>_regs[a_parent];
						}													 
						document.id('se<?php echo $module->id;?>_reg_'+parent).value=a_parent;
					}else{
						document.id('se<?php echo $module->id;?>_after_reg_'+parent).innerHTML = '';
						document.id('se<?php echo $module->id;?>_reg_'+parent).value=a_parent;		
					}
					
				}				
				</script>

		</div>	
	<?php }
	
	if($params->get('show_cat','1')==1 && count($categories)){	?>
	<div class="search_cats">
	<?php 
	
			$cat_sel = '<select autocomplete="off" class="inputbox" id="se'.$module->id.'_cat_0" name="se_cats[]" onchange="se'.$module->id.'_new_cat(0,this.value);se'.$module->id.'_getFields(this.value);"><option value="">'.JText::_('COM_DJCLASSIFIEDS_PLEASE_SELECT_CATEGORY').'</option>';
				/*$parent_id=0;	
				$lc=0;
				$lcount = count($list);
				
				foreach($list as $l){
					$lc++;
					if($parent_id!=$l->parent_id){
						$cat_sel .= '</select>';
						echo $cat_sel;
						break;
					}	
					$cat_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';
					
					if($parent_id==$l->parent_id && $lc==$lcount){
						$cat_sel .= '</select>';
						echo $cat_sel;
						break;
					}
				}*/
				foreach($categories[0] as $cat){
					$cat_sel .= '<option value="'.$cat->id.'">'.str_ireplace("'", "&apos;", $cat->name).'</option>';
				}	
				$cat_sel .= '</select>';
				echo $cat_sel;
				
				?>

				<div id="se<?php echo $module->id;?>_after_cat_0"></div>
				<script type="text/javascript">
					var se<?php echo $module->id;?>_cats=new Array();
					
				<?php
				//$cat_sel = '<select class="inputbox" name="se_cats[]" id="se_cat_0" onchange="se_new_cat(0,this.value);se_getFields(this.value);">';
				/*$parent_id=0;	
				
				foreach($list as $l){
					if($parent_id!=$l->parent_id){
						$cat_sel .= '</select>';
						echo "se_cats[$parent_id]='$cat_sel<div id=\"se_after_cat_$parent_id\"></div>';";
						$parent_id=$l->parent_id;
						$cat_sel = '<select class="inputbox select_mod" name="se_cats[]" id="se_cat_'.$l->parent_id.'" onchange="se_new_cat('.$parent_id.',this.value);se_getFields(this.value);">';
						$cat_sel .= '<option value="p'.$parent_id.'">'.JTEXT::_('MOD_DJCLASSIFIEDS_SEARCH_CATEGORY_SELECTOR_EMPTY_VALUE').'</option>';		
					}	
					$cat_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';
				}
				$cat_sel .= '</select>';	
				echo "se_cats[$parent_id]='$cat_sel<div id=\"se_after_cat_$parent_id\"></div>';";*/
				$cat_sel = '';
				foreach($categories as $cpar_id => $cat_group){
					$cat_sel = '<select class="inputbox" name="se_cats[]" id="se'.$module->id.'_cat_'.$cpar_id.'" onchange="se'.$module->id.'_new_cat('.$cpar_id.',this.value);se'.$module->id.'_getFields(this.value);">';
					$cat_sel .= '<option value="p'.$cpar_id.'">'.JTEXT::_('MOD_DJCLASSIFIEDS_SEARCH_CATEGORY_SELECTOR_EMPTY_VALUE').'</option>';																
					foreach($cat_group as $cat){
						$cat_sel .= '<option value="'.$cat->id.'">'.str_ireplace("'", "&apos;", $cat->name).'</option>';	
					}	
					$cat_sel .= '</select>';
					echo "se".$module->id."_cats[$cpar_id]='$cat_sel<div id=\"se".$module->id."_after_cat_$cpar_id\"></div>';";
				}
				
				
					/*$se_url = '';
					foreach ($_GET as $k => $v) {						
						if(strstr($k,'se_')){
							$se_url .= '&'.$k.'='.$v;		
						}
					}*/
				?>	
				var se_current=0;
				
				function se<?php echo $module->id;?>_new_cat(parent,a_parent){
					if(se<?php echo $module->id;?>_cats[a_parent]){
						//alert(se_cats[v]);	
						document.id('se<?php echo $module->id;?>_after_cat_'+parent).innerHTML = se<?php echo $module->id;?>_cats[a_parent]; 
						document.id('se<?php echo $module->id;?>_cat_'+parent).value=a_parent;
					}else{
						document.id('se<?php echo $module->id;?>_after_cat_'+parent).innerHTML = '';
						document.id('se<?php echo $module->id;?>_cat_'+parent).value=a_parent;		
					}
					
				}
				
			function se<?php echo $module->id;?>_getFields(cat_id){
					
				var el = document.getElementById("search<?php echo $module->id;?>_ex_fields");
				var before = document.getElementById("search<?php echo $module->id;?>_ex_fields").innerHTML.trim();	
				
				if(cat_id!=0){	
					el.innerHTML = '<div style="text-align:center"><img src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/loading.gif" /></div>';
					var url = '<?php echo JURI::base();?>index.php?option=com_djclassifieds&view=item&task=getSearchFields&cat_id=' + cat_id;
								 var myRequest = new Request({
							    url: '<?php echo JURI::base();?>index.php',
							    method: 'post',
								data: {
							      'option': 'com_djclassifieds',
							      'view': 'item',
							      'task': 'getSearchFields',
								  'cat_id': cat_id			  
								  },
							    onRequest: function(){
							        //myElement.set('html', '<div style="text-align:center;"><img style="margin-top:10px;" src="<?php echo JURI::base().'components/'.JRequest::getString('option').'/images/long_loader.gif';?>" /><br />loading...</div>');
							    },
							    onSuccess: function(responseText){																
									el.innerHTML = responseText;
									var djcals = document.getElements('.djsecal');
									if(djcals){
										var startDate = new Date(2008, 8, 7);
										djcals.each(function(djcla,index){
											Calendar.setup({
									            inputField  : djcla.id,
									            ifFormat    : "%Y-%m-%d",                  
									            button      : djcla.id+"button",
									            date      : startDate
									         });
										});
									}	
														 	
							    },
							    onFailure: function(){
							       // myElement.set('html', 'Sorry, your request failed, please contact to ');
							    }
							});
							myRequest.send();	
				}else{
					el.innerHTML = '';
					//el.innerHTML='<?php echo JText::_('COM_DJCLASSIFIEDS_PLEASE_SELECT_CATEGORY');?>';
				}
					
			}
				<?php // echo $this->cat_path; ?>
								
				
				</script>

		</div>		
		<div style="clear:both"></div>
		<div id="search<?php echo $module->id;?>_ex_fields" class="search_ex_fields"></div>
		<div style="clear:both"></div>
		<?php }
		if($params->get('show_type','0')==1 && $types){ ?>
			<div class="search_type">				
				<select autocomplete="off" name="se_type_id" class="inputbox" >
					<option value=""><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_TYPE');?></option>
					<?php echo JHtml::_('select.options', $types, 'value', 'text', JRequest::getInt('se_type_id'), true);?>
				</select>
			</div>
		<?php }
		if($params->get('show_time','0')==1 && $types){ 
			$days_l = JRequest::getInt('days_l','');?>
			<div class="search_time">				
				<select autocomplete="off" name="days_l" class="inputbox" >
					<option <?php if($days_l==''){echo ' SELECTED ';}?> value=""><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_ANY_TIME');?></option>
					<option <?php if($days_l=='1'){echo ' SELECTED ';}?> value="1"><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_PAST_24_HOURS');?></option>
					<option <?php if($days_l=='3'){echo ' SELECTED ';}?> value="3"><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_PAST_THREE_DAYS');?></option>
					<option <?php if($days_l=='7'){echo ' SELECTED ';}?> value="7"><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_PAST_WEEK');?></option>
					<option <?php if($days_l=='30'){echo ' SELECTED ';}?> value="30"><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_PAST_MONTH');?></option>					
				</select>
			</div>
		<?php }  
		if($params->get('show_price','1')==1){	?>
			<div class="search_price">				
				<label><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_PRICE').' ( '.$comparams->get('unit_price','EUR').' )'; ?></label>
				<span class="label1"><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_PRICE_FROM'); ?></span>
				<input size="5" class="inputbox" type="text" name="se_price_f" value="<?php echo JRequest::getVar('se_price_f',''); ?>"/>
				<span class="label2"><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_PRICE_TO'); ?></span>
				<input size="5" class="inputbox" type="text" name="se_price_t" value="<?php echo JRequest::getVar('se_price_t',''); ?>"/>
			</div>
	<?php }?>
	<?php if($params->get('show_only_images','0')==1){	?>
			<div class="search_only_images">							
				<input autocomplete="off" class="inputbox" <?php if(JRequest::getInt('se_only_img',0)){echo ' checked="checked" ';}?> type="checkbox" name="se_only_img" value="1"/>
				<span class="label"><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_SHOW_ONLY_WITH_IMAGES'); ?></span>
			</div>
	<?php }?>
	<?php if($params->get('show_only_video','0')==1){	?>
			<div class="search_only_video">							
				<input autocomplete="off" class="inputbox" <?php if(JRequest::getInt('se_only_video',0)){echo ' checked="checked" ';}?> type="checkbox" name="se_only_video" value="1"/>
				<span class="label"><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_SHOW_ONLY_WITH_VIDEO'); ?></span>
			</div>
	<?php }?>					
	<button type="submit" class="button btn"><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_SEARCH');?></button>
	
	<?php 
	if((JRequest::getInt('se',0)==1 || (JRequest::getInt('cid',0)>0 && JRequest::getInt('option','')=='com_djclassifieds')) && ($params->get('show_reset','1')>0) ){ 
		if($params->get('show_reset','1')==1){ ?>
			<a href="<?php echo JRoute::_($link_reset);?>" class="reset_button"><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_RESET');?></a>	
		<?php }else{ ?>
			<a href="<?php echo JRoute::_($link_reset);?>" class="button"><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_RESET');?></a>
		<?php } ?>
		
			
	<?php } ?>
	 
</form>
 
<div style="clear:both"></div>
</div>

<?php
$cat_id_se = 0;
if($params->get('show_cat','1')==1){
	if(JRequest::getVar('se','0','','string')!='0' && isset($_GET['se_cats'])){
		$cat_id_se= end($_GET['se_cats']);
		 
		if($cat_id_se=='' && count($_GET['se_cats'])>2){
			$cat_id_se =$_GET['se_cats'][count($_GET['se_cats'])-2];
		}	
		
		$cat_id_se = str_ireplace('p', '', $cat_id_se);				
	}
	if($cat_id_se=='0'){		
		$cat_id_se = $cid;	
	}	
	
	$se_parents = array();
	$act_parent = 0;
	if($cat_id_se > 0){
		foreach($list as $c){
			if($cat_id_se == $c->id ){
				$se_parents[] = $c->parent_id;
				$act_parent = $c->parent_id;
				break;		
			}
		}
		while($act_parent!=0){
			foreach($list as $c){
				if($act_parent == $c->id ){
					$se_parents[] = $c->parent_id;
					$act_parent = $c->parent_id;
					break;		
				}
			}	
		}
		
	}
}
$reg_id_se = 0;

if($params->get('show_loc','1')==1){
	$act_reg_parent = 0;
	if(JRequest::getVar('se','0','','string')!='0' && isset($_GET['se_regs'])){
		$reg_id_se= end($_GET['se_regs']);
		if(!$reg_id_se && count($_GET['se_regs'])>2){
			$reg_id_se =$_GET['se_regs'][count($_GET['regs'])-2];
		}
	}
	if($reg_id_se=='0'){
		$reg_id_se = JRequest::getInt('rid','0');	
	}	
	$se_reg_parents = array();
	if($reg_id_se > 0){
		foreach($regions as $r){
			if($reg_id_se == $r->id ){
				$se_reg_parents[] = $r->parent_id;
				$act_reg_parent = $r->parent_id;
				break;		
			}
		}
		while($act_reg_parent!=0){
			foreach($regions as $r){
				if($act_reg_parent == $r->id ){
					$se_reg_parents[] = $r->parent_id;
					$act_reg_parent = $r->parent_id;
					break;		
				}
			}	
		}
	}	
}
if($cat_id_se > 0 || $reg_id_se > 0){ 

	?>
	<script type="text/javascript">
		window.addEvent("load", function(){
			<?php 		
			if($cat_id_se>0){
				for($sp=count($se_parents)-1;$sp>0 ;$sp--){		
					echo 'se'.$module->id.'_new_cat('.$se_parents[$sp] .','.$se_parents[$sp-1].');';
				} 
				?>
				se<?php echo $module->id;?>_new_cat(<?php echo $se_parents[0]; ?>,<?php echo $cat_id_se; ?>);
				se<?php echo $module->id;?>_getFields(<?php echo $cat_id_se; ?>);
				
			<?php } ?>
			<?php
			if($reg_id_se > 0){
				for($sp=count($se_reg_parents)-1;$sp>0 ;$sp--){		
					echo 'se'.$module->id.'_new_reg('.$se_reg_parents[$sp] .','.$se_reg_parents[$sp-1].');';
				} 
				
				if($reg_id_se>0){ ?>
				se<?php echo $module->id;?>_new_reg(<?php echo $se_reg_parents[0]; ?>,<?php echo $reg_id_se; ?>);
				<?php }
			}	 ?>			
		});
	</script>
	<?php
	
}

if($cat_id_se==0 && $params->get('show_cat','1')==1 && $params->get('cat_id','0')>0){
		$cat_id = $params->get('cat_id','0');
		$se_parents = array();
		$act_parent = 0;
			foreach($list as $c){
				if($cat_id == $c->id ){
					$se_parents[] = $c->parent_id;
					$act_parent = $c->parent_id;
					break;		
				}
			}
			while($act_parent!=0){
				foreach($list as $c){
					if($act_parent == $c->id ){
						$se_parents[] = $c->parent_id;
						$act_parent = $c->parent_id;
						break;		
					}
				}	
			}
	
	?>
		<script type="text/javascript">		
		window.addEvent("load", function(){
			<?php
			for($sp=count($se_parents)-1;$sp>0 ;$sp--){
				echo 'se'.$module->id.'_new_cat('.$se_parents[$sp] .','.$se_parents[$sp-1].');';
			}
			?>
			se<?php echo $module->id;?>_new_cat(<?php echo $se_parents[0]; ?>,<?php echo $cat_id; ?>);
			se<?php echo $module->id;?>_getFields(<?php echo $cat_id; ?>);
		});
		</script>
<?php 		
} 
?>