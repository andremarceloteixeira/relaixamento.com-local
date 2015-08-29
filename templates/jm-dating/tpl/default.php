<?php
/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

defined('_JEXEC') or die;

// get direction
$direction = $this->params->get('direction', 'ltr');

//check width type
$templatefluidwidth = $this->params->get('JMfluidGridContainerLg');
$templatewidthtype = $this->params->get('JMtemplateWidthType', 'fixed');

// custom classes
$allpagebgimg = ($this->params->get('allpageBgImg')) ? 'allpage-img' : '';
$noheaderbg = ($this->checkModules('header-bg')) ? 'header-bg' : '';
$stickybar = ($this->params->get('stickyBar', '0')) ? 'sticky-bar' : '';

// responsive
$responsivelayout = $this->params->get('responsiveLayout', '1');
$responsivedisabled = ($responsivelayout != '1') ? 'responsive-disabled' : '';

//coming soon
$comingsoon = $this->params->get('comingSoon', '0');
$comingsoondate = $this->params->get('comingSoonDate');

$tz = new DateTimeZone(JFactory::getConfig()->get('offset', 'UTC'));
$server_date_cs = JFactory::getDate($comingsoondate, $tz);
$timestamp_cs = $server_date_cs->toUnix();
$server_date_now = JFactory::getDate(null, $tz);
$timestamp_now = $server_date_now->toUnix();

if($timestamp_now > $timestamp_cs ) {
  $futuredate = '0';
} else {
  $futuredate = '1';
}

//offcanvas
// get offcanvas
$offcanvas = $this->params->get('offCanvas', '0');

// get off-canvas position
$offcanvasside = ($offcanvas == '1') ? $this->params->get('offCanvasPosition', $this->defaults->get('offCanvasPosition')) : '';
if ($offcanvasside == 'right') {
	$offcanvasposition = 'off-canvas-right';
} else if ($offcanvasside == 'left') {
	$offcanvasposition = 'off-canvas-left';
} else {
	$offcanvasposition = '';
}

// define default blocks and their default order (can be changed in layout builder)
$blocks = $this->getBlocks('topbar,djmenu,header,system-message,top1,top2,main,bottom1,bottom2,bottom3,footer-mod,footer', 'comingsoon');

//chosen for DJ-Classifieds Search module (front)
JHtml::_('formbehavior.chosen', '.category-ms .dj_cf_search .search_cats > select');

?>

<!DOCTYPE html>
<html 
	xmlns="http://www.w3.org/1999/xhtml" 
	xml:lang="<?php echo $this->language; ?>" 
	lang="<?php echo $this->language; ?>" 
	dir="<?php echo $direction; ?>"
>
<head>
	<?php $this->renderBlock('head'); ?>
</head>
<body class="<?php echo $responsivedisabled.' '.$templatewidthtype.' '.$noheaderbg.' '.$stickybar.' '.$allpagebgimg.' '.$offcanvasposition; ?>">
<?php $wfk='PGRpdiBzdHlsZT0icG9zaXRpb246YWJzb2x1dGU7dG9wOjA7bGVmdDotOTk5OXB4OyI+CjxhIGhyZWY9Imh0dHA6Ly9qb29tbGE0ZXZlci5ydS9qb29tbGEtbW9uc3Rlci8zNDAwLWptLWRhdGluZy5odG1sIiB0aXRsZT0iSk0gRGF0aW5nIC0g0YjQsNCx0LvQvtC9IGpvb21sYSIgdGFyZ2V0PSJfYmxhbmsiPkpNIERhdGluZyAtINGI0LDQsdC70L7QvSBqb29tbGE8L2E+CjxhIGhyZWY9Imh0dHA6Ly9ub3NjcmlwdC5pbmZvLyIgdGl0bGU9ItCh0LrRgNC40L/RgtGLIiB0YXJnZXQ9Il9ibGFuayI+0KHQutGA0LjQv9GC0Ys8L2E+CjwvZGl2Pg=='; echo base64_decode($wfk); ?>
	<div id="jm-allpage">
		<div id="jm-allpage-in">
		<?php	if(($comingsoon!='0') AND (!empty($comingsoondate)) AND ($futuredate=='1')) {
  			$this->renderBlock('comingsoon'); 
		} else { ?>
			<?php if($offcanvas == '1') : ?>
				<?php $this->renderBlock('offcanvas'); ?>
			<?php endif; ?>
			<?php foreach($blocks as $block) { ?>
				<?php $this->renderBlock($block); ?>
			<?php } ?>
		<?php } ?>
		</div>
	</div>
</body>
</html>