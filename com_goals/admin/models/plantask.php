<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class GoalsModelPlanTask extends JModelAdmin
{
	protected $context = 'com_goals';

	public function getTable($type = 'PlanTask', $prefix = 'GoalsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) 
	{
		$form = $this->loadForm('com_goals.plantask', 'plantask', array('control' => 'jform', 'load_data' => false));
		if (empty($form)) {
			return false;
		}
		$item = $this->getItem();
		$form->bind($item);

		return $form;
	}
	
	public function getCustomfieldsTable($id=0, $tid=0)
	{


		$db = $this->_db;
		$query = $db->getQuery(true);
			$query->select('`cid`');
			$query->from('#__goals_plans');
			$query->where('`id`='.(int)$id);
		$db->setQuery($query);
		$cid = $db->loadResult();
		if (!$cid) {echo '<div>'.JText::_('COM_GOALS_ERROR_ASSIGN_CAT').'</div>';die;}
		
		$fields = GoalsHelper::getCustomTaskFieldsPlans($cid, $tid);
		GoalsHelper::showCustoGroupFieldsValues($fields);
		$userfields = GoalsHelper::getCustomTaskUserFieldsPlans($id, $tid);
		GoalsHelper::showUserFieldsValues($userfields);
		die();
	}
}
