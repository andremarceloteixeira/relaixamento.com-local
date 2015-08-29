<?php
/**
* @version		2.0
* @package		DJ Classifieds
* @subpackage	DJ Classifieds Component
* @copyright	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license		http://www.gnu.org/licenses GNU/GPL
* @autor url    http://design-joomla.eu
* @autor email  contact@design-joomla.eu
* @Developer    Lukasz Ciastek - lukasz.ciastek@design-joomla.eu
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

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class DJClassifiedsViewProfile extends JViewLegacy{

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->_addPath('template', JPATH_COMPONENT.  '/themes/default/views/profile');
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		$theme = $par->get('theme','default');
		if ($theme && $theme != 'default') {
			$this->_addPath('template', JPATH_COMPONENT.  '/themes/'.$theme.'/views/profile');
		}
	}	
	
	function display($tpl = null){
		JHTML::_( 'behavior.modal' );		
		$document =  JFactory::getDocument();
		$par 	  = JComponentHelper::getParams( 'com_djclassifieds' );
		$app	  = JFactory::getApplication();		
		$user 	  = JFactory::getUser();		
		$model 	  = $this->getModel();
		
		$uid	  = JRequest::getVar('uid', 0, '', 'int');				
		$order    = JRequest::getCmd('order', $par->get('items_ordering','date_e'));
		$ord_t    = JRequest::getCmd('ord_t', $par->get('items_ordering_dir','desc'));

		if($uid==0 && $user->id==0){
			$uri = JFactory::getURI();
			$app->redirect('index.php?option=com_users&view=login&return='.base64_encode($uri),JText::_('COM_DJCLASSIFIEDS_PLEASE_LOGIN'));
		}else{
			if(!$uid){$uid = $user->id;}
			$items= $model->getItems($uid);
			$countitems = $model->getCountItems($uid);
			$custom_fields = $model->getCustomFields();
			$profile = $model->getProfile($uid);				
								
			//$document->setMetaData('robots','NOINDEX, FOLLOW');		
			$document->setTitle($profile['name'].' - '.JText::_('COM_DJCLASSIFIEDS_PROFILE'));
			
			if($par->get('show_types','0')){
				$types = $model->getTypes();
				$this->assignRef('types', $types);
			}
			
			
			$limit	= JRequest::getVar('limit', $par->get('limit_djitem_show'), '', 'int');
			$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
			$pagination = new JPagination( $countitems, $limitstart, $limit );				
			

			$this->assignRef('items', $items);
			$this->assignRef('custom_fields',$custom_fields);
			$this->assignRef('countitems', $countitems);		
			$this->assignRef('profile', $profile);
			$this->assignRef('pagination', $pagination);
			
	        parent::display($tpl);
		}
	}

}




