<?php
/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

defined('_JEXEC') or die;

?>

<?php if($this->checkModules('top-menu-nav')) : ?>
<section id="jm-djmenu-bar" class="<?php echo $this->getClass('block#djmenu') ?>">
    <div class="container-fluid">
            <nav id="jm-djmenu" class="clearfix">
                <jdoc:include type="modules" name="<?php echo $this->getPosition('top-menu-nav') ?>" style="raw"/>
            </nav>
    </div>
</section>
<?php endif; ?> 