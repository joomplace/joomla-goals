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

class GoalsTablePlanField extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__goals_custom_plan_fields', 'id', $db);
	}
	
	public function store($updateNulls = false)
	{
		$form = JRequest::getVar('jform');

		  /*** TYPES ***/
			 $type = (string)$form['type'];
			 $ob_params = new stdclass();
				 switch ( $type ) 
				 {
					case 'tf':	
							$ob_params->tf_default  = JRequest::getVar('tf_default');
							$ob_params->tf_max 		= JRequest::getVar('tf_max');
					break;
					case 'ta':	
							$ob_params->ta_rows  = JRequest::getVar('ta_rows');
							$ob_params->ta_colls = JRequest::getVar('ta_colls');					
					break;
					case 'hc':	break;
					case 'em':	break;
					case 'wu':	break;
					case 'pc':	break;
					case 'in':	break;
					case 'sl':	
						$sl_elmts = JRequest::getVar('sl_elmts');
						$sels=array();
						if (sizeof($sl_elmts))
						{
							foreach ( $sl_elmts as $sl_el ) 
							{
								$sl_el=trim($sl_el);
								if ($sl_el) $sels[]=$sl_el;
							}
						}
						$ob_params->sl_elmts = $sels;
					break;
					case 'ml':	
						$ms_elmts = JRequest::getVar('ms_elmts');
						$mels=array();
						if (sizeof($ms_elmts))
						{
							foreach ( $ms_elmts as $ml_el ) 
							{
								$ml_el=trim($ml_el);
								if ($ml_el) $mels[]=$ml_el;
							}
						}
						$ob_params->ms_elmts = $mels;
					break;
					case 'ch':	
					$ch_elmts = JRequest::getVar('ch_elmts');
						$chels=array();
						if (sizeof($ch_elmts))
						{
							foreach ( $ch_elmts as $ch_el ) 
							{
								$ch_el=trim($ch_el);
								if ($ch_el) $chels[]=$ch_el;
							}
						}
						$ob_params->ch_elmts = $chels;
					break;
					case 'rd':	
						$rb_elmts = JRequest::getVar('rb_elmts');
						$rbels=array();
						if (sizeof($rb_elmts))
						{
							foreach ( $rb_elmts as $rb_el ) 
							{
								$rb_el=trim($rb_el);
								if ($rb_el) $rbels[]=$rb_el;
							}
						}
						$ob_params->rb_elmts = $rbels;
						
					break;
					default:	break;
				}
			$this->values = json_encode($ob_params);
				
		/***/	
			
		if (parent::store($updateNulls))
		{
						
			/*** GROUPS ***/
			$groups = (array)$form['groups'];
				if(sizeof($groups))
				{
					$db = $this->_db;
					$query = $db->getQuery(true);
						$query->delete('#__goals_custom_groups_xref');
						$query->where('fid='.$this->id);
						$db->setQuery($query);
					$db->query();
						
					foreach ( $groups as $gid ) 
					{						
						$query = $db->getQuery(true);
							$query->insert('#__goals_custom_groups_xref');
							$query->set('`fid`='.$db->quote($this->id).', `gid`='.$db->quote($gid));
							$db->setQuery($query);
						$db->query();
						
					}
				}
			/***/
			return true;
		}else return false;
	}
}