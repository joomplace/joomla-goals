<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class GoalsModelDashboard_items extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) { $config['filter_fields'] = array('id','title', 'url', 'icon', 'published'); }
		parent::__construct($config);
	}
	
	protected function populateState($ordering = NULL, $direction = NULL)
	{
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		parent::populateState();
	}

	protected function getListQuery() 
	{
        $db		= $this->getDbo();
        $query	= $db->getQuery(true);
		$query->select('`id`,`title`,`url`,`icon`,`published`');
		$query->from('`#__goals_dashboard_items`');
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->Escape($search, true).'%');
			$query->where('title LIKE '.$search);
		}
        $orderCol	= $this->state->get('list.ordering', 'title');
        $orderDirn	= $this->state->get('list.direction', 'desc');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		return $query;
	}

    function delete($cid)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__goals_dashboard_items');
        $query->where('id IN ('.implode(',',$cid).')');
        $db->setQuery($query);
        $db->execute();  //Remove all milistones
    }
}
