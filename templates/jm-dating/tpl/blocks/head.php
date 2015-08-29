<?php
/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

defined('_JEXEC') or die;

$themer = (int)$this->params->get('themermode', 0) == 1 ? true:false;
$devmode = (int)$this->params->get('devmode', 0) == 1 ? true:false;

// get direction
$direction = $this->params->get('direction', 'ltr');

// get information about responsive layout
$responsivelayout = $this->params->get('responsiveLayout', '1');

// get coming soon
$comingsoon = $this->params->get('comingSoon', '0');
$comingsoondate = $this->params->get('comingSoonDate');

//get off-canvas 
$offcanvas = $this->params->get('offCanvas', '0');

$google_font_urls = array();
$generated_fonts = array();

// get google web font url for body font
$bodyfonttype = $this->params->get('bodyFontType', '1');
$bodygooglewebfonturl = htmlspecialchars($this->params->get('bodyGoogleWebFontUrl', $this->defaults->get('bodyGoogleWebFontUrl')));
$bodygeneratedwebfont = htmlspecialchars($this->params->get('bodyGeneratedWebFont'));

$google_font_urls[] = ($bodyfonttype == '1') ? $bodygooglewebfonturl : false;
$generated_fonts[] = ($bodyfonttype == '2' || $themer || $devmode) ? $bodygeneratedwebfont : false;

// get google web font url for module headings
$headingsfonttype = $this->params->get('headingsFontType', '1');
$headingsgooglewebfonturl = htmlspecialchars($this->params->get('headingsGoogleWebFontUrl', $this->defaults->get('headingsGoogleWebFontUrl')));
$headingsgeneratedwebfont = htmlspecialchars($this->params->get('headingsGeneratedWebFont'));

$google_font_urls[] = ($headingsfonttype == '1') ? $headingsgooglewebfonturl : false;
$generated_fonts[] = ($headingsfonttype == '2' || $themer || $devmode) ? $headingsgeneratedwebfont : false;

// get google web font url for article headings
$articlesfonttype = $this->params->get('articlesFontType', '1');
$articlesgooglewebfonturl = htmlspecialchars($this->params->get('articlesGoogleWebFontUrl', $this->defaults->get('articlesGoogleWebFontUrl')));
$articlesgeneratedwebfont = htmlspecialchars($this->params->get('articlesGeneratedWebFont'));

$google_font_urls[] = ($articlesfonttype == '1') ? $articlesgooglewebfonturl : false;
$generated_fonts[] = ($articlesfonttype == '2' || $themer || $devmode) ? $articlesgeneratedwebfont : false;

// get google web font url for dj-menu
$djmenufonttype = $this->params->get('djmenuFontType', '1');
$djmenugooglewebfonturl = htmlspecialchars($this->params->get('djmenuGoogleWebFontUrl', $this->defaults->get('djmenuGoogleWebFontUrl')));
$djmenugeneratedwebfont = htmlspecialchars($this->params->get('djmenuGeneratedWebFont'));

$google_font_urls[] = ($djmenufonttype == '1') ? $djmenugooglewebfonturl : false;
$generated_fonts[] = ($djmenufonttype == '2' || $themer || $devmode) ? $djmenugeneratedwebfont : false;

// get google web font url for advanced selectors
$advancedfonttype = $this->params->get('advancedFontType', '0');
$advancedgooglewebfonturl = htmlspecialchars($this->params->get('advancedGoogleWebFontUrl'));
$advancedgeneratedwebfont = htmlspecialchars($this->params->get('advancedGeneratedWebFont'));

$google_font_urls[] = ($advancedfonttype == '1') ? $advancedgooglewebfonturl : false;
$generated_fonts[] = ($advancedfonttype == '2' || $themer || $devmode) ? $advancedgeneratedwebfont : false;

// get favicon
$faviconimg = $this->params->get('favIconImg');

define('JMF_TH_TEMPLATE', $themer);
define('JMF_TH_BOOTSTRAP', $themer);

?>
	
<?php if ($responsivelayout == "1") { 
// viewport fix for devices
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<?php } ?>

<?php 
// load core head
?>
<jdoc:include type="head" />

<?php

// load bootstrap css
if ($direction == 'rtl') {
	$this->addCompiledStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/bootstrap_rtl.less'), true, JMF_TH_BOOTSTRAP);
	if ($responsivelayout == "1") {
		$this->addCompiledStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/bootstrap_responsive_rtl.less'), true, JMF_TH_BOOTSTRAP);
	}
} else {
	$this->addCompiledStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/bootstrap.less'), true, JMF_TH_BOOTSTRAP);
	if ($responsivelayout == "1") {
		$this->addCompiledStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/bootstrap_responsive.less'), true, JMF_TH_BOOTSTRAP);
	}
}

// load template css
$this->addCompiledStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/template.less'), true, JMF_TH_TEMPLATE);

// load offcanvas styles
if ($offcanvas == "1") {
	$this->addCompiledStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/offcanvas.less'), true, JMF_TH_TEMPLATE);
}
		
// load RTL styles
if ($direction == 'rtl') {
	$this->addCompiledStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/template_rtl.less'), true, JMF_TH_TEMPLATE);
}

//load coming-soon
if(($comingsoon!='0') AND (!empty($comingsoondate))) {
	$this->addCompiledStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/comingsoon.less'), true, JMF_TH_TEMPLATE);
}

// load responsive styles
if ($responsivelayout == "1") {
	$this->addCompiledStyleSheet(JPath::clean(JMF_TPL_PATH.'/less/template_responsive.less'), true, JMF_TH_TEMPLATE);
}

// load custom styles
$this->document->addStyleSheet(JMF_TPL_URL.'/'.'css'.'/'.'custom.css'); 		

if (!empty($google_font_urls)) {
	$google_font_urls = array_unique($google_font_urls);
	foreach($google_font_urls as $google_font) {
		if ($google_font) {
			$this->addStyleSheet($google_font);
		}
	}	
}

if (!empty($generated_fonts)) {
	$generated_fonts = array_unique($generated_fonts);
	foreach ($generated_fonts as $generated_font) {
		if ($generated_font) {
			$this->addGeneratedFont($generated_font);
		}
	}	
}

// load bootstrap scripts
JHtml::_('bootstrap.framework');

// load template scripts
$this->addScript(JMF_TPL_URL.'/'.'js'.'/'.'scripts.js');

// cache custom css
if ($url = $this->cacheStyleSheet('template_params.php')) {
	$this->document->addStyleSheet($url);
}
?>	 

<!--[if IE 9]>
<link href="<?php echo JMF_TPL_URL ?>/css/ie9.css" rel="stylesheet" type="text/css" />
<![endif]-->

<?php
	
// load favicon
if ($faviconimg) { ?>
	<link href="<?php echo JURI::base().$faviconimg; ?>" rel="Shortcut Icon" />
<?php } else { ?>
	<link href="<?php echo JMF_TPL_URL ?>/images/favicon.ico" rel="Shortcut Icon" />
<?php } ?>

<script type="text/javascript">
window.advancedSearch = '<?php echo addslashes(JText::_( 'PLG_SYSTEM_JMFRAMEWORK_CONFIG_ADVANCED_SEARCH_LABEL' ));?>';
<?php if ($direction == 'rtl') { ?>
window.optionsDir = { direction: 'left' };
<?php } else { ?>
window.optionsDir = { direction: 'right' };
<?php } ?>
</script>

<?php 
// load injection code
echo $this->params->get('codeInjection');
 ?>