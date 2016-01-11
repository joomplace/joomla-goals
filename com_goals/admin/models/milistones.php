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

class GoalsModelMilistones extends JModelList
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
		$this->setState('filter.goals', $this->getUserStateFromRequest('com_goals.filter.goals', 'filter_goals'));
		parent::populateState();
	}

	protected function getListQuery() 
	{
		$db = $this->_db;
		$query = $db->getQuery(true);
		$query->select('m.*');
		$query->from('#__goals_milistones AS m ');
		$query->select('g.title AS `gtitle`');
		$query->join('LEFT','`#__goals` AS `g` ON `g`.`id`=`m`.`gid`');
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->Escape($search, true).'%');
			$query->where('m.`title` LIKE '.$search.' OR m.`description` LIKE '.$search);
		}
		$search = $this->getState('filter.goals');
		if (!empty($search)) {
			$query->where('`g`.`id`='.$search);
		}
		$orderCol	= $this->state->get('list.ordering','title');
		$orderDirn	= $this->state->get('list.direction','DESC');
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}

	public function getGoals()
	{
		$model = JModelAdmin::getInstance('Goals', 'GoalsModel', array('ignore_request' => true));
		$this->goals = $model->getItems();

		return $this->goals;
	}
}