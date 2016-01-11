<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class GoalsTableHabitsTemplate extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__goals_habitstemplates', 'id', $db);
	}
	
	function store($updateNulls = false)
	{

		$settings = GoalsHelper::getSettings();
		$db = $this->_db;
		$isnew = false;
		if (!$this->id) $isnew = true;
		
		//$this->color = JRequest::getVar('color');
		
		$week = array();
		for ( $i = 1, $n = 7; $i < $n; $i++ ) 
		{
			$week[] = JRequest::getVar(GoalsHelper::dayToStr($i));
		}
		$week[] = JRequest::getVar(GoalsHelper::dayToStr(0));
		$this->days = implode(',',$week);
		$this->weight = abs((int)$this->weight);
		if (parent::store())
		{
			if ($isnew && ($settings->enable_jsoc_int==2 || $settings->enable_jsoc_int==3))
				{
					/*ACTIVITY STREAM FOR JS*/
					jimport( 'joomla.filesystem.folder' );
					if (JFolder::exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_community'))
					{
					$templ_mes = $settings->jsoc_activity_mess_hab;
					$a = $b = array();
					$a[] = '{habit}'; 	$b[] = $this->title; 
					$a[] = '{type}';	$b[] = ($this->type=='+'?'positive':'negative');					
					$mes = $db->quote(str_replace($a,$b,$templ_mes));
					
						$query = $db->getQuery(true);			
						$today = JFactory::getDate();
						$date = $db->quote($today->toSQL());
							$query->insert('#__community_activities');	 	 	 	 	 	 	 	 	 	
							$query->set('`actor`='.(int)$this->uid);
							$query->set('`title`='.$mes);
							$query->set('`app`="habit"');
							$query->set('`created`='.$date);
							$query->set('`comment_type`="system.message"');
							$query->set('`like_type`="system.message"');
							$query->set('`points`=0');
							$db->setQuery($query);
							$db->query();
							
							$id = $db->insertid();
							if ($id)
							{
								$query = $db->getQuery(true);
									$query->update('#__community_activities');
									$query->where('`id`='.(int)$id);
									$query->set('`like_id`='.(int)$id);
									$query->set('`comment_id`='.(int)$id);
								$db->setQuery($query);
								$db->query();
							}
					}
					/**/
					}
			return true;
		}
		return false;
	}
	
	public function delete($pk = null)
	{
		if (parent::delete($pk))
		{
			$db		= JFactory::getDbo();
				$query	= $db->getQuery(true);
				$query->delete('`#__goals_habits_log`');
				$query->where('`hid`='.$pk);
				$db->setQuery($query);
				$db->query();
			return true;
		}
		return false;
	}
}