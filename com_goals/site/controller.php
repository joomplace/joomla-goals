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
jimport('joomla.filesystem.file');

if(!defined('DS')) define('DS', '/');

/**
 * Goals Component Controller
 */

class GoalsController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = array())
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		JHtml::_('bootstrap.framework');
			if(!$user->id)
			{
				$uri = JFactory::getURI();
				$app->redirect('index.php?option=com_users&view=login&return='. base64_encode($uri),JText::_('COM_GOALS_ALERTNOAUTHOR'),'warning');
				return false;
			}
		$view	= JRequest::getCmd('view', 'dashboard');
		JRequest::setVar('view', $view);
		if ($view=='fields')
		{
			?>
				<script type="text/javascript">
					window.parent.updateuserfields();
					//location.href=window.parent.location.href;
					window.parent.SqueezeBox.close();
				</script>
			<?php
			return;
		}else
		if ($view=='habitlogs')
		{
			$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
			$mainframe = JFactory::getApplication();
			$mainframe->redirect(JRoute::_('index.php?option=com_goals&view=habithistory'.$tmpl));
		}
		return parent::display($cachable = false, $urlparams = false);
	}
}