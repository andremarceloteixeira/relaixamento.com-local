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
?>
<div id="dj-classifieds">
<div class="pointspackages">
	<table cellpadding="0" cellspacing="0" width="98%" border="0" class="paymentdetails">
		<tr>
			<td class="td_title">
				<h2><?php echo JText::_("COM_DJCLASSIFIEDS_POINTS_PACKAGES"); ?></h2>
			</td>
		</tr>
		<tr>
			<td class="table_payment">
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					<?php	
						$i = 0;					
						foreach($this->points AS $point)
						{							
							?>
								<tr>
									<td class="payment_td">
										<table width="100%" cellspacing="0" cellpadding="5" border="0">										
											<tr>												
												<td class="td2">
													<h3><?php echo $point->name; ?></h3>
													<?php echo JText::_("COM_DJCLASSIFIEDS_POINTS").': '.$point->points; ?><br />													
													<?php echo JText::_("COM_DJCLASSIFIEDS_COST_PER_POINT").': '.DJClassifiedsTheme::priceFormat(round($point->price/$point->points,2),$par->get('unit_price','')); ?>													
												</td>
												<td width="100" align="center" class="td3">
													<div class="pp_price"><?php echo DJClassifiedsTheme::priceFormat($point->price,$par->get('unit_price','')); ?></div>
													<a class="button" href="index.php?option=com_djclassifieds&view=payment&type=points&id=<?php echo $point->id?>" style="text-decoration:none;">
														<?php echo JText::_('COM_DJCLASSIFIEDS_BUY_NOW'); ?>
													</a>
												</td>
											</tr>							
										</table>
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
</div>