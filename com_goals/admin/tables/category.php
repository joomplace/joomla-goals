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

class GoalsTableCategory extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__goals_categories', 'id', $db);
	}
	
	public function store($updateNulls = false)
	{	

		if ($this->date_created=='0000-00-00 00:00:00' || $this->date_created=='') $this->date_created=date('Y-m-d H:i:s');
		if (parent::store($updateNulls))
		{
			$db = $this->_db;
					$query = $db->getQuery(true);
						$query->delete('#__goals_categories_xref');
						$query->where('cid='.$this->id);
						$db->setQuery($query);
			$db->query();
			 /*** CUSTOM FIELDS ***/
			 $cfs = (array)JRequest::getVar('cf',array());
			 if (sizeof($cfs))
			 {
			 	$cfs = array_unique($cfs);
			 						
					foreach ( $cfs as $cf ) 
					{						
						$query = $db->getQuery(true);
							$query->insert('#__goals_categories_xref');
							$query->set('`cid`='.$db->quote($this->id).', `fid`='.$db->quote($cf));
							$db->setQuery($query);
						$db->query();
						
					}
			 }
		 /***/ 
		return true;
		}
		return false;
	}
}