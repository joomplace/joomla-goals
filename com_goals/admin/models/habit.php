<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class GoalsModelHabit extends JModelAdmin
{
	protected $context = 'com_goals';

	public function getTable($type = 'Habit', $prefix = 'GoalsTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) 
	{
		$form = $this->loadForm('com_goals.habit', 'habit', array('control' => 'jform', 'load_data' => false));
		if (empty($form)) {
			return false;
		}
        $post = JRequest::get('post');

        if (isset($post['template_name'])) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('*');
            $query->from('#__goals_habitstemplates');
            $query->where('id='.$post['template_name']);
            $db->setQuery($query);

            $properties = $db->loadAssoc();
            $properties['id'] = null;

            $item = JArrayHelper::toObject($properties, 'JObject');

        } else {

            $item = $this->getItem();
        }

		$form->bind($item);

		return $form;
	}

}
