<?php
/**
 * @version $Id: image.php 44 2014-11-17 07:26:11Z szymon $
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

abstract class DJImageResizer {

	private static $resized = 0;
	
	public static function createThumbnail($image_path, $folder, $width = 0, $height = 0, $mode = 'crop', $quality = 90) {

		// image resizing is disabled, we should return original image path
		if($mode == 'no') return $image_path;
		
		// check if the destination folder exists or create it
		$path = JPATH_SITE . DS . str_replace('/', DS, $folder);
		if (!JFile::exists($path) || !is_dir($path)) {
			if (!JFolder::create($path))
				return false;
		}
		// check if any dimensions was passed
		if ($width == 0 && $height == 0)
			return false;
		
		// don't procced if mode is not set
		if(!in_array($mode,array('crop','toWidth','toHeight'))) return false;
		
		// set name for image thumbnail
		$thumb_name = $width . 'x' . $height . '-' . $mode . '-' . $quality . '-' . str_replace(array('/',' '), '_', $image_path);
		
		// make image name safe
		$lang = JFactory::getLanguage();
		$thumb_name = $lang->transliterate($thumb_name);
		//$thumb_name = strtolower($thumb_name);
		$thumb_name = JFile::makeSafe($thumb_name);
		
		// if thumb exists just return the path
		if (!JFile::exists($path . DS . $thumb_name)) {
			
			// Remove php's time limit
			$timeRemoved = false;
			if(function_exists('ini_get') && function_exists('set_time_limit')) {
				if(!ini_get('safe_mode') ) {
					if(@set_time_limit(0)!==FALSE) $timeRemoved = true;
				}
			}
			// Increase php's memory limit
			if(function_exists('ini_set')) {
				@ini_set('memory_limit', '256M');				
			}
			
			// check if passed image exists
			if(strcasecmp(substr($image_path, 0, 4), 'http') === 0) { 
				$image_path = str_replace(' ', '%20', $image_path);
			}
			else if (JFile::exists(JPATH_SITE . DS . str_replace('/', DS, $image_path))) {
				$image_path = JPATH_SITE . DS . str_replace('/', DS, $image_path);
			} else {
				return false;
			}
			
			$app = JFactory::getApplication();
			$config = JFactory::getConfig();
			
			if(!$timeRemoved && ++self::$resized > 50) {
				if($config->get('config.debug')) {
					$app->enqueueMessage('DJ-MEDIATOOLS DEBUG::Redirect after '.(self::$resized-1).' images resized');
				}
				$uri = JFactory::getURI();
				$current = JRoute::_($uri->toString(), false);
				
				$app->redirect($current);
				$app->close();
			}
			
			if($config->get('config.debug')) {
				$app->enqueueMessage('DJ-MEDIATOOLS DEBUG::Creating resized image: '.$thumb_name);
			}
			
			switch($mode) {
				case 'toWidth' :
					self::resizeImage($image_path, $path . DS . $thumb_name, $width, 0, $quality);
					break;
				case 'toHeight' :
					self::resizeImage($image_path, $path . DS . $thumb_name, 0, $height, $quality);
					break;
				case 'crop' :
				default :
					self::resizeImage($image_path, $path . DS . $thumb_name, $width, $height, $quality);
					break;
			}
		}

		return $folder . '/' . $thumb_name;
	}
	
	/* grayscale function based on Angela Bradley article http://php.about.com/od/gdlibrary/ss/grayscale_gd.htm */
	public static function grayscaleImage($image_path, $folder){
		
		// check if the destination folder exists or create it
		$path = JPATH_SITE . DS . str_replace('/', DS, $folder);
		if (!JFile::exists($path) || !is_dir($path)) {
			if (!JFolder::create($path))
				return false;
		}
		
		// set name for image thumbnail
		$thumb_name = 'grayscale-' . str_replace(array('/',' '), '_', $image_path);
		
		// make image name safe
		$lang = JFactory::getLanguage();
		$thumb_name = $lang->transliterate($thumb_name);
		//$thumb_name = strtolower($thumb_name);
		$thumb_name = JFile::makeSafe($thumb_name);
		
		// if thumb exists just return the path
		if (!JFile::exists($path . DS . $thumb_name)) {
			
			// Remove php's time limit
			$timeRemoved = false;
			if(function_exists('ini_get') && function_exists('set_time_limit')) {
				if(!ini_get('safe_mode') ) {
					if(@set_time_limit(0)!==FALSE) $timeRemoved = true;
				}
			}
			// Increase php's memory limit
			if(function_exists('ini_set')) {
				@ini_set('memory_limit', '256M');				
			}
			
			// check if passed image exists
			if(strcasecmp(substr($image_path, 0, 4), 'http') === 0) { 
				$image_path = str_replace(' ', '%20', $image_path);
			}
			else if (JFile::exists(JPATH_SITE . DS . str_replace('/', DS, $image_path))) {
				$image_path = JPATH_SITE . DS . str_replace('/', DS, $image_path);
			} else {
				return false;
			}
			
			$app = JFactory::getApplication();
			$config = JFactory::getConfig();
			
			if(!$timeRemoved && ++self::$resized > 50) {
				if($config->get('config.debug')) {
					$app->enqueueMessage('DJ-MEDIATOOLS DEBUG::Redirect after '.(self::$resized-1).' images resized');
				}
				$uri = JFactory::getURI();
				$current = JRoute::_($uri->toString(), false);
				
				$app->redirect($current);
				$app->close();
			}
			
			if($config->get('config.debug')) {
				$app->enqueueMessage('DJ-MEDIATOOLS DEBUG::Creating resized image: '.$thumb_name);
			}
			
			if (!list($width, $height, $type, $attr) = getimagesize($image_path)) {
				return false;
			}
			
			$source = null;
			
			switch($type) {
				case 1 :
					$source = imagecreatefromgif($image_path);
					break;
				case 2 :
					$source = imagecreatefromjpeg($image_path);
					break;
				case 3 :
					$source = imagecreatefrompng($image_path);
					break;
				default :
					return false;
					break;
			}
			
			// Creating the Canvas for grayscale copy
			$bwimage = ImageCreateTrueColor($width, $height);
			
			//Creates the 256 color palette
			for ($c=0;$c<256;$c++) {
				$palette[$c] = imagecolorallocate($bwimage,$c,$c,$c);
			}
			
			//Reads the origonal colors pixel by pixel
			for ($y=0; $y<$height; $y++) {
				for ($x=0; $x<$width; $x++) {
					$rgb = imagecolorat($source,$x,$y);
					$r = ($rgb >> 16) & 0xFF;
					$g = ($rgb >> 8) & 0xFF;
					$b = $rgb & 0xFF;
			
					//This is where we actually use yiq to modify our rbg values, and then convert them to our grayscale palette
					$gs = self::yiq($r,$g,$b);
					imagesetpixel($bwimage,$x,$y,$palette[$gs]);
				}
			}
			
			// Outputs an grayscale image
			if (is_file($path . DS . $thumb_name)) unlink($path . DS . $thumb_name);
			
			imageinterlace($bwimage, 1); // progressive jpeg
			
			switch($type) {
				case 1 :
					imagegif($bwimage, $path . DS . $thumb_name);
					break;
				case 2 :
					imagejpeg($bwimage, $path . DS . $thumb_name);
					break;
				case 3 :
					imagepng($bwimage, $path . DS . $thumb_name);
					break;
			}
			
			ImageDestroy($bwimage);
			ImageDestroy($source);
			
		}
		
		return $folder . '/' . $thumb_name;
	}
	
	// The YIG formulas for better calculation of gray shades http://en.wikipedia.org/wiki/YIQ
	private static function yiq($r,$g,$b) {
		return (($r*0.299)+($g*0.587)+($b*0.114));
	}
		
	private static function resizeImage($path, $newpath, $nw = 0, $nh = 0, $quality = 90) {

		if (!$path || !$newpath)
			return false;
		if (!list($w, $h, $type, $attr) = getimagesize($path)) {
			return false;
		}

		$OldImage = null;

		switch($type) {
			case 1 :
				$OldImage = imagecreatefromgif($path);
				break;
			case 2 :
				$OldImage = imagecreatefromjpeg($path);
				break;
			case 3 :
				$OldImage = imagecreatefrompng($path);
				break;
			default :
				return false;
				break;
		}

		if ($nw == 0 && $nh == 0) {
			$nw = 75;
			$nh = (int)(floor(($nw * $h) / $w));
		} elseif ($nw == 0) {
			$nw = (int)(floor(($nh * $w) / $h));
		} elseif ($nh == 0) {
			$nh = (int)(floor(($nw * $h) / $w));
		}

		// check if ratios match
		$_ratio = array($w / $h, $nw / $nh);
		if ($_ratio[0] != $_ratio[1]) {// crop image

			// find the right scale to use
			$_scale = min((float)($w / $nw), (float)($h / $nh));

			// coords to crop
			$cropX = (float)($w - ($_scale * $nw));
			$cropY = (float)($h - ($_scale * $nh));

			// cropped image size
			$cropW = (float)($w - $cropX);
			$cropH = (float)($h - $cropY);

			$crop = ImageCreateTrueColor($cropW, $cropH);
			if ($type == 3) {
				imagecolortransparent($crop, imagecolorallocate($crop, 0, 0, 0));
				imagealphablending($crop, false);
				imagesavealpha($crop, true);
			}
			ImageCopy($crop, $OldImage, 0, 0, (int)($cropX / 2), (int)($cropY / 2), $cropW, $cropH);
		}

		// do the thumbnail
		$NewThumb = ImageCreateTrueColor($nw, $nh);
		if ($type == 3) {
			imagecolortransparent($NewThumb, imagecolorallocate($NewThumb, 0, 0, 0));
			imagealphablending($NewThumb, false);
			imagesavealpha($NewThumb, true);
		}
		if (isset($crop)) {// been cropped
			ImageCopyResampled($NewThumb, $crop, 0, 0, 0, 0, $nw, $nh, $cropW, $cropH);
			ImageDestroy($crop);
		} else {// ratio match, regular resize
			ImageCopyResampled($NewThumb, $OldImage, 0, 0, 0, 0, $nw, $nh, $w, $h);
		}

		if (is_file($newpath)) unlink($newpath);
		
		imageinterlace($NewThumb, 1); // progressive jpeg
		
		switch($type) {
			case 1 :
				imagegif($NewThumb, $newpath);
				break;
			case 2 :
				imagejpeg($NewThumb, $newpath, $quality);
				break;
			case 3 :
				imagepng($NewThumb, $newpath);
				break;
		}

		ImageDestroy($NewThumb);
		ImageDestroy($OldImage);

		return true;
	}

}
?>
