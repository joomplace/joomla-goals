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

class GoalsTableMilistone extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__goals_milistones', 'id', $db);
	}
	
	function store($updateNulls = false)
	{
		$this->color = JRequest::getVar('color');
		return parent::store();
	}


    public function complete($pks = null, $state = 1, $userId = 0)
    {
        $k = $this->_tbl_keys;

        if (!is_null($pks))
        {
            foreach ($pks AS $key => $pk)
            {
                if (!is_array($pk))
                {
                    $pks[$key] = array($this->_tbl_key => $pk);
                }
            }
        }

        $userId = (int) $userId;
        $state  = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks))
        {
            $pk = array();

            foreach ($this->_tbl_keys AS $key)
            {
                if ($this->$key)
                {
                    $pk[$this->$key] = $this->$key;
                }
                // We don't have a full primary key - return false
                else
                {
                    return false;
                }
            }

            $pks = array($pk);
        }

        foreach ($pks AS $pk)
        {
            // Update the publishing state for rows with the given primary keys.
            $query = $this->_db->getQuery(true)
                ->update('#__goals_milistones')
                ->set('`status` = ' . (int) $state);

            // Determine if there is checkin support for the table.
            if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time'))
            {
                $query->where('(checked_out = 0 OR checked_out = ' . (int) $userId . ')');
                $checkin = true;
            }
            else
            {
                $checkin = false;
            }

            // Build the WHERE clause for the primary keys.
            $this->appendPrimaryKeys($query, $pk);

            $this->_db->setQuery($query);
            $this->_db->execute();

            // If checkin is supported and all rows were adjusted, check them in.
            if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
            {
                $this->checkin($pk);
            }

            $ours = true;

            foreach ($this->_tbl_keys AS $key)
            {
                if ($this->$key != $pk[$key])
                {
                    $ours = false;
                }
            }

            if ($ours)
            {
                $this->published = $state;
            }
        }

        $this->setError('');

        return true;
    }

}