<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

if (!JFactory::getUser()->authorise('core.manage', 'com_goals')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}


JLoader::register('GoalsHelper', JPATH_ADMINISTRATOR . '/components/com_goals/helpers/goals.php');
JHtml::_('behavior.modal', 'a.modal');
jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('Goals');

$controller->execute(JFactory::getApplication()->input->get('task'));

$controller->redirect();
