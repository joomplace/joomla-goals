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

class GoalsTableGoalstemplate extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__goalstemplates', 'id', $db);
	}

	public function store($updateNulls = false)
	{
		$settings = GoalsHelper::getSettings();
		$isnew = false;
		if (!$this->id) $isnew = true;
		
		$form = JRequest::getVar('jform');	
        if (parent::store($updateNulls))
        {
        /*** GOALS XREF ***/
            $userfields = (array)$form['userfields'];
            if(sizeof($userfields))
            {
                $db = $this->_db;
                $query = $db->getQuery(true);
                    $query->delete('#__goals_xref');
                    $query->where('gid='.$this->id);
                    $db->setQuery($query);
                $db->query();

                foreach ( $userfields as $fid )
                {
                    $query = $db->getQuery(true);
                        $query->insert('#__goals_xref');
                        $query->set('`gid`='.$db->quote($this->id).', `fid`='.$db->quote($fid));
                        $db->setQuery($query);
                    $db->query();

                }
            }
			return true;
		}else return false;
	}
}