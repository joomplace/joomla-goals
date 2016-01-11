<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

class GoalsControllerPlanTask extends JControllerForm
{
	
	public function getcustomfields()
	{
		$id = JRequest::getInt('id');
		$tid = JRequest::getInt('tid');

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('pid')->from('#__goals_stages')->where('id='.$id);
        $db->setQuery($query);
        $pid = $db->loadResult();

		if (!$id) return '<div>'.JText::_('COM_GOALS_TASK_NOT_SELECTED').'</div>';
		$model = $this->getModel();
		return $model->getCustomfieldsTable($pid,$tid);
	}

	public function addtemplate() {

        $model = $this->getModel('Plantask');
        $templates_model = $this->getModel('PlantasksTemplates');
        $templates = $templates_model->getItems();
        $view = $this->getView('Plantask','html');
        $view->assignRef('templates', $templates);
        $view->setLayout('default_template');
        $view->setModel($model, true);
        $view->display();

    }
}