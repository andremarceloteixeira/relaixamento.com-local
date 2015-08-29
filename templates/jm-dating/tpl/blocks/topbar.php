<?php
/*--------------------------------------------------------------
# Copyright (C) joomla-monster.com
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Website: http://www.joomla-monster.com
# Support: info@joomla-monster.com
---------------------------------------------------------------*/

defined('_JEXEC') or die;

//get logo and site description
$logo = htmlspecialchars($this->params->get('logo'));
$logotext = htmlspecialchars($this->params->get('logoText'));
$sitedescription = htmlspecialchars($this->params->get('siteDescription'));
$app = JFactory::getApplication();
$sitename = $app->getCfg('sitename');

?>

<?php if ($this->checkModules('top-bar') or ($logo != '') or ($logotext != '') or ($sitedescription != '')) : ?>
<section id="jm-top-bar">  
    <div class="container-fluid">
        <div id="jm-top-bar-in" class="clearfix">      
            <?php if (($logo != '') or ($logotext != '') or ($sitedescription != '')) : ?>
            <div id="jm-logo-sitedesc" class="pull-left">
                <?php if (($logo != '') or ($logotext != '')) : ?>
                <h1 id="jm-logo">
                    <a href="<?php echo JURI::base(); ?>" onfocus="blur()" >
                        <?php if ($logo != '') : ?>
                        <img src="<?php echo JURI::base(), $logo; ?>" alt="<?php if(!$logotext) { echo $sitename; } else { echo $logotext; }; ?>" border="0" />
                        <?php else : ?>
                        <?php echo '<span>'.$logotext.'</span>';?>
                        <?php endif; ?>
                    </a>
                </h1>
                <?php endif; ?>
                <?php if ($sitedescription != '') : ?>
                <div id="jm-sitedesc" class="pull-left">
                    <?php echo $sitedescription; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>                
            <?php if($this->checkModules('top-bar')) : ?>
   
                <jdoc:include type="modules" name="<?php echo $this->getPosition('top-bar'); ?>" style="jmmoduleraw"/>
     
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
