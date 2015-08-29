<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class Com_DJMediatoolsInstallerScript
{
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	
	function preflight( $type, $parent ) {
		$jversion = new JVersion();
	
		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
		$this->oldrelease = $this->getParam('version');
		
	}
	
	function postflight( $type, $parent ) {
		
		$db = JFactory::getDBO();
		
		if($type == 'install') {
			
			$db->setQuery("UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND (element='djmediatools' OR folder='djmediatools')");
			$db->query();
		}
		
		if($type == 'update') {
			
			defined('DS') or define('DS', DIRECTORY_SEPARATOR);
			
			// fix doubled single album view
			if ( version_compare( $this->oldrelease, '2.0.0' , 'lt' ) ) {				
				$path = JPATH_ROOT.DS.'components'.DS.'com_djmediatools'.DS.'views'.DS.'item'.DS.'tmpl'.DS.'default.xml';
				//JFactory::getApplication()->enqueueMessage($path);
				if(JFile::exists($path)) {
					@unlink($path);
				}				
			}
			
			// fix video column for updates from verion 1.3.4 to 1.4.beta2
			$fixvideo = false;
			if ( version_compare( $this->oldrelease, '1.3.4' , 'ge' ) && version_compare( $this->oldrelease, '1.4.beta3' , 'lt' ) ) {
				
				$config = JFactory::getConfig();
				$app = JFactory::getApplication();
				
				$db->setQuery("SELECT count(column_name) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$config->get('dbprefix')."djmt_items' AND table_schema = '".$config->get('db')."' AND column_name = 'video'");
				$result = $db->loadResult();
				
				if(!$result) {
					
					$db->setQuery("ALTER TABLE `".$config->get('dbprefix')."djmt_items` ADD `video` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `image`");
					$db->query();
					$fixvideo = true;
				}				
			}
			// convert old video links into video field
			if ( $fixvideo || version_compare( $this->oldrelease, '1.3.4' , 'lt' ) ) {
				
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_djmediatools'.DS.'lib'.DS.'video.php');
				
				$db->setQuery('SELECT * FROM #__djmt_items');
				$items = $db->loadObjectList();
				
				foreach($items as $item) {
					
					$item->params = new JRegistry($item->params);
					$linktype = explode(';', $item->params->get('link_type',''));
					
					if($linktype[0] == 'video') {
						
						$video = DJVideoHelper::getVideo($item->params->get('video_link'));
						
						$db->setQuery('UPDATE #__djmt_items SET video='.$db->quote($video->embed).' WHERE id='.$item->id);
						$db->query();
					}
				}
			}
			
		}
	}
	
	
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_djmediatools"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
 
	/*
	 * sets parameter values in the component's row of the extension table
	 */
	function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_djmediatools"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE name = "com_djmediatools"' );
				$db->query();
		}
	}
}
