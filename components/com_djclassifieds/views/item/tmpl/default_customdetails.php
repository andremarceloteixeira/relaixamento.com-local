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
$par = JComponentHelper::getParams( 'com_djclassifieds' );
$app = JFactory::getApplication();
$item = $this->item;

	if(count($this->fields)>0){?>
		<div class="custom_det">
			<h2><?php echo JText::_('COM_DJCLASSIFIEDS_CUSTOM_DETAILS'); ?></h2>
			<?php 
			//echo '<pre>';print_r($this->fields);die();
			
			foreach($this->fields as $f){							
				if($par->get('show_empty_cf','1')==0){
					if(!$f->value && ($f->value_date=='' || $f->value_date=='0000-00-00')){
						continue;
					}
				}
				if($f->source>0){continue;}				
				?>
				<div class="row row_<?php echo $f->name;?>">
					<span class="row_label"><?php echo JText::_($f->label); ?></span>
					<span class="row_value">
						<?php 
						if($f->type=='textarea'){							
							if($f->value==''){echo '---'; }
							else{echo $f->value;}								
						}else if($f->type=='checkbox'){
							if($f->value==''){echo '---'; }
							else{
								echo str_ireplace(';', ', ', substr($f->value,1,-1));
							}
						}else if($f->type=='date'){
							if($f->value_date==''){echo '---'; }
							else{
								echo $f->value_date;
							}
						}else if($f->type=='link'){
							if($f->value==''){echo '---'; }
							else{
								if(strstr($f->value, 'http://') || strstr($f->value, 'https://')){
									echo '<a '.$f->params.' href="'.$f->value.'">'.str_ireplace(array("http://","https://"), array('',''), $f->value).'</a>';;
								}else{
									echo '<a '.$f->params.' href="http://'.$f->value.'">'.$f->value.'</a>';;
								}																
							}							
						}else{
							if($f->value==''){echo '---'; }
							else{echo $f->value;}	
						}
						?>
					</span>
				</div>		
			<?php
			} ?>
		</div>
	<?php }?>