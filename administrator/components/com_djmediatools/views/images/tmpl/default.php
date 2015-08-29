<?php
/**
 * @version $Id: default.php 16 2013-07-30 09:59:57Z szymon $
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
<div>
<div id="j-main-container" class="span7 form-horizontal">

		<fieldset>
		<?php
		$count = count($this->images);
		?>
		
		<div class="control-group">
			<p><?php echo JText::_('COM_DJMEDIATOOLS_IMAGES_DELETE_LABEL_DESC'); ?></p>
			<div class="control-label">
			<label for="djmt_delete_images"><?php echo JText::_('COM_DJMEDIATOOLS_IMAGES_DELETE_LABEL'); ?></label>
			</div>
			<div class="controls">
			<?php if ($count > 0) { ?>
			<button disabled="disabled" class="button btn btn-danger" id="djmt_delete_images">
				<?php echo JText::sprintf('COM_DJMEDIATOOLS_IMAGES_DELETE_BUTTON', $count); ?>
			</button>
			<?php } else { ?>
			<button disabled="disabled" class="button btn"><?php echo JText::_('COM_DJMEDIATOOLS_NOTHING_TO_DELETE'); ?></button>
			<?php } ?>
			</div>
		</div>
		<div style="clear:both"><br /><br /></div>
		<?php
		$count = count($this->stylesheets);
		?>
		<div class="control-group">
			<p><?php echo JText::_('COM_DJMEDIATOOLS_STYLESHEETS_DELETE_LABEL_DESC'); ?></p>
			<div class="control-label">
			<label for="djmt_delete_stylesheets"><?php echo JText::_('COM_DJMEDIATOOLS_STYLESHEETS_DELETE_LABEL'); ?></label>
			</div>
			<div class="controls">
			<?php if ($count > 0) { ?>
			<button disabled="disabled" class="button btn btn-primary" id="djmt_delete_stylesheets">
				<?php echo JText::sprintf('COM_DJMEDIATOOLS_STYLESHEETS_DELETE_BUTTON', $count); ?>
			</button>
			<?php } else { ?>
			<button disabled="disabled" class="button btn"><?php echo JText::_('COM_DJMEDIATOOLS_NOTHING_TO_DELETE'); ?></button>
			<?php } ?>
			</div>
		</div>
		
		</fieldset>
</div>
</div>