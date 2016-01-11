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

class GoalsModelAchievements extends JModelList
{
	public function getGoalsList()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$user 	= JFactory::getUser();
		//Select required fields from the categories.
		$query	= $db->getQuery(true)
					 ->select('g.*')
					 ->select('(SELECT COUNT(`t`.`id`) FROM `#__goals_tasks` AS `t` WHERE `t`.`gid`=`g`.`id` LIMIT 1) AS `records_count`')
					 ->select('(SELECT COUNT(`m`.`id`) FROM `#__goals_milistones` AS `m` WHERE `m`.`gid`=`g`.`id` LIMIT 1) AS `milistones_count`')
					 ->select('(SELECT COUNT(`mc`.`id`) FROM `#__goals_milistones` AS `mc` WHERE `mc`.`gid`=`g`.`id` AND `mc`.`status`=1 LIMIT 1) AS `milistones_count_complete`')
					 ->select('`c`.`title` AS `catname`')
					 ->join('LEFT','`#__goals_categories` AS `c` ON `c`.`id`=`g`.`cid`')
					 ->from('`#__goals` AS `g`')
					 ->where('`g`.`uid`='.$user->id)
                     ->where('`g`.`is_complete`=1')
					 ->order('`g`.`is_complete` DESC,`g`.`deadline` DESC');
        $db->setQuery($query);
		return $db->loadObjectList();
	}

    public function getPlansList()
    {
        // Create a new query object.
        $db		= $this->getDbo();
        $user 	= JFactory::getUser();
        //Select required fields from the categories.
        $query	= $db->getQuery(true)
            ->select('DISTINCT g.*')
            ->from('`#__goals_plans` AS `g`')
            ->where('`g`.`uid`='.$user->id)
            ->where('`g`.`is_complete`=1')
            ->order('`g`.`is_complete` DESC,`g`.`deadline` DESC');
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}