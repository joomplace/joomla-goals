<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

class GoalsControllerHabitlog extends JControllerForm
{
	public function getModel($name = 'addhabitlog', $prefix = 'GoalsModel', $config = array('ignore_request' => true))
	{
		if (empty($name)) {
			$name = $this->context;
		}

		return parent::getModel($name, $prefix, $config);
	}


	function cancel($key = null)
	 {
	 	$id = JRequest::getInt('id');
	 	$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
	 	$url =  JRoute::_('index.php?option=com_goals&view=habithistory'.$tmpl);
	 	if (isset($id))
	 	{
	 		if ($id>0) $url = JRoute::_('index.php?option=com_goals&view=habithistory&id='.(int)$id.$tmpl);
	 	}
	 	$this->setRedirect($url,false);
	 }

}