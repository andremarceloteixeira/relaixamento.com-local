<?php
/**
 * @version $Id: controller.php 17 2013-08-19 09:55:29Z szymon $
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class DJMediatoolsController extends JControllerLegacy
{
	protected $default_view = 'cpanel';
	
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/djmediatools.php';
		DJMediatoolsHelper::addSubmenu($view = JRequest::getCmd('view', 'cpanel'));
				
		parent::display();

		return $this;
	}
	
	public function getvideoembedded() {
		
		$app = JFactory::getApplication();
		
		$link = urldecode(JRequest::getVar('video'));
		
		// get video object
		$video = DJVideoHelper::getVideo($link);
		
		if(count($video->getErrors())) {
			echo $video->getError();
		} else {
			echo $video->embed;
		}
		
		$app->close();
	}
	
	public function getvideothumb() {
	
		$app = JFactory::getApplication();
		
		$link = urldecode(JRequest::getVar('video'));
		
		// get video object
		$video = DJVideoHelper::getVideo($link);
		
		if(count($video->getErrors())) {
			echo $video->getError();
		} else {
			echo $video->thumbnail;
		}
		
		$app->close();
	}
	
	public function upload() {
		
		// todo: secure upload from injections
		$user = JFactory::getUser();
		if (!$user->authorise('core.manage', 'com_djmediatools')){
			echo JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN');
			exit(0);
		}
		
		DJUploadHelper::upload();
		
		return true;
	}
	
	// hidden task
	public function moveurltovideo(){
		
		$db = JFactory::getDBO();
		$db->setQuery('SELECT * FROM #__djmt_items');
		$items = $db->loadObjectList();
		$moved = 0;
		
		foreach($items as $item) {
				
			$item->params = new JRegistry($item->params);
			$linktype = explode(';', $item->params->get('link_type',''));
				
			if($linktype[0] == 'url') {
		
				$video = DJVideoHelper::getVideo($item->params->get('link_url'));
				
				if(count($video->getErrors())) continue; // not a video link
				if(empty($video->embed)) continue;
				
				$db->setQuery('UPDATE #__djmt_items SET video='.$db->quote($video->embed).' WHERE id='.$item->id.' AND (video IS NULL OR video=\'\')');
				$db->query();
				
				$moved++;
			}
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_djmediatools', false), 'Url link parameter moved to Video link successfully.');
		
		return true;
	}
}