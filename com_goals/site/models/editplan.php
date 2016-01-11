<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/plan.php';

class GoalsModelEditplan extends GoalsModelPlan
{
	public function getTable($type = 'Plan', $prefix = 'GoalsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
    public function getItem() {
        $get = JRequest::get('get');
        if (isset($get['tempid'])) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('*');
            $query->from('#__goals_planstemplates');
            $query->where('id='.$get['tempid']);
            $db->setQuery($query);
            $item = $db->loadObject();
            $item->deadline = date('Y-m-d', time()+($item->period)*24*60*60);
            $item->id = null;
            $item->template = $get['tempid'];
        } else {

           $item =  parent::getItem();

        }

        return $item;

    }
}
