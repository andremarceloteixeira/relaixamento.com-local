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
JHTML::_('behavior.framework' );
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.modal');
$par = JComponentHelper::getParams( 'com_djclassifieds' );
$points_a = $par->get('points',0);
$app = JFactory::getApplication();
$menus	= $app->getMenu('site');
$menu_points = $menus->getItems('link','index.php?option=com_djclassifieds&view=points',1);
if($menu_points){
	$itemid = '&Itemid='.$menu_points->id;
}else{$itemid='';}

?>
<div id="dj-classifieds">
	<table cellpadding="0" cellspacing="0" width="98%" border="0" class="paymentdetails first">
		<tr>
			<td class="td_title">
				<h2><?php echo JText::_('COM_DJCLASSIFIEDS_PAYMENT_DETAILS');?></h2>
			</td>
		</tr>
		<tr>
			<td class="td_pdetails">
				<?php 
					$p_count =0;
					$p_total=0;
					$points_total=0;
					if(strstr($this->item->pay_type, 'cat')){
						$c_price = $this->item->c_price/100;
						echo '<div class="pd_row"><span>'.JText::_('COM_DJCLASSIFIEDS_CATEGORY').'</span><span class="price">'.DJClassifiedsTheme::priceFormat($c_price,$par->get('unit_price',''));
						if($points_a && $this->item->c_points){
							echo ' / '.$this->item->c_points.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
						}
						echo '</span></div>';
						$p_total+=$c_price;
						$points_total+=$this->item->c_points;
						$p_count++;
					}													
					if(strstr($this->item->pay_type, 'duration')){
						echo '<div class="pd_row"><span>'.JText::_('COM_DJCLASSIFIEDS_DURATION').' '.$this->item->exp_days.' ';
							if($this->item->exp_days==1){
								echo JText::_('COM_DJCLASSIFIEDS_DAY');
							}else{
								echo JText::_('COM_DJCLASSIFIEDS_DAYS');
							}
							if(strstr($this->item->pay_type, 'duration_renew')){								
								echo '</span><span class="price">'.DJClassifiedsTheme::priceFormat($this->duration->price_renew,$par->get('unit_price',''));
								if($points_a && $this->duration->points_renew){
									echo ' / '.$this->duration->points_renew.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
								}
								echo '</span></div>';
								$p_total+=$this->duration->price_renew;
								$points_total+=$this->duration->points_renew;		
							}else{
								echo '</span><span class="price">'.DJClassifiedsTheme::priceFormat($this->duration->price,$par->get('unit_price',''));
								if($points_a && $this->duration->points){
									echo ' / '.$this->duration->points.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
								}
								echo '</span></div>';
								$p_total+=$this->duration->price;
								$points_total+=$this->duration->points;
							}						
						
						$p_count++;			
					}

					foreach($this->promotions as $prom){
						if(strstr($this->item->pay_type, $prom->name)){
							echo '<div class="pd_row"><span>'.JText::_($prom->label).'</span>';
							echo '<span class="price">'.DJClassifiedsTheme::priceFormat($prom->price,$par->get('unit_price',''));
								if($points_a && $prom->points){
									echo ' / '.$prom->points.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
								}
							echo '</span></div>';
							$p_total+=$prom->price;
							$points_total+=$prom->points;
							$p_count++;			
						}	
					}
					if($p_count>1){
						echo '<div class="pd_row_total"><span>'.JText::_('COM_DJCLASSIFIEDS_TOTAL').'</span>';
						echo '<span class="price">'.DJClassifiedsTheme::priceFormat($p_total,$par->get('unit_price',''));
							if($points_a && $points_total){
								echo ' / '.$points_total.' '.JTEXT::_('COM_DJCLASSIFIEDS_POINTS');
							}
						echo '</span></div>';
					}
				?>
			</td>
		</tr>			
	</table>
	<table cellpadding="0" cellspacing="0" width="98%" border="0" class="paymentdetails">
		<tr>
			<td class="td_title">
				<h2><?php echo JText::_("COM_DJCLASSIFIEDS_PAYMENT_METHODS"); ?></h2>
			</td>
		</tr>
		<tr>
			<td class="table_payment">
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					<?php
						if($points_a && $points_total){ ?>
							<tr>
								<td class="payment_td">
									<table width="100%" cellspacing="0" cellpadding="5" border="0">
										<tr>
											<td width="160" align="center" class="td1">
												<img title="<?php echo JText::_('COM_DJCLASSIFIEDS_POINTS')?>" src="components/com_djclassifieds/assets/images/points.png">
											</td>
											<td class="td2">
												<h2><?php echo JText::_('COM_DJCLASSIFIEDS_POINTS_PACKAGE')?></h2>
												<p style="text-align:left;"><?php echo JText::_('COM_DJCLASSIFIEDS_POINTS_AVAILABLE').': '.$this->user_points;?></p>
											</td>
											<td width="130" align="center" class="td3">
												<?php 
												if($this->user_points>=$points_total){ 
													echo '<a class="button" href="index.php?option=com_djclassifieds&view=payment&task=payPoints&id='.$this->item->id.'" style="text-decoration:none;">'.JText::_('COM_DJCLASSIFIEDS_USE_POINTS').'</a>';	
												}else{ 
													echo '<a target="_blank" class="button" href="'.JRoute::_('index.php?option=com_djclassifieds&view=points'.$itemid).'" style="text-decoration:none;">'.JText::_('COM_DJCLASSIFIEDS_BUY_POINTS').'</a>';	
												} ?>
												
											</td>
									</tr>
									</table>
								</td>
							</tr>
						<?php }
						$i = 0;					
						foreach($this->PaymentMethodDetails AS $pminfo)
						{
							if($pminfo==''){
								continue;
							}
							//$paymentLogoPath = JURI::root()."plugins/djclassifiedspayment/".$this->plugin_info[$i]->name."/images/".$pminfo["logo"];
							?>
								<tr>
									<td class="payment_td">
										<?php echo $pminfo; ?>
									</td>
								</tr>
							<?php
							$i++;
						}
					?>
				</table>
			</td>
		</tr>
	</table>
</div>