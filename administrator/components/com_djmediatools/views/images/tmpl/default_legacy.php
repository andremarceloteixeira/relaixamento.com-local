<?php
/**
 * @version $Id: default_legacy.php 13 2013-06-26 11:51:08Z szymon $
 * @package DJ-MediaTools
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 * DJ-MediaTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-MediaTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-MediaTools. If not, see <http://www.gnu.org/licenses/>.
 *
 */

defined('_JEXEC') or die('Restricted access'); 
?>


<div class="width-100">
<fieldset class="adminform">
	
	<ul class="adminformlist">
	<?php 		 
		$count = count($this->images);
		?>		
		<li>
			<p><?php echo JText::_('COM_DJMEDIATOOLS_IMAGES_DELETE_LABEL_DESC'); ?></p>
			<label for="djmt_delete_images"><?php echo JText::_('COM_DJMEDIATOOLS_IMAGES_DELETE_LABEL'); ?></label>
			<?php if ($count > 0) { ?>
			<button disabled="disabled" class="button btn" id="djmt_delete_images">
				<?php echo JText::sprintf('COM_DJMEDIATOOLS_IMAGES_DELETE_BUTTON', $count); ?>
			</button>
			<?php } else { ?>
			<button disabled="disabled" class="button btn"><?php echo JText::_('COM_DJMEDIATOOLS_NOTHING_TO_DELETE'); ?></button>
			<?php } ?>
		</li>
		<li style="clear:both"><br /><br /></li>
		<?php 		 
		$count = count($this->stylesheets);
		?>	
		<li>
			<p><?php echo JText::_('COM_DJMEDIATOOLS_STYLESHEETS_DELETE_LABEL_DESC'); ?></p>
			<label for="djmt_delete_stylesheets"><?php echo JText::_('COM_DJMEDIATOOLS_STYLESHEETS_DELETE_LABEL'); ?></label>
			<?php if ($count > 0) { ?>
			<button disabled="disabled" class="button btn" id="djmt_delete_stylesheets">
				<?php echo JText::sprintf('COM_DJMEDIATOOLS_STYLESHEETS_DELETE_BUTTON', $count); ?>
			</button>
			<?php } else { ?>
			<button disabled="disabled" class="button btn"><?php echo JText::_('COM_DJMEDIATOOLS_NOTHING_TO_DELETE'); ?></button>
			<?php } ?>
		</li>
	</ul>
</fieldset>
<div style="clear: both" class="clr"></div>
</div>
