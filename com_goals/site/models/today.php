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

class GoalsModelToday extends JModelList
{
    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables.
        $app	= JFactory::getApplication();
        $settings	= GoalsHelperFE::getSettings();

        $l = $app->getCfg('list_limit');
        if (isset($settings->goals_dashboard_count)) $l = (int) $settings->goals_dashboard_count;
        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $l);
        $this->setState('list.limit', $limit);

        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        $this->setState('list.start', $limitstart);

    }

    protected function getListQuery()
    {
        // Create a new query object.
        $db		= $this->getDbo();
        $query	= $db->getQuery(true);
        $user = JFactory::getUser();

        // Select required fields from the categories.
        $query->select('g.*');
        $query->from('`#__goals` AS `g`');
        $query->where('`g`.`uid`='.$user->id);
        $query->where('`g`.`is_complete`=0');
        $query->order('`g`.`deadline` DESC');
        return $query;
    }

    public function getRecords()
    {
        $settings	= GoalsHelperFE::getSettings();
        $db	= JFactory::getDbo();
        $user = JFactory::getUser();
        $query	= $db->getQuery(true);
        $query->select('t.*, g.`title` AS `gtitle`, g.`metric` AS `gmetric`');
        $query->from('`#__goals_tasks` AS `t`');
        $query->join('LEFT','`#__goals` AS `g` ON `t`.`gid`=`g`.`id`');
        $query->where("DATE_FORMAT(`t`.`date`, '%d-%m-%y') = DATE_FORMAT( NOW(), '%d-%m-%y' )");
        $query->where('`g`.`uid`='.$user->id);
        $db->setQuery($query, 0, isset($settings->records_dashboard_count) ? $settings->records_dashboard_count : 0);
        return $db->loadObjectList();
    }

    public function getTasks()
    {
        $settings	= GoalsHelperFE::getSettings();
        $db	= JFactory::getDbo();
        $user = JFactory::getUser();
        $query	= $db->getQuery(true);
        $query->select('t.*, p.`title` AS `ptitle`, p.`metric` AS `pmetric`, s.pid as pid, s.title as stitle');
        $query->from('`#__goals_plantasks` AS `t`');
        $query->join('LEFT','`#__goals_stages` AS `s` ON `t`.`sid`=`s`.`id`');
        $query->join('LEFT','`#__goals_plans` AS `p` ON `s`.`pid`=`p`.`id`');
        $query->where("DATE_FORMAT(`t`.`date`, '%d-%m-%y') = DATE_FORMAT( NOW(), '%d-%m-%y' )");
        $query->where('`p`.`uid`='.$user->id);
        $db->setQuery($query, 0, isset($settings->records_dashboard_count) ? $settings->records_dashboard_count : 0);
        return $db->loadObjectList();
    }
    public function getOverdueTasks()
    {
        $settings	= GoalsHelperFE::getSettings();
        $db	= JFactory::getDbo();
        $user = JFactory::getUser();
        $query	= $db->getQuery(true);
        $query->select('t.*, p.`title` AS `ptitle`, p.`metric` AS `pmetric`, s.pid as pid, s.title as stitle');
        $query->from('`#__goals_plantasks` AS `t`');
        $query->join('LEFT','`#__goals_stages` AS `s` ON `t`.`sid`=`s`.`id`');
        $query->join('LEFT','`#__goals_plans` AS `p` ON `s`.`pid`=`p`.`id`');
        $query->where("DATE_FORMAT(`t`.`date`, '%d-%m-%y') < DATE_FORMAT( NOW(), '%d-%m-%y' )");
        $query->where('`p`.`uid`='.$user->id);
        $query->where('`t`.`status`=0');
        $db->setQuery($query, 0, isset($settings->records_dashboard_count) ? $settings->records_dashboard_count : 0);
        return $db->loadObjectList();
    }

    public function getHabits()
    {
        $db	= $this->getDbo();
        $user = JFactory::getUser();

        $query	= $db->getQuery(true);
        $query->select('h.*');
        $query->from('`#__goals_habits` AS `h`');
        $query->where('`h`.`complete`=0');
        $query->where('`h`.`uid`='.$user->id);
        $query->order('`h`.`type` DESC');
        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function getHabitLog($id=0)
    {
        return GoalsHelperFE::getHabitLog($id);
    }
}