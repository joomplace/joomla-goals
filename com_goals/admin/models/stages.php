<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class GoalsModelStages extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) { $config['filter_fields'] = array('id','title','duedate','gtitle','status'); }
		parent::__construct($config);
	}
	
	protected function populateState()
	{
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		$this->setState('filter.plans', $this->getUserStateFromRequest('com_goals.filter.plans', 'filter_plans'));
		parent::populateState();
	}

	protected function getListQuery() 
	{
		$db = $this->_db;
		$query = $db->getQuery(true);

		$query->select('m.*');
		$query->from('#__goals_stages AS m ');
		$query->select('g.title AS `gtitle`');
		$query->join('LEFT','`#__goals_plans` AS `g` ON `g`.`id`=`m`.`pid`');
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->Escape($search, true).'%');
			$query->where('m.`title` LIKE '.$search.' OR m.`description` LIKE '.$search);
		}
		$search = $this->getState('filter.plans');
		if (!empty($search)) {
			$query->where('`g`.`id`='.$search);
		}
		$orderCol	= $this->state->get('list.ordering','title');
		$orderDirn	= $this->state->get('list.direction','DESC');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}
	
	public function getPlans()
	{
		$model = JModelList::getInstance('Plans', 'GoalsModel', array('ignore_request' => true));
		$this->plans = $model->getItems();
		
		return $this->plans;
	}
}
