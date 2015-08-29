<?php
/**
* @version		2.0
* @package		DJ Classifieds
* @subpackage 	DJ Classifieds Component
* @copyright 	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license 		http://www.gnu.org/licenses GNU/GPL
* @author 		url: http://design-joomla.eu
* @author 		email contact@design-joomla.eu
* @developer 	Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
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

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.modal');

class DJClassifiedsImage {
	
	//function makeThumb_old($adres, $nw, $nh, $ext)

	public static function makeThumb($path, $newpath, $nw = 0, $nh = 0, $keep_ratio = false, $enlarge = true) {
			
		//$newpath = $path.'.'.$ext.'.jpg';	
		
		$params = JComponentHelper::getParams( 'com_djclassifieds' );
		if($params->get('image_resize', '0')){
			$keep_ratio = true;
		}
		if (!$path || !$newpath)
		return false;
		if (! list ($w, $h, $type, $attr) = getimagesize($path)) {
			return false;
		}

		$OldImage = null;

		switch($type)
		{
			case 1:
				$OldImage = imagecreatefromgif($path);
				break;
			case 2:
				$OldImage = imagecreatefromjpeg($path);
				break;
			case 3:
				$OldImage = imagecreatefrompng($path);
				break;
			default:
				return  false;
				break;
		}
		
		if ($nw == 0 && $nh == 0) {
			$nw = 75;
			$nh = (int)(floor(($nw * $h) / $w));
		}
		elseif ($nw == 0) {
			$nw = (int)(floor(($nh * $w) / $h));
		}
		elseif ($nh == 0) {
			$nh = (int)(floor(($nw * $h) / $w));
		}
		if ($keep_ratio) {
			$x_ratio = $nw / $w;
			$y_ratio = $nh / $h;

			if (($x_ratio * $h) < $nh){
				$nh = ceil($x_ratio * $h);
			}else{
				$nw = ceil($y_ratio * $w);
			}
		}
		
		if ( ($nw > $w || $nh > $h) && !$enlarge) {
			$nw = $w;
			$nh = $h;
		}

		// check if ratios match
		$_ratio=array($w/$h, $nw/$nh);
		if ($_ratio[0] != $_ratio[1]) { // crop image

			// find the right scale to use
			$_scale=min((float)($w/$nw),(float)($h/$nh));

			// coords to crop
			$cropX=(float)($w-($_scale*$nw));
			$cropY=(float)($h-($_scale*$nh));

			// cropped image size
			$cropW=(float)($w-$cropX);
			$cropH=(float)($h-$cropY);

			$crop=ImageCreateTrueColor($cropW,$cropH);
			if ($type == 3) {
				imagecolortransparent($crop, imagecolorallocate($crop, 0, 0, 0));
				imagealphablending($crop, false);
				imagesavealpha($crop, true);
			}
			
			$cropCoeffsX = array('l' => 0, 'm' => 0.5, 'r' => 1);
			$cropCoeffsY = array('t' => 0, 'm' => 0.5, 'b' => 1);
			
			$cropAlignmentX = $params->get('crop_alignment_h', 'm');
			$cropAlignmentY = $params->get('crop_alignment_v', 'm');
			
			if (!array_key_exists($cropAlignmentX, $cropCoeffsX)) {
				$cropAlignmentX = 'm';
			}
			
			if (!array_key_exists($cropAlignmentY, $cropCoeffsY)) {
				$cropAlignmentY = 'm';
			}
			
			ImageCopy(
			$crop,
			$OldImage,
			0,
			0,
			(int)($cropX * $cropCoeffsX[$cropAlignmentX]),
			(int)($cropY * $cropCoeffsY[$cropAlignmentY]),
			$cropW,
			$cropH
			);
		}

		// do the thumbnail
		$NewThumb=ImageCreateTrueColor($nw,$nh);
		if ($type == 3) {
			imagecolortransparent($NewThumb, imagecolorallocate($NewThumb, 0, 0, 0));
			imagealphablending($NewThumb, false);
			imagesavealpha($NewThumb, true);
		}
		if (isset($crop)) { // been cropped
			ImageCopyResampled(
			$NewThumb,
			$crop,
			0,
			0,
			0,
			0,
			$nw,
			$nh,
			$cropW,
			$cropH
			);
			ImageDestroy($crop);
		} else { // ratio match, regular resize
			ImageCopyResampled(
			$NewThumb,
			$OldImage,
			0,
			0,
			0,
			0,
			$nw,
			$nh,
			$w,
			$h
			);
		}

		$thumb_path = $newpath;
		if (is_file($thumb_path))
		unlink($thumb_path);
		switch($type)
		{
			case 1:
				imagegif($NewThumb, $thumb_path);
				break;
			case 2:
				imagejpeg($NewThumb, $thumb_path, 85);
				break;
			case 3:
				imagepng($NewThumb, $thumb_path);
				break;
		}
		//imagejpeg($NewThumb, $thumb_path, 85);

		ImageDestroy($NewThumb);
		ImageDestroy($OldImage);

		return true;
	}	
	
	public static function makeThumb_old($path, $nw = 0, $nh = 0,$ext, $keep_ratio = false, $enlarge = true) {
			
		$newpath = $path.'.'.$ext.'.jpg';
		
		if (!$path || !$newpath)
		return false;
		if (! list ($w, $h, $type, $attr) = getimagesize($path)) {
			return false;
		}

		$OldImage = null;

		switch($type)
		{
			case 1:
				$OldImage = imagecreatefromgif($path);
				break;
			case 2:
				$OldImage = imagecreatefromjpeg($path);
				break;
			case 3:
				$OldImage = imagecreatefrompng($path);
				break;
			default:
				return  false;
				break;
		}
		
		if ($nw == 0 && $nh == 0) {
			$nw = 75;
			$nh = (int)(floor(($nw * $h) / $w));
		}
		elseif ($nw == 0) {
			$nw = (int)(floor(($nh * $w) / $h));
		}
		elseif ($nh == 0) {
			$nh = (int)(floor(($nw * $h) / $w));
		}
		if ($keep_ratio) {
			$x_ratio = $nw / $w;
			$y_ratio = $nh / $h;

			if (($x_ratio * $h) < $nh){
				$nh = ceil($x_ratio * $h);
			}else{
				$nw = ceil($y_ratio * $w);
			}
		}
		
		if ( ($nw > $w || $nh > $h) && !$enlarge) {
			$nw = $w;
			$nh = $h;
		}

		// check if ratios match
		$_ratio=array($w/$h, $nw/$nh);
		if ($_ratio[0] != $_ratio[1]) { // crop image

			// find the right scale to use
			$_scale=min((float)($w/$nw),(float)($h/$nh));

			// coords to crop
			$cropX=(float)($w-($_scale*$nw));
			$cropY=(float)($h-($_scale*$nh));

			// cropped image size
			$cropW=(float)($w-$cropX);
			$cropH=(float)($h-$cropY);

			$crop=ImageCreateTrueColor($cropW,$cropH);
			if ($type == 3) {
				imagecolortransparent($crop, imagecolorallocate($crop, 0, 0, 0));
				imagealphablending($crop, false);
				imagesavealpha($crop, true);
			}
			ImageCopy(
			$crop,
			$OldImage,
			0,
			0,
			(int)($cropX/2),
			(int)($cropY/2),
			$cropW,
			$cropH
			);
		}

		// do the thumbnail
		$NewThumb=ImageCreateTrueColor($nw,$nh);
		if ($type == 3) {
			imagecolortransparent($NewThumb, imagecolorallocate($NewThumb, 0, 0, 0));
			imagealphablending($NewThumb, false);
			imagesavealpha($NewThumb, true);
		}
		if (isset($crop)) { // been cropped
			ImageCopyResampled(
			$NewThumb,
			$crop,
			0,
			0,
			0,
			0,
			$nw,
			$nh,
			$cropW,
			$cropH
			);
			ImageDestroy($crop);
		} else { // ratio match, regular resize
			ImageCopyResampled(
			$NewThumb,
			$OldImage,
			0,
			0,
			0,
			0,
			$nw,
			$nh,
			$w,
			$h
			);
		}

		$thumb_path = $newpath;
		if (is_file($thumb_path))
		unlink($thumb_path);
		
		/*switch($type)
		{
			case 1:
				imagegif($NewThumb, $thumb_path);
				break;
			case 2:
				imagejpeg($NewThumb, $thumb_path, 85);
				break;
			case 3:
				imagepng($NewThumb, $thumb_path);
				break;
		}*/
		
		imagejpeg($NewThumb, $thumb_path, 85);
		
		ImageDestroy($NewThumb);
		ImageDestroy($OldImage);

		return true;
	}
	
	public static function getAdsImages($item_ids){
		$db			= JFactory::getDBO();
		$query = "SELECT img.* FROM #__djcf_images img "
				."WHERE img.item_id IN (".$item_ids.") AND img.type='item' "
						."ORDER BY img.item_id, img.ordering";
		$db->setQuery($query);
		$items_img=$db->loadObjectList();
			foreach($items_img as $img){
				$img->thumb_s = $img->path.$img->name.'_ths.'.$img->ext;
				$img->thumb_m = $img->path.$img->name.'_thm.'.$img->ext;
				$img->thumb_b = $img->path.$img->name.'_thb.'.$img->ext;
			}
		
		return $items_img; 
	} 
   
}