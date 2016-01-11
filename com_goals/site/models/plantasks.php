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

class GoalsModelPlantasks extends JModelList
{
    public function getItems(){
        return $this->getPlans(true);
    }

    public function getTaskLegacy(){
        $task_id = JFactory::getApplication()->input->get('id',false);
        $user = JFactory::getUser();

        $db		= $this->getDbo();
        $query	= $db->getQuery(true);
        $query->select('t.id, s.id as `sid`, p.id as `pid`')
            ->from('`#__goals_plantasks` AS `t`')
            ->join('LEFT','`#__goals_stages` AS `s` ON `t`.`sid`=`s`.`id`')
            ->join('LEFT','`#__goals_plans` AS `p` ON `p`.`id`=`s`.`pid`');
        $query->where('`p`.`uid`='.$user->id);
        $query->where('`t`.`id`='.$task_id);
        $db->setQuery($query);
        return $db->loadObject();
    }

    public function getPlans($recursive = false)
    {
        $user = JFactory::getUser();
        $id = JFactory::getApplication()->input->get('pid',false);
        if($id){
            $plans = array($this->getPlan($id, $recursive));
        }else{
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.*');
            $query->from('`#__goals_plans` AS `p`');
            $query->where('`p`.`uid`='.$user->id);
            $db->setQuery($query);
            $plans = $db->loadObjectList('id');
            if($recursive){
                foreach($plans as &$plan){
                    $plan->stages = self::getStages($plan->id, $recursive);
                }
            }
        }
        return $plans;
    }

    public function getPlan($id, $recursive = false)
    {
        $user = JFactory::getUser();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('p.*');
        $query->from('`#__goals_plans` AS `p`');
        if($id) $query->where('`p`.`id` = ' . (int)$id);
        $query->where('`p`.`uid`='.$user->id);
        $db->setQuery($query);
        $plan = $db->loadObject();
        if($recursive){
            $plan->stages = self::getStages($plan->id, $recursive);
        }
        return $plan;
    }

    public function getStages($pid = 0, $recursive = false)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('m.*');
        $query->from('`#__goals_stages` AS `m`');
        if($pid) $query->where('`m`.`pid` = ' . $pid);
        //$query->order('`m`.`duedate` DESC');
        $db->setQuery($query);
        $stages = $db->loadObjectList('id');
        if($recursive){
            foreach($stages as &$stage){
                $stage->tasks = self::getStageTasks($stage->id);
            }
        }
        return $stages;
    }

    public function getStageTasks($sid = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__goals_plantasks');
        $query->where('`sid` = '.$db->quote($sid));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /*
	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	    $user = JFactory::getUser();

	    $pid = JRequest::getInt('pid',0);
	    $sid = JRequest::getInt('sid',0);

		$query->select('r.*, s.id as sid, s.title as stitle');
		$query->select('`g`.`title` AS `ptitle`, g.`metric` AS `pmetric`, `g`.id as `pid`');
        $query->from('`#__goals_plantasks` AS `r`');
        $query->join('LEFT','`#__goals_stages` AS `s` ON `r`.`sid`=`s`.`id`');
        $query->join('LEFT','`#__goals_plans` AS `g` ON `g`.`id`=`s`.`pid`');

		$query->where('`g`.`uid`='.$user->id);
		if ($pid) $query->where('`s`.`pid`='.(int)$pid);
		if ($sid) $query->where('`sid`='.(int)$sid);
		$query->order('`r`.`date` DESC');

		return $query;
	}
    */
}