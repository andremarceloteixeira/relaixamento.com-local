<?php
/**
 * @version $Id: video.php 26 2014-03-31 14:07:17Z szymon $
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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

abstract class DJVideoHelper {
	
	private static $vid = array();
	
	// @link normal or short (youtube) link to the vimeo or youtube video
	public static function getEmbeddedLink($link) {
		
		$video = self::getVideo($link);
		
		return $video->embed;
	}
	
	public static function getThumbnail($link) {
	
		$video = self::getVideo($link);
	
		return $video->thumbnail;
	}
	
	public static function getVideo($link) {
		
		$key = md5($link);
		
		if(!isset(self::$vid[$key])) {
				
			self::parseVideoLink($link, $key);
				
		}
		
		return self::$vid[$key];
		
	}
	
	private static function parseVideoLink($link, $key) {
		
		self::$vid[$key] = new JObject();
			
		$parts = explode('/',$link);
		
		if(isset($parts[3])) {
				
			if(!in_array($parts[3],array('embed','video'))) {
					
				switch($parts[2]) {
					case 'youtu.be':
						self::$vid[$key]->provider = 'youtube';
						self::$vid[$key]->id = array_pop($parts);
						self::$vid[$key]->embed = 'http://www.youtube.com/embed/';
						self::$vid[$key]->embed.= self::$vid[$key]->id;
						self::$vid[$key]->thumbnail = 'http://img.youtube.com/vi/'.self::$vid[$key]->id.'/hqdefault.jpg'; // hqdefault.jpg = 640x480, maxresdefault.jpg = 1280x800
						break;
					case 'vimeo.com':
						self::$vid[$key]->provider = 'vimeo';
						self::$vid[$key]->id = array_pop($parts);
						self::$vid[$key]->embed = 'http://player.vimeo.com/video/';
						self::$vid[$key]->embed.= self::$vid[$key]->id;
						$file = file_get_contents('http://vimeo.com/api/v2/video/'.self::$vid[$key]->id.'.php');
						if(!$file) self::$vid[$key]->setError(JText::_('COM_DJMEDIATOOLS_NOT_SUPPORTED_VIDEO_LINK'));
						else {
							$hash = unserialize($file);
							self::$vid[$key]->thumbnail = $hash[0]['thumbnail_large']; // thumbnail_large = 640x360
						}
						break;
					case 'www.youtube.com':
					case 'youtube.com':
						self::$vid[$key]->provider = 'youtube';
						$video = array_pop($parts);
						preg_match('/v=([\w\d_-]+)/', $video, $video);
						self::$vid[$key]->id = $video[1];
						self::$vid[$key]->embed = 'http://www.youtube.com/embed/';
						self::$vid[$key]->embed.= self::$vid[$key]->id;
						self::$vid[$key]->thumbnail = 'http://img.youtube.com/vi/'.self::$vid[$key]->id.'/hqdefault.jpg'; // hqdefault.jpg = 640x480, maxresdefault.jpg = 1280x800
						break;
					default:
						self::$vid[$key]->setError(JText::_('COM_DJMEDIATOOLS_NOT_SUPPORTED_VIDEO_LINK'));
				}
			} else {
				self::$vid[$key]->embed = '';
				self::$vid[$key]->thumbnail = '';
			}
				
		} else {
			self::$vid[$key]->setError(JText::_('COM_DJMEDIATOOLS_NOT_SUPPORTED_VIDEO_LINK'));
		}
		
	}
	
}