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

class GoalsModelStagesTemplates extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) { $config['filter_fields'] = array('id','title','duedate','gtitle','status'); }
		parent::__construct($config);
	}
	
	protected function populateState($ordering = NULL, $direction = NULL)
	{
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		$this->setState('filter.goalstemplates', $this->getUserStateFromRequest('com_goals.filter.goalstemplates', 'filter_goalstemplates'));
		parent::populateState();
	}

	protected function getListQuery() 
	{
		$db = $this->_db;
		$query = $db->getQuery(true);

		$query->select('m.*');


		$query->from('#__goals_stagestemplates AS m ');
		$query->select('p.title AS `pttitle`');
		$query->join('LEFT','`#__goals_planstemplates` AS `p` ON `p`.`id`=`m`.`pid`');
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->Escape($search, true).'%');
			$query->where('m.`title` LIKE '.$search.' OR m.`description` LIKE '.$search);
		}
		$search = $this->getState('filter.planstemplates');
		if (!empty($search)) {
			$query->where('`p`.`id`='.$search);
		}
		$orderCol	= $this->state->get('list.ordering','title');
		$orderDirn	= $this->state->get('list.direction','DESC');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}
	
	public function getGoalsTemplates()
	{
		$model = JModel::getInstance('PlansTemplates', 'GoalsModel', array('ignore_request' => true));
		$this->plans = $model->getItems();
		return $this->plans;
	}
}
