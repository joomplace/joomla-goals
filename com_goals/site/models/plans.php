<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class GoalsModelPlans extends JModelList
{
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$user 	= JFactory::getUser();
		//Select required fields from the categories.
        $query = GoalsHelper::getListQuery("plans",$db, $user);
		return $query;

	}

    public function getTasksCount($plan_id) {

    }
}