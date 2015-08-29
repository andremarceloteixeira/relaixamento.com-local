<?php
/**
* @version 2.0
* @package DJ Classifieds
* @subpackage DJ Classifieds Component
* @copyright Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
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
jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');

class DJClassifiedsController extends JControllerLegacy {

	function __construct($config = array())
	{
		parent::__construct($config);
		$lang = JFactory::GetLanguage();
		$lang->load('com_djclassifieds');				
		
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		$notify_days = $par->get('notify_days','0');

		if($notify_days>0 && $par->get('notify_days_trigger','1')==2 ){
			DJClassifiedsNotify::notifyExpired(5,0);
		}
		//$this->registerTask( 'modfp',  'getFrontpageXMLData' );
	}
	
	
	function processPayment()
	{
		JRequest::setVar( 'view', 'payment' );
		JRequest::setVar( 'layout', 'process' );
		parent::display();
	}
	
	function paymentReturn(){
		$app = JFactory::getApplication();	
		$id = JRequest::getInt("id","");
		$cid = JRequest::getInt("cid","");
		$itemid = JRequest::getInt("Itemid","");
		$r = JRequest::getVar("r","");
		
		if($r=='ok'){
			//$redirect= 'index.php?option=com_djclassifieds&view=item&id='.$id.'&cid='.$cid.'&Itemid='.$itemid;
			$message=JTExt::_('COM_DJCLASSIFIEDS_THANKS_FOR_PAYMENT_WAIT_FOR_CONFIRMATION');	
		}else{					
			$message=JTExt::_('COM_DJCLASSIFIEDS_PAYMENT_CANCELED');
		}
		//$redirect= 'index.php?option=com_djclassifieds&view=items&cid=0&Itemid='.$itemid;
		$redirect=DJClassifiedsSEO::getCategoryRoute('0:all');
		$redirect = JRoute::_($redirect);
		$app->redirect($redirect, $message);
	}
	
	function cronNotifications(){
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		$notify_days = $par->get('notify_days','0');

		if($notify_days>0 && $par->get('notify_days_trigger','1')==3 ){
			DJClassifiedsNotify::notifyExpired(0,0);
		}
		die('done');
	}
	
	public function upload() {	
		$user = JFactory::getUser();			
		DJUploadHelper::upload();
		return true;
	}
	
}	