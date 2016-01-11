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

class GoalsModelStagesTemplate extends JModelAdmin
{
	protected $context = 'com_goals';

	public function getTable($type = 'StagesTemplate', $prefix = 'GoalsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) 
	{
		$form = $this->loadForm('com_goals.stagestemplate', 'stagestemplate', array('control' => 'jform', 'load_data' => false));
		if (empty($form)) {
			return false;
		}
		$item = $this->getItem();
		$form->bind($item);

		return $form;
	}
}
