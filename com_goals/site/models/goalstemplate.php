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

class GoalsModelGoalsTemplate extends JModelList
{
	protected function getListQuery()
	{
		$id = JRequest::getInt('id');
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

        $query->select('g.*');
        $query->from('`#__goalstemplates` AS `g`');

        return $query;
	}

}