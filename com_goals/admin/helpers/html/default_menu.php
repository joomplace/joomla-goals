<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;
$app = JFactory::getApplication();

?>
<div id="navbar-example" class="navbar navbar-static navbar-inverse">
    <div class="navbar-inner">
        <div class="container" style="width: auto;">
            <a class="brand" href="<?php JRoute::_('index.php?option=com_goals') ?>"><img class="jp-panel-logo" src="<?php echo JURI::root() ?>administrator/components/com_goals/assets/images/joomplace-logo.png" /> <?php echo JText::_('JoomPlace')?></a>
            <ul class="nav" role="navigation">
                <li class="dropdown">
                    <a id="control-panel" href="index.php?option=com_goals&view=dashboard" role="button" class="dropdown-toggle">Control Panel</a>
                </li>
            </ul>
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse-goals">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <div class="nav-collapse-goals nav-collapse collapse">
                <ul class="nav" role="navigation">
                <li class="dropdown">
                    <a href="#" id="drop-customization" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_GOALS_MENU_SETTINGS') ?><b class="caret"></b></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="drop-customization">
                        <li class="dropdown"><a id="control-settings" href="<?php echo JRoute::_('index.php?option=com_config&view=component&component=com_goals&return='.base64_encode(JRoute::_('index.php?option=com_goals'))) ?>" role="button" class="dropdown-toggle"><?php echo JText::_('COM_GOALS_SUBMENU_SETTINGS');?></a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=groups"><?php echo JText::_('COM_GOALS_SUBMENU_CUSTOM_GROUPS');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=fields"><?php echo JText::_('COM_GOALS_SUBMENU_CATEGORY_CUSTOM_FIELDS');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=categories"><?php echo JText::_('COM_GOALS_SUBMENU_CATEGORIES');?></a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" id="drop-goals-manage" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo  JText::_('COM_GOALS_MENU_MANAGEMENT') ?><b class="caret"></b></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="drop-goals-manage">
                        <li role="presentation" class="nav-header"><?php echo JText::_('COM_GOALS_MENUHEADER_GOALS');?></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=goals"><?php echo JText::_('COM_GOALS_SUBMENU_GOALS');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=milistones"><?php echo JText::_('COM_GOALS_SUBMENU_MILISTONES');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=tasks"><?php echo JText::_('COM_GOALS_SUBMENU_TASKS');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=userfields"><?php echo JText::_('COM_GOALS_SUBMENU_GOALS_USER_CUSTOM_FIELDS');?></a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation" class="nav-header"><?php echo JText::_('COM_GOALS_MENUHEADER_PLANS');?></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=plans"><?php echo JText::_('COM_GOALS_SUBMENU_PLANS');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=stages"><?php echo JText::_('COM_GOALS_SUBMENU_STAGES');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=plantasks"><?php echo JText::_('COM_GOALS_SUBMENU_PLANTASKS');?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=planuserfields"><?php echo JText::_('COM_GOALS_SUBMENU_PLANS_USER_CUSTOM_FIELDS');?></a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation" class="nav-header"><?php echo JText::_('COM_GOALS_MENUHEADER_HABITS');?></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=habits"><?php echo JText::_('COM_GOALS_SUBMENU_HABITS');?></a></li>
                    </ul>
                </li>
                    <li class="dropdown">
                        <a href="#" id="drop-goals-templates" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo  JText::_('COM_GOALS_MENU_TEMPLATES') ?><b class="caret"></b></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="drop-goals-templates">
                            <li role="presentation" class="nav-header"><?php echo JText::_('COM_GOALS_MENUHEADER_GOALS');?></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=goalstemplates"><?php echo JText::_('COM_GOALS_SUBMENU_GOALSTEMPLATES');?></a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=milistonestemplates"><?php echo JText::_('COM_GOALS_SUBMENU_MILISTONES_TEMPLATES');?></a></li>
                            <li role="presentation" class="divider"></li>
                            <li role="presentation" class="nav-header"><?php echo JText::_('COM_GOALS_MENUHEADER_PLANS');?></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=planstemplates"><?php echo JText::_('COM_GOALS_SUBMENU_PLANS_TEMPLATES');?></a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=stagestemplates"><?php echo JText::_('COM_GOALS_SUBMENU_STAGES_TEMPLATES');?></a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=plantaskstemplates"><?php echo JText::_('COM_GOALS_SUBMENU_PLANTASKS_TEMPLATES');?></a></li>
                            <li role="presentation" class="divider"></li>
                            <li role="presentation" class="nav-header"><?php echo JText::_('COM_GOALS_MENUHEADER_HABITS');?></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?option=com_goals&view=habitstemplates"><?php echo JText::_('COM_GOALS_SUBMENU_HABITSTEMPLATES');?></a></li>
                        </ul>
                    </li>
            </ul>
            <ul class="nav pull-right">
                <li id="fat-menu" class="dropdown">
                    <a href="#" id="help" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_GOALS_SUBMENU_HELP') ?><b class="caret"></b></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="help">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://www.joomplace.com/video-tutorials-and-documentation/personal-goals-manager/" target="_blank"><?php echo JText::_('COM_GOALS_SUBMENU_HELP') ?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://www.joomplace.com/forum/joomla-components/goals.html" target="_blank"><?php echo JText::_('COM_GOALS_ADMINISTRATION_SUPPORT_FORUM') ?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://www.joomplace.com/support/helpdesk/" target="_blank"><?php echo JText::_('COM_GOALS_ADMINISTRATION_SUPPORT_DESC') ?></a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://www.joomplace.com/support/helpdesk/post-purchase-questions/ticket/create" target="_blank"><?php echo JText::_('COM_GOALS_ADMINISTRATION_SUPPORT_REQUEST') ?></a></li>
                    </ul>
                </li>
            </ul>
                </div>
        </div>
    </div>
</div>