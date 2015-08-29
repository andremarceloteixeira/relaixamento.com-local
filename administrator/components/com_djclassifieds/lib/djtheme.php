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
jimport( 'joomla.application.component.controller' );


class DJClassifiedsTheme {
	
	function __construct(){
	}

	public static function priceFormat($price,$unit=''){
		$app = JFactory::getApplication();
        $par = JComponentHelper::getParams( 'com_djclassifieds' );				
		$price_decimal_separator = null;
		$price_thousands_separator = null;
		
		if(!$unit){
			$unit = $par->get('unit_price','EUR');
		}
		
		switch($par->get('price_thousand_separator',0)) {
			case 0: $price_thousands_separator=''; break;
			case 1: $price_thousands_separator=' '; break;
			case 2: $price_thousands_separator='\''; break;
			case 3: $price_thousands_separator=','; break;
			case 4: $price_thousands_separator='.'; break;
			default: $price_thousands_separator=''; break;
		}
		
		switch($par->get('price_decimal_separator',0)) {
			case 0: $price_decimal_separator=','; break;
			case 1: $price_decimal_separator='.'; break;
			default: $price_decimal_separator=','; break;
		}
		
		$price_to_format = $price;
		if ($par->get('price_format','0')== 1) {
			$price = str_ireplace(',', '.', $price);
			if(is_numeric($price)){
				$price_to_format = number_format($price, $par->get('price_decimals',2), $price_decimal_separator, $price_thousands_separator);	
			}
			
		}
		
		if ($par->get('unit_price_position','0')== 1) {			
			$formated_price = $unit;
			if ($par->get('unit_price_space','1')== 1) {$formated_price .= ' ';}
			$formated_price .= $price_to_format;
		}else {
			$formated_price = $price_to_format;
			if ($par->get('unit_price_space','1')== 1) {$formated_price .= ' ';}
			$formated_price .= $unit;
		}
		return $formated_price;
		
	}
	public static function formatDate($from, $to = null){
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		if($par->get('date_format_type',0)){
			return DJClassifiedsTheme::dateFormatFromTo($from, $to);
		}else{
			if($par->get('date_persian',0)){
				return mds_date($par->get('date_format','Y-m-d H:i:s'),$from,1);
			}else{
				return date($par->get('date_format','Y-m-d H:i:s'),$from);
			}							
		}
	}
	public static function dateFormatFromTo($from, $to = null)
	 {
	 	$par = JComponentHelper::getParams( 'com_djclassifieds' );		
	  	$to = (($to === null) ? (time()) : ($to));
	  	$to = ((is_int($to)) ? ($to) : (strtotime($to)));
	  	$from = ((is_int($from)) ? ($from) : (strtotime($from)));
	  	$output = '';	  
	  	$limit = $par->get('date_format_ago_limit','2');
	  	$units = array
	  	(
		   "COM_DJCLASSIFIEDS_DATE_YEAR"   => 29030400, 
		   "COM_DJCLASSIFIEDS_DATE_MONTH"  => 2419200,  
		   "COM_DJCLASSIFIEDS_DATE_WEEK"   => 604800,   
		   "COM_DJCLASSIFIEDS_DATE_DAY"    => 86400,    
		   "COM_DJCLASSIFIEDS_DATE_HOUR"   => 3600,     
		   "COM_DJCLASSIFIEDS_DATE_MINUTE" => 60,       
		   "COM_DJCLASSIFIEDS_DATE_SECOND" => 1         
	  	);
	
	  	$diff = abs($from - $to);
	  	$suffix = (($from > $to) ? (JTEXT::_('COM_DJCLASSIFIEDS_DATE_FROM_NOW')) : (JTEXT::_('COM_DJCLASSIFIEDS_DATE_AGO')));
		
		$i=0;
		  	foreach($units as $unit => $mult){
		   		if($diff >= $mult){
		    		if($i==$limit-1 && $i>0){
		    		 	$output .= " ".JTEXT::_('COM_DJCLASSIFIEDS_DATE_AND').' '.intval($diff / $mult)." ";
					}else{
						$output .= ", ".intval($diff / $mult)." ";
					}	
		    		//$and = (($mult != 1) ? ("") : (JTEXT::_('COM_DJCLASSIFIEDS_DATE_AND')));
		    		//$output .= ", ".$and.intval($diff / $mult)." ";
					if(intval($diff / $mult) == 1){
						$output .= JTEXT::_($unit);	
					}else{
						$output .= JTEXT::_($unit."S");
					}
		    		
		    		$diff -= intval($diff / $mult) * $mult;
					$i++;
					if($i==$limit){ break; }			
		   		}
			}
			$output .= " ".$suffix;
	  		$output = substr($output, strlen(", "));
	  return $output;
	 }
	 
	static function includeCSSfiles($theme=''){				
	 	$par = JComponentHelper::getParams( 'com_djclassifieds' );
	 	$document= JFactory::getDocument();
	 	if(!$theme){ $theme = $par->get('theme','default');}
	 	$theme_path = JPATH_BASE.DS.'components'.DS.'com_djclassifieds'.DS.'themes'.DS.$theme.DS.'css'.DS;
	 	
		if (JFile::exists($theme_path.'style.css')){
	 		$cs = JURI::base().'components/com_djclassifieds/themes/'.$theme.'/css/style.css';
	 		$document->addStyleSheet($cs);
	 	}else if($theme!='default'){
	 		$cs = JURI::base().'components/com_djclassifieds/themes/default/css/style.css'; 
	 		$document->addStyleSheet($cs);
	 	}
	 	
	 	if($par->get('include_css','1')){
	 		if (JFile::exists($theme_path.'style_default.css')){
	 			$cs = JURI::base().'components/com_djclassifieds/themes/'.$theme.'/css/style_default.css';
	 			$document->addStyleSheet($cs);
	 		}else if($theme!='default'){
	 			$cs = JURI::base().'components/com_djclassifieds/themes/default/css/style_default.css'; 
	 			$document->addStyleSheet($cs);
	 		}  
	 	}
	 	
	 	$add_rtl=0;
	 	if($document->direction=='rtl'){
	 		$add_rtl=1;
		}else if (isset($_COOKIE["jmfdirection"])){
			if($_COOKIE["jmfdirection"]=='rtl'){
				$add_rtl=1;	
			}
		}else if (isset($_COOKIE["djdirection"])){
			if($_COOKIE["djdirection"]=='rtl'){
				$add_rtl=1;	
			}
		}
		if($add_rtl){
	 		if (JFile::exists($theme_path.'style_rtl.css')){
	 			$cs = JURI::base().'components/com_djclassifieds/themes/'.$theme.'/css/style_rtl.css';
	 			$document->addStyleSheet($cs);
	 		}
	 	}
	 	if (JFile::exists($theme_path.'responsive.css')){
	 		$cs = JURI::base().'components/com_djclassifieds/themes/'.$theme.'/css/responsive.css';
	 		$document->addStyleSheet($cs);
	 	}
	 	
	 	return null;
	 }	 
}