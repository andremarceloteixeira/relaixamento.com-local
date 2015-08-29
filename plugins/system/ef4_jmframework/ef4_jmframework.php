<?php
/**
 * @version $Id: ef4_jmframework.php 63 2014-11-25 15:19:52Z michal $
 * @package JMFramework
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Michal Olczyk - michal.olczyk@design-joomla.eu
 *
 * JMFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JMFramework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with JMFramework. If not, see <http://www.gnu.org/licenses/>.
 *
 */

defined('_JEXEC') or die('Restricted access');

class plgSystemEF4_JMFramework extends JPlugin
{
    private $template;
    
    public function __construct(&$subject, $config = array()) {
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
        parent::__construct($subject, $config);
    }
    
    /**
     * 
     * We need to specially prepare the form because we're merging templateDetails.xml from a template and params.xml from the plugin.
     * @param JForm $form
     * @param mixed $data
     */
    function onContentPrepareForm($form, $data)
    {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        $this->template = $this->getTemplateName();
        
        if ($this->template && ( ($app->isAdmin() && $form->getName() == 'com_templates.style') || ($app->isSite() && ($form->getName() == 'com_config.templates' || $form->getName() == 'com_templates.style')) )) {
            jimport('joomla.filesystem.path');
            //JForm::addFormPath( dirname(__FILE__) . DS. 'includes' . DS .'assets' . DS . 'admin' . DS . 'params');
            $plg_file = JPath::find(dirname(__FILE__) . DS. 'includes' . DS .'assets' . DS . 'admin' . DS . 'params', 'template.xml');
            $tpl_file = JPath::find(JPATH_ROOT . DS. 'templates' . DS . $this->template, 'templateDetails.xml');
            $default_settings_file = JPATH_ROOT . DS. 'templates' . DS . $this->template . DS . 'templateDefaults.json';
            
            if (!$plg_file) {
                return false;
            }
            
            // params.xml should be loaded first and templateDetails.xml afterwards 
            if ($tpl_file) {
                $form->loadFile($plg_file, false, '//form');
                $form->loadFile($tpl_file, false, '//config');
            } else {
                $form->loadFile($plg_file, false, '//form');
            }
            
            // for users' own safety, we don't allow some things to be changed in the front-end
            if ($app->isSite()) {
                $jmstorage_fields = $form->getFieldset('jmstorage');
                foreach ($jmstorage_fields as $name => $field){
                    $form->removeField($name, 'params');
                }
                $form->removeField('config', 'params');
                
                $jmlayoutbuilder_fields = $form->getFieldset('jmlayoutbuilder');
                foreach ($jmlayoutbuilder_fields as $name => $field){
                    $form->removeField($name, 'params');
                }
                $form->removeField('layout', 'params');
            }
            
            // Hiding a notice to enable this plugin. If plugin is disabled then the notice is visible. That's it.
            if ($app->isAdmin()) {
                $doc->addStyleDeclaration('#jm-ef3plugin-info, .jm-row > .jm-notice {display: none !important}');
            }
            
            if (JFile::exists($default_settings_file)) {
            	$settings_json = JFile::read($default_settings_file);
            	if ($settings_json) {
            		$defaults = json_decode($settings_json, true);
            		if ($defaults && is_array($defaults)) {
            			foreach ($form->getFieldset() as $field) {
            				$field_name = $field->__get('fieldname');
            				if (array_key_exists($field_name, $defaults) && is_scalar($defaults[$field_name])) {
            					$form->setFieldAttribute($field_name, 'default', $defaults[$field_name], $field->__get('group'));
            				}
            			}
            			/*if (!empty($data) && isset($data->params)) {
            				foreach ($data->params as $param_name => $param_value) {
            					if (empty($param_value) && array_key_exists($param_name, $defaults)  && is_scalar($defaults[$param_name])) {
            						$data->params[$param_name] = $defaults[$param_name];
            					}
            				}
            			}*/
            		}
            	}
            }
        }
    }
    
    /**
     *
     * Preparing default values
     * @param string $context
     * @param mixed $data
     */
    function onContentPrepareData($context, $data)
    {
    	$app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        $this->template = $this->getTemplateName();
        
        if ($this->template && ( ($app->isAdmin() && $context == 'com_templates.style') || ($app->isSite() && $context == 'com_config.templates') )) {
            jimport('joomla.filesystem.path');
            
            $default_settings_file = JPATH_ROOT . DS. 'templates' . DS . $this->template . DS . 'templateDefaults.json';
            
            if (JFile::exists($default_settings_file)) {
            	$settings_json = JFile::read($default_settings_file);
            	if ($settings_json) {
            		
            		$defaults = json_decode($settings_json, true);
            		if ($defaults && is_array($defaults)) {
            			if (!empty($data) && isset($data->params)) {
            				
            				if (!is_array($data->params)) {
            					if (is_object($data->params)) {
            						$data->params = JArrayHelper::fromObject($data->params);
            					} else {
            						$data->params = array();
            					}
            				}
            				
            				foreach ($defaults as $param_name => $param_value) {
            					if (empty($data->params[$param_name])) {
            						$data->params[$param_name] = $defaults[$param_name];
            					}
            				}
            			}
            		}
            	}
            }
        }
    }

    
    /**
     * After the routing we can determine which template is being used. 
     * The plugin works only with specially prepared Joomla Monster templates.
     */
    function onAfterRoute(){
        $app = JFactory::getApplication();
        
        // If it's not Joomla Monster template, the $template will be false.
        $template = $this->getTemplateName();
        if ($template) {
            
            // This plugin's directory
            define('JMF_FRAMEWORK_PATH', dirname(__FILE__));
            
            // Plugin's URL
            define('JMF_FRAMEWORK_URL', JURI::root(true).'/plugins/system/ef4_jmframework');
            
            // Name of the template
            define('JMF_TPL', $template);
            
            // Path to template's directory
            define('JMF_TPL_PATH', JPATH_ROOT.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template);
            
            // Template directory's URL
            define('JMF_TPL_URL', JURI::root(true). '/templates/' . $template);
            
            // Flag that informs that plugin is active
            define('JMF_EXEC', 'JMF');
            
            // Admin assets' URL
            define('JMF_ASSETS', JURI::root(true).'/plugins/system/ef4_jmframework/includes/assets/admin/');
            
            $this->loadLanguage();
            
            $this->template = $template;
            
            if ($app->isSite()) {
                require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'template.php';
                include_once JMF_TPL_PATH.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'jm_template.php';
                $className = false;
                if (class_exists('JMTemplate')) {
                    $className = 'JMTemplate';
                } else if (class_exists('JMTemplate'.ucfirst(str_replace('-', '', JMF_TPL)))) {
                    $className = 'JMTemplate'.ucfirst(str_replace('-', '', JMF_TPL));
                }
                
                $lang = JFactory::getLanguage();
            
                $lang->load('tpl_'.$this->template, JPATH_ADMINISTRATOR, 'en-GB', false, true)
                ||  $lang->load('tpl_'.$this->template, JMF_TPL_PATH, 'en-GB', false, true);
                
                $lang->load('tpl_'.$this->template, JPATH_ADMINISTRATOR, null, true, true)
                ||  $lang->load('tpl_'.$this->template, JMF_TPL_PATH, null, true, true);
                
                if ($className !== false) {
                    $doc = JFactory::getDocument();
                    if ($doc instanceof JDocumentHTML) {
                        $jmf = new $className($doc, true);
                        $jmf->ajax(); // check for ajax requests
                    }
                }
            } else {
                require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'template.php';
                require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'admin.php';
                $doc = JFactory::getDocument();
                $jmf = new JMFAdminTemplate($doc);
                $jmf->ajax(); // check for ajax requests 
            }
        }
    }
    
    /**
     * Loading template's language file 
     */
    function onAfterRender() {
        $app = JFactory::getApplication();
        if ($app->isAdmin() && $this->template) {
            $this->loadLanguage('tpl_'.$this->template, JPATH_ROOT);
        }
    }
    
    /**
     * Adding some scripts required in template's configuration 
     */    
    function onBeforeRender(){
        $app = JFactory::getApplication();
        $template = $this->getTemplateName();
        if ($template && ($app->isAdmin() || ($app->input->get('option') == 'com_config' && $app->input->get('view') == 'templates' ) )) {
            
            $document = JFactory::getDocument();
            
            if ($app->isAdmin()) {
                $document->addStyleSheet(JMF_ASSETS . 'css/admin.css');
            }
            $document->addScript(JMF_ASSETS . 'js/jmoptiongroups.js');
            $document->addScript(JMF_ASSETS . 'js/jmspacer.js');
            //$document->addScript(JMF_TPL_ASSETS . 'js/jmconfig.js');
            $document->addScript(JMF_ASSETS . 'js/jscolor.js');
            $document->addScript(JMF_ASSETS . 'js/misc.js');
            
            //$document->addScript('http://code.jquery.com/jquery-latest.js');
        }
        
    }

    /**
     * Here go all the actions that have to be performed right before document's HEAD has been rendered. 
     */
    function onBeforeCompileHead(){
        
        $app = JFactory::getApplication();
        $document = JFactory::getDocument();
        
        // Don't proceed when current template is not compatible with EF4 Framework or we are in the Joomla back-end
        if (empty($this->template) || $app->isAdmin()) {
            return true;
        }
        
        $params = $app->getTemplate(true)->params;
        
        // Handling Facebook's Open Graph
        if ((bool)$params->get('facebookOpenGraph', true)) {
            $fbAppId = $params->get('facebookOpenGraphAppID', false);
            $this->addOpenGraph($fbAppId);
        }
        
        // Removing obsolete CSS stylesheets
        $css_to_remove = $app->get('jm_remove_stylesheets', array());
        if (!empty($css_to_remove) && is_array($css_to_remove)) {
            foreach($document->_styleSheets as $url => $cssData) {
                foreach($css_to_remove as $oursUrl => $replacement) {
                    if (strpos($url, $oursUrl) !== false) {
                        unset($document->_styleSheets[$url]);
                        if ($replacement && is_array($replacement) && isset($replacement['url']) && isset($replacement['type'])) {
                            switch($replacement['type']) {
                                case 'css' : $document->addStyleSheet($replacement['url'], 'text/css'); break;
                                case 'less' : $document->addHeadLink($replacement['url'], 'stylesheet/less'); break;
                                default: break;
                            }
                        }
                    }
                }
            }
            $app->set('jm_remove_stylesheets', false);
        }
        
        // Don't compress CSS/JS when Development Mode or Joomla Debugging is enabled
        if($params->get('devmode',0) || JDEBUG || $app->input->get('option')=='com_config') { 
            return true;
        }
        
        // Preparing cache folder for CSS/JS compressed files
        if (JFolder::exists(JMF_TPL_PATH.DIRECTORY_SEPARATOR.'cache') == false) {
            if (!JFolder::create(JMF_TPL_PATH.DIRECTORY_SEPARATOR.'cache')) {
                if (JDEBUG) {
                    throw new Exception(JText::_('PLG_SYSTEM_JMFRAMEWORK_CACHE_FOLDER_NOT_ACCESSIBLE'));    
                } else {
                    return false;
                }
            }
        }
        
        $cssCompress = $params->get('cssCompress','0')=='1' ? true : false;
        $jsCompress = $params->get('jsCompress','0')=='1' ? true : false;
		
        // Handling CSS minifications and compression.
        if($cssCompress) {
            
            $styles = $document->_styleSheets;
            $compress = array();
            $mtime = 0;
            
            foreach($styles as $url => $style) {
                
                // Getting stylesheet path
                $path = $this->getPath($url);
                if(!$path || !JFile::exists($path)) continue;       
                
                // Getting the last modification time of stylesheet
                $ftime = filemtime($path);
                if($ftime > $mtime) $mtime = $ftime;
                
                $compress[$url] = $path;
            }
            
            $key = md5(serialize($compress));
            
            $stylepath = JPath::clean(JMF_TPL_PATH.'/cache/jmf_'.$key.'.css');
            $cachetime = JFile::exists($stylepath) ? filemtime($stylepath) : 0;
            $styleurl  = JMF_TPL_URL.'/cache/jmf_'.$key.'.css?t='.$cachetime;
            
            // Minify and merge stylesheets only if minified stylesheet isn't cached already or one of the stylesheets was modified
            if(!JFile::exists($stylepath) || $mtime > $cachetime) {
            	
                require_once JPath::clean(JMF_FRAMEWORK_PATH.'/includes/libraries/minify/CSSmin.php');
                $cssmin = new CSSmin();
                $css = array();
                $css[] = "/* Joomla-Monster EF4 Framework minify CSS";
                $css[] = " * --------------------------------------- */";
                
                foreach($compress as $url => $path) {
                    $src = JFile::read($path);
                    $src = $this->updateUrls($src, dirname($url));
                    $css[] = "\n/* src: ".$url." */";
                    $css[] = $cssmin->run($src, 1024);
                }
                
                $css = implode("\n", $css);
                JFile::write($stylepath, $css);
            }
            
            // Removing all merged stylesheets from the head and adding the minified stylesheet instead
            if(JFile::exists($stylepath)) {
                
                $newstyles = array();                
                $newstyles[$styleurl] = array('mime' => 'text/css', 'media' => null, 'attribs' => array());
                
                foreach ($styles as $url => $data) {
                    if(!array_key_exists($url, $compress)) $newstyles[$url] = $data;
                }
                
                $document->_styleSheets = $newstyles;
            }
            
        }
        
        // Handling JS minifications and compression.
        if($jsCompress) {
                
            $scripts = $document->_scripts;
            $compress = array();
            $mtime = 0;
            
            foreach($scripts as $url => $script) {
                
                // Getting script path
                $path = $this->getPath($url);
                if(!$path || !JFile::exists($path)) continue;       
                
                // Getting the last modification time of script
                $ftime = filemtime($path);
                if($ftime > $mtime) $mtime = $ftime;
                
                $compress[$url] = $path;
            }
            
            $key = md5(serialize($compress));
            
            $scriptpath = JPath::clean(JMF_TPL_PATH.'/cache/jmf_'.$key.'.js');
            $cachetime = JFile::exists($scriptpath) ? filemtime($scriptpath) : 0;
            $scripturl  = JMF_TPL_URL.'/cache/jmf_'.$key.'.js?t='.$cachetime;
            
            // Minify and merge scripts only if minified script isn't cached already or one of the scripts was modified
            if(!JFile::exists($scriptpath) || $mtime > $cachetime) {
                
            	require_once JPath::clean(JMF_FRAMEWORK_PATH.'/includes/libraries/minify/JSMin.php');
                
                $js = array();
                $js[] = "/* Joomla-Monster EF4 Framework minify JS";
                $js[] = " * -------------------------------------- */";
                
                foreach($compress as $url => $path) {
                    $src = JFile::read($path);
                    $js[] = "\n/* src: " . $url . " */";
                    $js[] = JSMin::minify($src).";";
                }
                
				$js = implode("\n", $js);
                JFile::write($scriptpath, $js);
            }
            
            // Removing all merged scripts from the head and adding the minified script instead
            if(JFile::exists($scriptpath)) {
                
                $newscripts = array();                
                $newscripts[$scripturl] = array('mime' => 'text/javascript', 'defer' => false, 'async' => false);
                
                foreach ($scripts as $url => $data) {
                    if(!array_key_exists($url, $compress)) $newscripts[$url] = $data;
                }
                
                $document->_scripts = $newscripts;
            }
            
        }
        
    }

    /*
     * Updating the URLs inside stylesheets for compatibility with minified stylesheet location
     */
    function updateUrls($src, $url){
        
        $app = JFactory::getApplication();
        
        // make sure url is root relative or absolute
        $url = ($url[0] === '/' || strpos($url, '://') !== false) ? $url : JURI::root(true) . '/' . $url;
        
        // replace image urls
        preg_match_all('/url\\(\\s*([^\\)\\s]+)\\s*\\)/', $src, $matches, PREG_SET_ORDER);
        
        foreach($matches as $match) {
            
            $uri = $match[1];
            
            if($uri[0] === "'" || $uri[0] === '"') {
                $uri = substr($uri, 1, strlen($uri) - 2);
            } 
            
            if ($uri[0] !== '/' && strpos($uri, '://') === false && strpos($uri, 'data:') !== 0) {
                
                $uri = $url . '/' . $uri;
                // replace the url
                $src = str_replace($match[0], "url('$uri')", $src);
            }
        }
        
        // replace imported stylesheet urls
        preg_match_all('/@import\\s+[\'"](.*?)[\'"]/', $src, $matches, PREG_SET_ORDER);
        
        foreach($matches as $match) {
            
            $uri = $match[1];
            
            if($uri[0] === "'" || $uri[0] === '"') {
                $uri = substr($uri, 1, strlen($uri) - 2);
            } 
            
            if ($uri[0] !== '/' && strpos($uri, '://') === false && strpos($uri, 'data:') !== 0) {
                
                $uri = $url . '/' . $uri;
                // replace the url
                $src = str_replace($match[0], "@import '$uri'", $src);
            }
        }
        
        return $src;
    }
	
    /**
     * Getting the fixed path to the CSS/JS file which is allowed to be merged
     */
    function getPath($url) {
        
        $app = JFactory::getApplication();
        $params = $app->getTemplate(true)->params;
        $skips = explode("\n", $params->get('skipCompress'));
        
        foreach($skips as $skip) {
            $skip = trim($skip);
            if(empty($skip)) continue;
            //$this->debug("URL: ".$url."\nSKIP: ".$skip."\nCMP: ".(strstr($url, $skip)!==false ? 'TRUE':'FALSE'));
            if(strstr($url, $skip)!==false) return false;
        }
        
        if(substr($url, 0, 2) === '//'){
            $url = JURI::getInstance()->getScheme() . ':' . $url; 
        }
        
        if (preg_match('/^https?\:/', $url)) {
            if (strpos($url, JURI::base()) === false){
                // external css
                return false;
            }
            $path = JPath::clean(JPATH_ROOT . '/' . substr($url, strlen(JURI::base())));
        } else {
            $path = JPath::clean(JPATH_ROOT . '/' . (JURI::root(true) && strpos($url, JURI::root(true)) === 0 ? substr($url, strlen(JURI::root(true))) : $url));
        }
        
        return is_file($path) ? $path : false;
    }
    
    /**
     * Establishing the current template in testing if it supports EF4 Framework
     */
    function getTemplateName() {
        $app = JFactory::getApplication();
        $template = false;
        if ($app->isSite()) {
            $template = $app->getTemplate(null);
        } else {
            $option = $app->input->get('option', null, 'string');
            $view = $app->input->get('view', null, 'string');
            $task = $app->input->get('task', '', 'string');
            $controller = current(explode('.',$task));
            $id = $app->input->get('id', null, 'int');
            if ($option == 'com_templates' && ($view == 'style' || $controller == 'style' || $task == 'apply' || $task == 'save' || $task == 'save2copy') && $id > 0) {
                $db = JFactory::getDbo();
                
                $query = $db->getQuery(true);
                
                $query->select('template');
                $query->from('#__template_styles');
                $query->where('id='.$id);
                
                $db->setQuery($query);
                $template = $db->loadResult();
            }
        }
        
        if ($template) {
            jimport('joomla.filesystem.file');
            $path = JPATH_ROOT.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'templateDetails.xml';
            if (JFile::exists($path)) {
                $xml = JInstaller::parseXMLInstallFile($path);
                if (array_key_exists('group', $xml)){
                    if ($xml['group'] == 'jmf-ef4') {
                        return $template;
                    }   
                }
            }
        }
        
        return false;
    }

    /**
     * Initialising JMFOpenGraph class
     */
    protected function addOpenGraph($appId = null) {
        require_once JMF_FRAMEWORK_PATH.JPath::clean('/includes/libraries/opengraph/opengraph.php');
        JMFOpenGraph::applyTags($appId);
    }
    
    /**
     * Utility class for quick debugging.
     */
    public static function debug($data, $exit = false, $type = 'warning'){
    
        $app = JFactory::getApplication();
        if($exit) {
            echo "JMF DEBUG:";
            echo  "<pre>".print_r($data,true)."</pre>";
            $app->close();
        } else {
            $app->enqueueMessage("<pre>JMF DEBUG:\n".print_r($data,true)."</pre>", $type);
        }
    }
}
