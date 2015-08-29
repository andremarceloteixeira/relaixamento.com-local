<?php
/**
 * @version $Id: images.raw.php 13 2013-06-26 11:51:08Z szymon $
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

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.controlleradmin');


class DJMediatoolsControllerImages extends JControllerLegacy
{
	public function purge() {
		$user = JFactory::getUser();
		if (!$user->authorise('core.admin', 'com_djmediatools')){
			echo JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN');
			exit(0);
		}
		
		$files = JFolder::files(JPATH_ROOT.DS.'media'.DS.'djmediatools'.DS.'cache', '.', false, false, array('index.html', '.svn', 'CVS', '.DS_Store', '__MACOSX')); 
		$errors = array();
		if (count($files) > 0) {
			foreach ($files as $file) {
				if (!JFile::delete(JPATH_ROOT.DS.'media'.DS.'djmediatools'.DS.'cache'.DS.$file)){
					$errors[] = $file;
				}
			}
		}
		if (count($errors) > 0) {
			echo JText::sprintf('COM_DJMEDIATOOLS_N_IMAGES_HAVE_NOT_BEEN_DELETED', count($errors));
		} else {
			echo JText::sprintf('COM_DJMEDIATOOLS_N_IMAGES_HAVE_BEEN_DELETED', count($files));
		}
	}
	
	public function purgeCSS() {
		$user = JFactory::getUser();
		if (!$user->authorise('core.admin', 'com_djmediatools')){
			echo JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN');
			exit(0);
		}
	
		$files = JFolder::files(JPATH_ROOT.DS.'media'.DS.'djmediatools'.DS.'css', '.', false, false, array('index.html', '.svn', 'CVS', '.DS_Store', '__MACOSX'));
		$errors = array();
		if (count($files) > 0) {
			foreach ($files as $file) {
				if (!JFile::delete(JPATH_ROOT.DS.'media'.DS.'djmediatools'.DS.'css'.DS.$file)){
					$errors[] = $file;
				}
			}
		}
		if (count($errors) > 0) {
			echo JText::sprintf('COM_DJMEDIATOOLS_N_STYLESHEETS_HAVE_NOT_BEEN_DELETED', count($errors));
		} else {
			echo JText::sprintf('COM_DJMEDIATOOLS_N_STYLESHEETS_HAVE_BEEN_DELETED', count($files));
		}
	}
}