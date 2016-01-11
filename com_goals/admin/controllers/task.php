<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

class GoalsControllerTask extends JControllerForm
{
	
	public function getcustomfields()
	{
		$id = JRequest::getInt('id');
		$tid = JRequest::getInt('tid');
		if (!$id) return '<div>'.JText::_('COM_GOALS_TASK_NOT_SELECTED').'</div>';
		$model = $this->getModel();
		return $model->getCustomfieldsTable($id,$tid);
	}
}