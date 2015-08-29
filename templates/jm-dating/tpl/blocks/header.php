<?php
/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

defined('_JEXEC') or die;
?>

<?php if ($this->checkModules('header') or $this->checkModules('header-bg')) : ?>
<section id="jm-header" class="<?php echo $this->getClass('block#header') ?>">
    <?php if ($this->checkModules('header-bg')) : ?>
    <div id="jm-header-bg">   
      <jdoc:include type="modules" name="<?php echo $this->getPosition('header-bg'); ?>" style="jmmodule"/>
    </div> 
    <?php endif; ?>
    <?php if ($this->checkModules('header')) : ?>
    <div id="jm-header-content" class="container-fluid">
      <div id="jm-header-content-in" class="clearfix">	
      	<jdoc:include type="modules" name="<?php echo $this->getPosition('header'); ?>" style="jmmodule"/>
      </div>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>
