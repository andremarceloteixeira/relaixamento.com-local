<?php
/**
 * @version $Id: djvideo.php 15 2013-07-15 13:34:25Z szymon $
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

defined('_JEXEC') or die();
defined('JPATH_BASE') or die;
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldDJVideo extends JFormField {
	
	protected $type = 'DJVideo';
	
	protected function getInput()
	{	
		$doc = JFactory::getDocument();
		
		// Initialize some field attributes.
		$attr = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr.= $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$attr.= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr.= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$attr.= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		
		// Initialize JavaScript field attributes.
		JHtml::_('behavior.framework', true);		
		$js = "
			function parseVideo(video_id, image) {
				var video = document.id(video_id);
				var loader = new Element('img', { src: 'components/com_djmediatools/assets/ajax-loader.gif', 'class': 'ajax-loader' });
				var imageField = document.id(image);
				var preview = document.id(video.get('id')+'_preview');
				video.blur();
				
				var imageRequest = new Request({
					url: 'index.php?option=com_djmediatools&view=item&tmpl=component',
					method: 'post',
					data: 'task=getvideothumb&video='+encodeURIComponent(video.value),
					onRequest: function(){
						loader.inject(video, 'after');
					},
					onSuccess: function(responseText){
						loader.dispose();
						if(responseText) {
							var patt=/^http/;
							if(patt.test(responseText)){
								imageField.value = responseText;
								new Element('img', { src: responseText, 'style': 'height: 180px;' }).inject(preview, 'bottom');
							} else {
				        		alert(responseText);
				        	}
						}
					},
					onFailure: function(){
						loader.dispose();
						alert('connection error');
					}
				});
				
				var parseRequest = new Request({
					url: 'index.php?option=com_djmediatools&view=item&tmpl=component',
					method: 'post',
					data: 'task=getvideoembedded&video='+encodeURIComponent(video.value),
					onRequest: function(){
						preview.empty();
						loader.inject(video, 'after');
					},
					onSuccess: function(responseText){
						loader.dispose();
						if(responseText) {
							var patt=/^http/;
							if(patt.test(responseText)){
								video.value = responseText;
								new Element('iframe', { src: responseText.replace('autoplay=1',''), height: 180, width: 288, frameborder: 0, allowfullscreen: ''}).inject(preview);
								if(imageField && (!imageField.get('value') || confirm('".JText::_('COM_DJMEDIATOOLS_CONFIRM_UPDATE_IMAGE_FIELD')."'))) {
									imageRequest.send();
								}
				        		//number.replaces(orderid);
				        	} else {
				        		alert(responseText);
				        	}
						}
					},
					onFailure: function(){
						loader.dispose();
						alert('connection error');
					}
				}).send();
				
			}
		";
		$doc->addScriptDeclaration($js);
		$thumb = ($this->element['thumb_field'] ? $this->formControl.'_'.(string) $this->element['thumb_field'] : '');
		
		$attr.= ' onpaste="setTimeout(function(){parseVideo(\''.$this->id.'\',\''.$thumb.'\')},0);"';
		$attr.= ' onclick="this.select();"';
		
		return '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
				. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $attr . '/><div class="djvideo_preview" id="' . $this->id . '_preview"></div>';
		
	}
}
?>