<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
JLoader::register('GoalsHelperFE', JPATH_COMPONENT . '/helpers/goals.php');
JLoader::register('GoalsHelperRoute', JPATH_COMPONENT . '/helpers/route.php');
GoalsHelperRoute::bakeBread('','');

global $viewed_user;
global $viewer_user;
$viewed_user = $viewer_user = JFactory::getUser();

$document = JFactory::getDocument();
$componentParams = &JComponentHelper::getParams('com_goals');
$param = $componentParams->get('include_local_bootstrap', 0); 
if($param || JFactory::getApplication()->input->get('tmpl','')=='component'){
	$document->addStyleSheet(JURI::root().'components/com_goals/assets/css/local_bootstrap.css');
}
$task = JFactory::getApplication()->input->get('task');
if(!strpos($task,'Graph')) echo '<div id="goals-wrap">';
if(JFactory::getApplication()->input->get('ranged')){
?>
    <link rel="stylesheet" href="<?php echo JURI::root().'components/com_goals/assets/css/local_bootstrap.css'; ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo JURI::root().'components/com_goals/assets/css/style.css'; ?>" type="text/css">
    <style>
        .dialog-body #goals-wrap{
            min-width: 390px;
        }
        #goals-wrap .goal-item-actions{
            padding-top: 10px;
        }
        #goals-wrap .goals-item .goal-item-progress .progress.progress-small.progress-striped{
            height: 7px !important;
            margin: 0px 0px 5px !important;
        }
        #goals-wrap .navbar-inner {
            min-height: 40px;
            padding-left: 20px;
            padding-right: 20px;
            background-color: #fafafa;
            background-image: -moz-linear-gradient(top,#ffffff,#f2f2f2);
            background-image: -webkit-gradient(linear,0 0,0 100%,from(#ffffff),to(#f2f2f2));
            background-image: -webkit-linear-gradient(top,#ffffff,#f2f2f2);
            background-image: -o-linear-gradient(top,#ffffff,#f2f2f2);
            background-image: linear-gradient(to bottom,#ffffff,#f2f2f2);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#fff2f2f2', GradientType=0);
            border: 1px solid #d4d4d4;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            -webkit-box-shadow: 0 1px 4px rgba(0,0,0,0.065);
            -moz-box-shadow: 0 1px 4px rgba(0,0,0,0.065);
            box-shadow: 0 1px 4px rgba(0,0,0,0.065);
        }
        #goals-wrap .navbar .nav > li > a:hover, #goals-wrap .navbar .nav > li > a:focus{
            color: #3a87ad;
        }
        #goals-wrap .navbar .nav > li > .dropdown-menu > li >  a:hover, #goals-wrap .navbar .nav > li > .dropdown-menu > li >  a:focus {
            color: #FFF;
        }
        #goals-wrap .navbar .brand {
            font-size: 16px;
        }
        @media (max-width: 979px){
			#goals-wrap .navbar .nav > li > a:hover, #goals-wrap .navbar .nav > li > a:focus{
				color: #3a87ad;
			}
			#goals-wrap .navbar .nav > li > .dropdown-menu > li >  a:hover, #goals-wrap .navbar .nav > li > .dropdown-menu > li >  a:focus {
				color: #FFF;
			}
        }
    </style>
<?php
}
$document->addStyleSheet(JURI::root().'components/com_goals/assets/css/style.css');
$document->addStyleDeclaration("
        #goals-wrap .navbar-inner {
            min-height: 40px;
            padding-left: 20px;
            padding-right: 20px;
            background-color: #fafafa;
            background-image: -moz-linear-gradient(top,#ffffff,#f2f2f2);
            background-image: -webkit-gradient(linear,0 0,0 100%,from(#ffffff),to(#f2f2f2));
            background-image: -webkit-linear-gradient(top,#ffffff,#f2f2f2);
            background-image: -o-linear-gradient(top,#ffffff,#f2f2f2);
            background-image: linear-gradient(to bottom,#ffffff,#f2f2f2);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#fff2f2f2', GradientType=0);
            border: 1px solid #d4d4d4;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            -webkit-box-shadow: 0 1px 4px rgba(0,0,0,0.065);
            -moz-box-shadow: 0 1px 4px rgba(0,0,0,0.065);
            box-shadow: 0 1px 4px rgba(0,0,0,0.065);
        }
        #goals-wrap .navbar .nav > li > a:hover, #goals-wrap .navbar .nav > li > a:focus{
            color: #3a87ad;
        }
        #goals-wrap .navbar .nav > li > .dropdown-menu > li >  a:hover, #goals-wrap .navbar .nav > li > .dropdown-menu > li >  a:focus {
            color: #FFF;
        }
        #goals-wrap .navbar .brand {
            font-size: 16px;
        }
        @media (max-width: 979px){
			#goals-wrap .navbar .nav > li > a:hover, #goals-wrap .navbar .nav > li > a:focus{
				color: #3a87ad;
			}
			#goals-wrap .navbar .nav > li > .dropdown-menu > li >  a:hover, #goals-wrap .navbar .nav > li > .dropdown-menu > li >  a:focus {
				color: #FFF;
			}
        }
");
$controller = JControllerLegacy::getInstance('Goals');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

if(!strpos($task,'Graph')) echo '</div>';