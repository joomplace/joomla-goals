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

class GoalsModelPlantask extends JModelList
{
	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	    $user = JFactory::getUser();

	    $pid = JRequest::getInt('pid',0);
	    $id = JRequest::getInt('id',0);



        $query->select('r.*, s.id as sid, s.title as stitle');
        $query->select('`g`.`title` AS `ptitle`, g.`metric` AS `pmetric`, `g`.id as `pid`, `g`.`cid` AS `cid`');
        $query->from('`#__goals_plantasks` AS `r`');
        $query->join('LEFT','`#__goals_stages` AS `s` ON `r`.`sid`=`s`.`id`');
        $query->join('LEFT','`#__goals_plans` AS `g` ON `g`.`id`=`s`.`pid`');
        if ($id) $query->where('`r`.`id`='.(int)$id);
        $query->where('`g`.`uid`='.$user->id);
        if ($pid) $query->where('`s`.`pid`='.(int)$pid);
        $query->order('`r`.`date` DESC');


		return $query;
	}
}