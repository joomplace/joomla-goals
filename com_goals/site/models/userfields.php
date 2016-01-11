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

class GoalsModelUserfields extends JModelList
{
	protected function getListQuery()
	{
		$user = JFactory::getUser();
		$uid = $user->id;
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$id = JRequest::getInt('id',0);
		$query->select('f.*');
		$query->from('`#__goals_custom_fields` AS `f`');
		$query->where('`f`.`user`='.$uid);
		$query->order('`f`.`type` DESC');
		return $query;
	}
}