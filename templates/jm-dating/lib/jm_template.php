<?php

/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

defined('_JEXEC') or die;

class JMTemplate extends JMFTemplate {
	public function postSetUp() {
		// get columns classes
		$s = $this->getLayoutConfig('#scheme','lcr');
		$l = $this->params->get('columnLeftWidth', '3');
		$r = $this->params->get('columnRightWidth', '3');
				
		if ((!$this->checkModules('left-column')) && (!$this->checkModules('right-column'))) {
            $c = 12;
			$s = str_replace(array('l','r'), '', $s);
        } else if (($this->checkModules('left-column')) && (!$this->checkModules('right-column'))) {
            $c = 12 - $l;
			$s = str_replace(array('r'), '', $s);
        } else if ((!$this->checkModules('left-column')) && ($this->checkModules('right-column'))) {
            $c = 12 - $r;
			$l = $r;
			$s = str_replace(array('l'), '', $s);
        } else {
            $c = 12 - $l - $r;
        }
		
		// get classes for columns		
		$class = $this->getColumnClasses($s, $c, $l, $r);
		
		$this->params->set('class', $class);	
		
		$bootstrap_vars = array();
		
		/* Template Layout */
		//$parametr = $this->params->get('parametr', $this->defaults->get('parametr'));
		
		$templatefluidwidth = $this->params->get('JMfluidGridContainerLg', $this->defaults->get('JMfluidGridContainerLg'));
		$bootstrap_vars['JMfluidGridContainerLg'] = $templatefluidwidth;
		
		//check type
		$checkwidthtype = strstr($templatefluidwidth, '%');
		$checkwidthtypevalue = ($checkwidthtype) ? 'fluid' : 'fixed';
		$bootstrap_vars['JMtemplateWidthType'] = $checkwidthtypevalue;
		$templatewidthtype = $this->params->set('JMtemplateWidthType', $checkwidthtypevalue);
		
		$gutterwidth = $this->params->get('JMbaseSpace', $this->defaults->get('JMbaseSpace'));
		$bootstrap_vars['JMbaseSpace'] = $gutterwidth;
		
		//offcanvas
		$offcanvaswidth = $this->params->get('JMoffCanvasWidth', $this->defaults->get('JMoffCanvasWidth'));
		$bootstrap_vars['JMoffCanvasWidth'] = $offcanvaswidth;

        /* Font Modifications */
        
        //body
        
        $bodyfontsize = (int)$this->params->get('JMbaseFontSize', $this->defaults->get('JMbaseFontSize'));
		$bootstrap_vars['JMbaseFontSize'] = $bodyfontsize.'px';
		
        $bodyfonttype = $this->params->get('bodyFontType', '1');
        $bodyfontfamily = $this->params->get('bodyFontFamily', $this->defaults->get('bodyFontFamily')); 
        $bodygooglewebfontfamily = $this->params->get("bodyGoogleWebFontFamily", $this->defaults->get('bodyGoogleWebFontFamily'));
		$bodygooglewebfonturl = $this->params->get('bodyGoogleWebFontUrl');
        $generatedwebfontfamily = $this->params->get('bodyGeneratedWebFont');

        switch($bodyfonttype) {
            case "0" : {
                $bootstrap_vars['JMbaseFontFamily'] = $bodyfontfamily;
                break;    
            }
        	case "1" :{
                $bootstrap_vars['JMbaseFontFamily'] = $bodygooglewebfontfamily;
                break;
            }
            case "2" :{
            	$bootstrap_vars['JMbaseFontFamily'] = $generatedwebfontfamily;
            	break;
            }
            default: {
                $bootstrap_vars['JMbaseFontFamily'] = $this->defaults->get('bodyGoogleWebFontFamily');
                break;
            }
       	}
	   
		//top menu horizontal
		
		$djmenufontsize = (int)$this->params->get('JMtopmenuFontSize', $this->defaults->get('JMtopmenuFontSize'));
		$bootstrap_vars['JMtopmenuFontSize'] = $djmenufontsize.'px';
		
		$djmenufonttype = $this->params->get('djmenuFontType', '1');
		$djmenufontfamily = $this->params->get('djmenuFontFamily', $this->defaults->get('djmenuFontFamily'));
		$djmenugooglewebfontfamily = $this->params->get("djmenuGoogleWebFontFamily", $this->defaults->get('djmenuGoogleWebFontFamily'));
		$djmenugeneratedwebfontfamily = $this->params->get('djmenuGeneratedWebFont');
		
        switch($djmenufonttype) {
            case "0" : {
                $bootstrap_vars['JMtopmenuFontFamily'] = $djmenufontfamily;
                break;    
            }
            case "1" :{
                $bootstrap_vars['JMtopmenuFontFamily'] = $djmenugooglewebfontfamily;
                break;
            }
            case "2" :{
            	$bootstrap_vars['JMtopmenuFontFamily'] = $djmenugeneratedwebfontfamily;
            	break;
            }
            default: {
                $bootstrap_vars['JMtopmenuFontFamily'] = $this->defaults->get('djmenuGoogleWebFontFamily');
                break;
            }
       	}
       	
       	//module title
	   	
	 	$headingsfontsize = (int)$this->params->get('JMmoduleTitleFontSize', $this->defaults->get('JMmoduleTitleFontSize'));
		$bootstrap_vars['JMmoduleTitleFontSize'] = $headingsfontsize.'px';
		
		$headingsfonttype = $this->params->get('headingsFontType', '1');
		$headingsfontfamily = $this->params->get('headingsFontFamily', $this->defaults->get('headingsFontFamily')); 
		$headingsgooglewebfontfamily = $this->params->get("headingsGoogleWebFontFamily", $this->defaults->get('headingsGoogleWebFontFamily'));
		$headingsgeneratedwebfontfamily = $this->params->get('headingsGeneratedWebFont');
		
        switch($headingsfonttype) {
            case "0" : {
                $bootstrap_vars['JMmoduleTitleFontFamily'] = $headingsfontfamily;
                break;    
            }
            case "1" :{
                $bootstrap_vars['JMmoduleTitleFontFamily'] = $headingsgooglewebfontfamily;
                break;
            }
            case "2" :{
            	$bootstrap_vars['JMmoduleTitleFontFamily'] = $headingsgeneratedwebfontfamily;
            	break;
            }
            default: {
                $bootstrap_vars['JMmoduleTitleFontFamily'] = $this->defaults->get('headingsGoogleWebFontFamily');
                break;
            }
       	}
		
       	//article title
		
		$articlesfontsize = (int)$this->params->get('JMarticleTitleFontSize', $this->defaults->get('JMarticleTitleFontSize'));
		$bootstrap_vars['JMarticleTitleFontSize'] = $articlesfontsize.'px';
		
		$articlesfonttype = $this->params->get('articlesFontType', '1');
		$articlesfontfamily = $this->params->get('articlesFontFamily', $this->defaults->get('articlesFontFamily'));
		$articlesgooglewebfontfamily = $this->params->get("articlesGoogleWebFontFamily", $this->defaults->get('articlesGoogleWebFontFamily'));
		$articlesgeneratedfontfamily = $this->params->get('articlesGeneratedWebFont');
		
        switch($articlesfonttype) {
            case "0" : {
                $bootstrap_vars['JMarticleTitleFontFamily'] = $articlesfontfamily;
                break;    
            }
            case "1" :{
                $bootstrap_vars['JMarticleTitleFontFamily'] = $articlesgooglewebfontfamily;
                break;
            }
            case "2" :{
            	$bootstrap_vars['JMarticleTitleFontFamily'] = $articlesgeneratedfontfamily;
            	break;
            }
            default: {
                $bootstrap_vars['JMarticleTitleFontFamily'] = $this->defaults->get('articlesGoogleWebFontFamily');
                break;
            }
       	}
       	
       	
		
	    /* Color Modifications */
	    
	    //scheme color
        $colorversion = $this->params->get('JMcolorVersion', $this->defaults->get('JMcolorVersion')); 
		$bootstrap_vars['JMcolorVersion'] = $colorversion;

		//scheme images directory
		$imagesdir = $this->params->get('JMimagesDir', 'scheme1');
		$bootstrap_vars['JMimagesDir'] = $imagesdir;

		//custom variables
		
		// -------------------------------------
		// global
		// -------------------------------------
		
		//page background
		$JMallpageBackground = $this->params->get('JMallpageBackground', $this->defaults->get('JMallpageBackground')); 
		$bootstrap_vars['JMallpageBackground'] = $JMallpageBackground;
		
		//base font color
		$bodyfontcolor = $this->params->get('JMbaseFontColor', $this->defaults->get('JMbaseFontColor')); 
		$bootstrap_vars['JMbaseFontColor'] = $bodyfontcolor;
		
		//border
		$JMborder = $this->params->get('JMborder', $this->defaults->get('JMborder')); 
		$bootstrap_vars['JMborder'] = $JMborder;
		
		//headings
		$JMheadingColor = $this->params->get('JMheadingColor', $this->defaults->get('JMheadingColor')); 
		$bootstrap_vars['JMheadingColor'] = $JMheadingColor;
		
		// -------------------------------------
		// topbar
		// -------------------------------------
		
		//background
		$JMtopbarBackground = $this->params->get('JMtopbarBackground', $this->defaults->get('JMtopbarBackground')); 
		$bootstrap_vars['JMtopbarBackground'] = $JMtopbarBackground;
		
		//font color
		$JMtopbarFontColor = $this->params->get('JMtopbarFontColor', $this->defaults->get('JMtopbarFontColor')); 
		$bootstrap_vars['JMtopbarFontColor'] = $JMtopbarFontColor;
		
		// -------------------------------------
		// dj-menu
		// -------------------------------------
		
		//background
		$JMmegamenuBackground = $this->params->get('JMmegamenuBackground', $this->defaults->get('JMmegamenuBackground')); 
		$bootstrap_vars['JMmegamenuBackground'] = $JMmegamenuBackground;

		//font color
		$JMmegamenuFontColor = $this->params->get('JMmegamenuFontColor', $this->defaults->get('JMmegamenuFontColor')); 
		$bootstrap_vars['JMmegamenuFontColor'] = $JMmegamenuFontColor;		
		
		//SUBMENU
		//background
		$JMmegamenuSubmenuBackground = $this->params->get('JMmegamenuSubmenuBackground', $this->defaults->get('JMmegamenuSubmenuBackground')); 
		$bootstrap_vars['JMmegamenuSubmenuBackground'] = $JMmegamenuSubmenuBackground;
		
		//border
		$JMmegamenuSubmenuBorder = $this->params->get('JMmegamenuSubmenuBorder', $this->defaults->get('JMmegamenuSubmenuBorder')); 
		$bootstrap_vars['JMmegamenuSubmenuBorder'] = $JMmegamenuSubmenuBorder;
		
		//font color
		$JMmegamenuSubmenuFontColor = $this->params->get('JMmegamenuSubmenuFontColor', $this->defaults->get('JMmegamenuSubmenuFontColor')); 
		$bootstrap_vars['JMmegamenuSubmenuFontColor'] = $JMmegamenuSubmenuFontColor;	
		
		
		
		// -------------------------------------
		// top2 and bottom2
		// -------------------------------------
		
		//background
		$JMtop2Background = $this->params->get('JMtop2Background', $this->defaults->get('JMtop2Background')); 
		$bootstrap_vars['JMtop2Background'] = $JMtop2Background;
		
		//border
		$JMtop2Border = $this->params->get('JMtop2Border', $this->defaults->get('JMtop2Border')); 
		$bootstrap_vars['JMtop2Border'] = $JMtop2Border;
		
		//font color
		$JMtop2FontColor = $this->params->get('JMtop2FontColor', $this->defaults->get('JMtop2FontColor')); 
		$bootstrap_vars['JMtop2FontColor'] = $JMtop2FontColor;

		//module title font color
		$JMtop2ModuleTitleFontColor = $this->params->get('JMtop2ModuleTitleFontColor', $this->defaults->get('JMtop2ModuleTitleFontColor')); 
		$bootstrap_vars['JMtop2ModuleTitleFontColor'] = $JMtop2ModuleTitleFontColor;

		// -------------------------------------
		// modules
		// -------------------------------------
		
		//module title
		$JMmoduleTitleColor = $this->params->get('JMmoduleTitleColor', $this->defaults->get('JMmoduleTitleColor')); 
		$bootstrap_vars['JMmoduleTitleColor'] = $JMmoduleTitleColor;
		
		//module title borders
		$JMtitleBorderModuleBorderColor = $this->params->get('JMtitleBorderModuleBorderColor', $this->defaults->get('JMtitleBorderModuleBorderColor')); 
		$bootstrap_vars['JMtitleBorderModuleBorderColor'] = $JMtitleBorderModuleBorderColor;
		
		//white-ms font color
		$JMwhiteModuleFontColor = $this->params->get('JMwhiteModuleFontColor', $this->defaults->get('JMwhiteModuleFontColor')); 
		$bootstrap_vars['JMwhiteModuleFontColor'] = $JMwhiteModuleFontColor;
		
		//white-ms module title
		$JMwhiteModuleTitleColor = $this->params->get('JMwhiteModuleTitleColor', $this->defaults->get('JMwhiteModuleTitleColor')); 
		$bootstrap_vars['JMwhiteModuleTitleColor'] = $JMwhiteModuleTitleColor;
		
		//white-ms background
		$JMwhiteModuleBackground = $this->params->get('JMwhiteModuleBackground', $this->defaults->get('JMwhiteModuleBackground')); 
		$bootstrap_vars['JMwhiteModuleBackground'] = $JMwhiteModuleBackground;
		
		//white-ms border
		$JMwhiteModuleBorderColor = $this->params->get('JMwhiteModuleBorderColor', $this->defaults->get('JMwhiteModuleBorderColor')); 
		$bootstrap_vars['JMwhiteModuleBorderColor'] = $JMwhiteModuleBorderColor;
		
		// -------------------------------------
		// footer
		// -------------------------------------
		
		//modules background
		$JMfootermodBackground = $this->params->get('JMfootermodBackground', $this->defaults->get('JMfootermodBackground')); 
		$bootstrap_vars['JMfootermodBackground'] = $JMfootermodBackground;
		
		//footer module font color
		$JMfootermodFontColor = $this->params->get('JMfootermodFontColor', $this->defaults->get('JMfootermodFontColor')); 
		$bootstrap_vars['JMfootermodFontColor'] = $JMfootermodFontColor;
		
		//module title font color
		$JMfootermodModuleTitleFontColor = $this->params->get('JMfootermodModuleTitleFontColor', $this->defaults->get('JMfootermodModuleTitleFontColor')); 
		$bootstrap_vars['JMfootermodModuleTitleFontColor'] = $JMfootermodModuleTitleFontColor;
		
		// -------------------------------------
		// extensions
		// -------------------------------------
		
		//mediatools title font color
		$JMmediatoolsTitleFontColor = $this->params->get('JMmediatoolsTitleFontColor', $this->defaults->get('JMmediatoolsTitleFontColor')); 
		$bootstrap_vars['JMmediatoolsTitleFontColor'] = $JMmediatoolsTitleFontColor;
		
		//mediatools description background
		$JMmediatoolsDescriptionBackgroundColor = $this->params->get('JMmediatoolsDescriptionBackgroundColor', $this->defaults->get('JMmediatoolsDescriptionBackgroundColor')); 
		$bootstrap_vars['JMmediatoolsDescriptionBackgroundColor'] = $JMmediatoolsDescriptionBackgroundColor;
		
		//mediatools description font color
		$JMmediatoolsDescriptionFontColor = $this->params->get('JMmediatoolsDescriptionFontColor', $this->defaults->get('JMmediatoolsDescriptionFontColor')); 
		$bootstrap_vars['JMmediatoolsDescriptionFontColor'] = $JMmediatoolsDescriptionFontColor;
		
		// -------------------------------------
		// offcanvas
		// -------------------------------------
        $offcanvasbackground = $this->params->get('JMoffCanvasBackground', $this->defaults->get('JMoffCanvasBackground')); 
		$bootstrap_vars['JMoffCanvasBackground'] = $offcanvasbackground;
		
        $offcanvasfontcolor = $this->params->get('JMoffCanvasFontColor', $this->defaults->get('JMoffCanvasFontColor')); 
		$bootstrap_vars['JMoffCanvasFontColor'] = $offcanvasfontcolor;	
		
		// -------------------------------------
		// end 
		// -------------------------------------
       	$this->params->set('jm_bootstrap_variables', $bootstrap_vars);
	
		// -------------------------------------
		// compile LESS
		// -------------------------------------

		// Offline Page
		$this->CompileStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/offline.less'), true);
		
		// DJ-Classifieds
		$djclassifieds_theme = $this->CompileStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/djclassifieds.less'), true, true);
		$djclassifieds_theme_rtl = $this->CompileStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/djclassifieds_rtl.less'), true, true);
		$djclassifieds_responsive = $this->CompileStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/djclassifieds_responsive.less'), true, true);
		
		// DJ-Megamenu
		$djmegamenu_theme = $this->CompileStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/djmegamenu.less'), true, true);
		
		// -------------------------------------
		// extensions themes
		// -------------------------------------	

        $app = JFactory::getApplication();		
		$themer = (int)$this->params->get('themermode', 0) == 1 ? true : false;
        if ($themer) { // add LESS files when Theme Customizer enabled
                
            $urlsToRemove = array(
            'templates/jm-dating/css/djmegamenu.css' => array('url' => 'templates/'.$app->getTemplate().'/less/djmegamenu.less', 'type' => 'less'),
            'components/com_djclassifieds/themes/jm-dating/css/style.css' => array('url' => 'templates/'.$app->getTemplate().'/less/djclassifieds.less', 'type' => 'less'),
            'components/com_djclassifieds/themes/jm-dating/css/style_rtl.css' => array('url' => 'templates/'.$app->getTemplate().'/less/djclassifieds_rtl.less', 'type' => 'less'),
            'components/com_djclassifieds/themes/jm-dating/css/responsive.css' => array('url' => 'templates/'.$app->getTemplate().'/less/djclassifieds_responsive.less', 'type' => 'less')
            );
            $app->set('jm_remove_stylesheets', $urlsToRemove);
        } else { // add CSS files when Theme Customizer disabled 
            $urlsToRemove = array(
            'templates/jm-dating/css/djmegamenu.css' => array('url' => $djmegamenu_theme, 'type' => 'css'),
            'components/com_djclassifieds/themes/jm-dating/css/style.css' => array('url' => $djclassifieds_theme, 'type' => 'css'),
            'components/com_djclassifieds/themes/jm-dating/css/style_rtl.css' => array('url' => $djclassifieds_theme_rtl, 'type' => 'css'),
            'components/com_djclassifieds/themes/jm-dating/css/responsive.css' => array('url' => $djclassifieds_responsive, 'type' => 'css')
            );
            $app->set('jm_remove_stylesheets', $urlsToRemove);
        }
    }
}

