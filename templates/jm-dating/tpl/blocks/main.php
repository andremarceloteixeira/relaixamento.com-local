<?php
/*--------------------------------------------------------------
 # Copyright (C) joomla-monster.com
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

defined('_JEXEC') or die;

// get column's classes
$class = $this->params->get('class');
// set column's classes for other screens: getColumnClasses('screen', content, left, right);
$xtablet = $this->getColumnClasses('xtablet', 12, 6, 6);
$tablet = $this->getColumnClasses('tablet', 12, 6, 6);
$tablet = $this->getColumnClasses('mobile', 12, 6, 6);

//get information about font size switcher
$fontswitcher = $this->params->get('fontSizeSwitcher', '0');

//check component part
if($this->checkModules('breadcrumbs')
 OR ($this->displayComponent())
 OR ($this->checkModules('content-top'))
 OR ($this->checkModules('left-column'))
 OR ($this->checkModules('right-column'))
 OR ($this->checkModules('content-bottom'))) : ?>
 
<section id="jm-main">
	<div class="container-fluid">
		
		<?php if($this->checkModules('breadcrumbs')) : ?>
		<div class="row-fluid">
			<div id="jm-breadcrumbs" class="span12">
				<jdoc:include type="modules" name="<?php echo $this->getPosition('breadcrumbs'); ?>" style="raw" />
			</div>
		</div>
		<?php endif; ?>
		<div class="row-fluid">
			<div id="jm-content" class="<?php echo $class['content']; ?>" data-xtablet="<?php echo $xtablet['content']; ?>" data-tablet="<?php echo $tablet['content']; ?>">
				<?php if($this->checkModules('content-top')) : ?>
				<div id="jm-content-top">
					<?php echo $this->renderModules('content-top','jmmodule'); ?>
				</div>
				<?php endif; ?>
				<?php if ($this->displayComponent()) { ?>
				<div id="jm-maincontent">
					<?php if($fontswitcher) : ?>
					<div id="jm-font-switcher" class="text-right">
	                    <a href="javascript:void(0);" class="texttoggler small" rel="smallview" title="small size">A</a>
	                    <a href="javascript:void(0);" class="texttoggler normal" rel="normalview" title="normal size">A</a>
	                    <a href="javascript:void(0);" class="texttoggler large" rel="largeview" title="large size">A</a>						
	                    <script type="text/javascript">
	                    //documenttextsizer.setup("shared_css_class_of_toggler_controls")
	                    documenttextsizer.setup("texttoggler");
	                    </script>
					</div>
					<?php endif; ?>
					<jdoc:include type="component" />
				</div>
				<?php } ?>
				<?php if($this->checkModules('content-bottom')) : ?>
				<div id="jm-content-bottom">
					<?php echo $this->renderModules('content-bottom','jmmodule'); ?>
				</div>
				<?php endif; ?>
			</div>
			<?php if($this->checkModules('left-column')) : ?>
			<aside id="jm-left" class="<?php echo $class['left']; ?>" data-xtablet="<?php echo $xtablet['left']; ?>" data-tablet="<?php echo $tablet['left']; ?>">
				<?php echo $this->renderModules('left-column','jmmodule'); ?>
			</aside>
			<?php endif; ?>
			<?php if($this->checkModules('right-column')) : ?>
			<aside id="jm-right" class="<?php echo $class['right']; ?>" data-xtablet="<?php echo $xtablet['right']; ?>" data-tablet="<?php echo $tablet['right']; ?>">
				<?php echo $this->renderModules('right-column','jmmodule'); ?>
			</aside>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>