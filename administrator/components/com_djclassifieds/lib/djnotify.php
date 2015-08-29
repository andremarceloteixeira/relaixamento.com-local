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


class DJClassifiedsNotify {
	
	function __construct(){
	}

	public static function notifyExpired($limit=0,$msg=1){
		$app = JFactory::getApplication();
        $par = JComponentHelper::getParams( 'com_djclassifieds' );				
		$mailfrom = $app->getCfg( 'mailfrom' );
		$config = JFactory::getConfig();    
		$fromname=$config->get('sitename').' - '.str_ireplace('administrator/', '', JURI::base());
		$notify_days = $par->get('notify_days','0');		
		$db= JFactory::getDBO();
		$mailer = JFactory::getMailer();
		
		if($notify_days>0){			
			$date_exp = date("Y-m-d G:i:s",mktime(date("G"), date("i"), date("s"), date("m")  , date("d")+$notify_days, date("Y")));
			$lim ='';
			
			if($limit>0){
				$lim = ' LIMIT '.$limit;	
			}
			
			
			$query = "SELECT i.id, i.cat_id, i.date_exp, i.name, i.user_id, u.email, u.name as u_name "
					."FROM #__djcf_items i, #__users u WHERE i.user_id = u.id AND i.notify=0 "
					."AND i.date_exp < '".$date_exp."' ".$lim;
			$db->setQuery($query);
			$items = $db->loadObjectList();
			//	echo '<pre>';print_r($db);print_r($items);die();	
		
				$menus	= $app->getMenu('site');
				$menu_item = $menus->getItems('link','index.php?option=com_djclassifieds&view=items',1);
				
				$itemid = ''; 
				if($menu_item){
					$itemid='&Itemid='.$menu_item->id;
				}
				$renew_link=str_ireplace('administrator/', '', JURI::base()).'index.php?option=com_djclassifieds&view=useritems'.$itemid;
				$renew_link = JRoute::_($renew_link);
				
				$update_id = '';
				$items_c=0;
				
				foreach($items as $i){
					$mailto = $i->email;
										
					$subject= sprintf ( JText::_( 'COM_DJCLASSIFIEDS_UNEMAIL_TITLE' ), $i->name);
					$message = sprintf ( JText::_( 'COM_DJCLASSIFIEDS_UNEMAIL_MESSAGE' ), $i->name, $notify_days);
					$message .= sprintf ( JText::_( 'COM_DJCLASSIFIEDS_UNEMAIL_RENEW' ), $renew_link);
					$message .= sprintf ( JText::_( 'COM_DJCLASSIFIEDS_UNEMAIL_REGARDS' ), $config->get('sitename'));					
					$mailer = JFactory::getMailer();
					$send =$mailer->sendMail($mailfrom, $fromname, $mailto, $subject, $message);
					if (!is_object($send)){
						$update_id .= $i->id.', ';
						$items_c++;
					}
					else {
						$app->enqueueMessage($send->get('message').' - email server might be currently overloaded. Notification emails will be sent later.');
						break;
					}
				}
				
				
				if($items_c>0){
					$update_id = substr($update_id, 0,-2);
					$query = "UPDATE `#__djcf_items` SET notify=1 WHERE id in (".$update_id.")";
					$db->setQuery($query);
					$db->query();
					if($msg==1){
						$app->enqueueMessage($items_c.' '.JText::_('COM_DJCLASSIFIEDS_NOTIFICATIONS_SENT'));
					}
				}											
		}
		return null;	
	}
	
	public static function notifyAdmin($id,$new_ad=1){
		$app = JFactory::getApplication();
		$par = JComponentHelper::getParams( 'com_djclassifieds' );	
		if($par->get('notify_admin','0')){
			if($par->get('notify_user_email','')!=''){
				$mailto = $par->get('notify_user_email');	
			}else{
				$mailto = $app->getCfg( 'mailfrom' );
			}
			
			$mailfrom = $app->getCfg( 'mailfrom' );
			$config = JFactory::getConfig();    
			$fromname=$config->get('sitename').' - '.str_ireplace('administrator/', '', JURI::base());
			
			$query = "SELECT i.id, i.cat_id, i.name, i.alias, i.intro_desc, i.description, i.user_id,i.promotions, c.name as c_name, c.alias as c_alias,u.name as u_name "
					."FROM #__djcf_items i "
					."LEFT JOIN #__djcf_categories c ON c.id=i.cat_id "
					."LEFT JOIN #__users u ON u.id=i.user_id "
					."WHERE i.id = ".$id." LIMIT 1";
			$db = JFactory::getDBO();		
			$db->setQuery($query);
			$item =$db->loadObject();
			
			if($new_ad){
				$subject = JText::_('COM_DJCLASSIFIEDS_ANEMAIL_TITLE').' '.$config->get('sitename');
				$m_message = JText::_('COM_DJCLASSIFIEDS_ANEMAIL_TITLE').' '.$config->get('sitename')."<br /><br />";	
			}else{
				$subject = JText::_('COM_DJCLASSIFIEDS_ANEMAIL_TITLE_EDIT').' '.$config->get('sitename');
				$m_message = JText::_('COM_DJCLASSIFIEDS_ANEMAIL_TITLE_EDIT').' '.$config->get('sitename')."<br /><br />";
			}
			
			$m_message .= JText::_('COM_DJCLASSIFIEDS_TITLE').': '.$item->name."<br /><br />";
			$m_message .= JText::_('COM_DJCLASSIFIEDS_CATEGORY').': '.$item->c_name."<br /><br />";
			$m_message .= JText::_('COM_DJCLASSIFIEDS_INTRO_DESCRIPTION').': '.$item->intro_desc."<br /><br />";
			$m_message .= JText::_('COM_DJCLASSIFIEDS_DESCRIPTION').': '.$item->description."<br /><br />";
			if($item->user_id>0){
				$m_message .= JText::_('COM_DJCLASSIFIEDS_ANEMAIL_FROM_USER').': '.$item->u_name.' ('.$item->user_id.")<br /><br />";				
			}else{
				$m_message .= JText::_('COM_DJCLASSIFIEDS_ANEMAIL_FROM_USER').': '.JText::_('COM_DJCLASSIFIEDS_GUEST')."<br /><br />";
				
			}
			if($item->promotions){
				$query = "SELECT * FROM #__djcf_promotions ";
				$db = JFactory::getDBO();		
				$db->setQuery($query);
				$promotions =$db->loadObjectList();
				$m_message .= JText::_('COM_DJCLASSIFIEDS_ANEMAIL_PROMOTIONS').': <br />';
				foreach($promotions as $prom){
					if(strstr($item->promotions, $prom->name)){
						$m_message .= JText::_($prom->label).'<br />';
					}
				}
				$m_message .='<br />';								
			}
			
			$u = JURI::getInstance( JURI::base() );
			if($u->getScheme()){
				$link = $u->getScheme().'://';
			}else{
				$link = 'http://';
			}
			$link .= $u->getHost().JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias));
					
			$m_message .='<a href="'.$link.'">'.$link.'</a>';
						
			$mailer = JFactory::getMailer();
			$mailer->sendMail($mailfrom, $fromname, $mailto, $subject, $m_message,$mode=1);
		}
		return null;		
	}

	public static function notifyNewAdvertUser($item,$cat){
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();
		$par =  JComponentHelper::getParams( 'com_djclassifieds' );	
		$user = JFactory::getUser();			
			if($user->id){
				$mailto = $user->email;
			}else{
				$mailto = $item->email;
			}								
			$mailfrom = $app->getCfg( 'mailfrom' );			    
			$fromname=$config->get('sitename').' - '.str_ireplace('administrator/', '', JURI::base());

			$subject = JText::_('COM_DJCLASSIFIEDS_NAU_EMAIL_TITLE').' '.$config->get('sitename');
			$m_message = JText::_('COM_DJCLASSIFIEDS_NAU_EMAIL_TITLE').' '.$config->get('sitename')."<br /><br />";
			
			$m_message .= JText::_('COM_DJCLASSIFIEDS_TITLE').': '.$item->name."<br /><br />";
			$m_message .= JText::_('COM_DJCLASSIFIEDS_STATUS').': ';
				if($item->published){
					$m_message .= JText::_('COM_DJCLASSIFIEDS_PUBLISHED')."<br /><br />";		
				}else{
					$m_message .= JText::_('COM_DJCLASSIFIEDS_WAITING_FOR_PUBLISH')."<br /><br />";
				}
			$m_message .= JText::_('COM_DJCLASSIFIEDS_INTRO_DESCRIPTION').': '.$item->intro_desc."<br /><br />";
			
			$u = JURI::getInstance( JURI::base() );
			if($u->getScheme()){
				$link = $u->getScheme().'://';
			}else{
				$link = 'http://';
			}
			$edit_link = $link;
			$link .= $u->getHost().JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$cat->alias));
					
			$m_message .=JText::_('COM_DJCLASSIFIEDS_ADVERT_LINK').': <a href="'.$link.'">'.$link.'</a><br /><br />';

			if(!$user->id && $item->email && $par->get('guest_can_edit',0)){
				$edit_link .= $u->getHost().JRoute::_(DJClassifiedsSEO::getNewAdLink().'&token='.$item->token);
				$m_message .=JText::_('COM_DJCLASSIFIEDS_EDITION_LINK').': <a href="'.$edit_link.'">'.$edit_link.'</a><br /><br />';
			}
			
			$mailer = JFactory::getMailer();
			$mailer->sendMail($mailfrom, $fromname, $mailto, $subject, $m_message,$mode=1);
		
		return null;		
	}


}
