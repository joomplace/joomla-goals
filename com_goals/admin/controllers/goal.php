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

class GoalsControllerGoal extends JControllerForm
{
	public function upload()
   	{
   		$model = $this->getModel('Goal');
   		return $model->upload();
   	}
    public function addtemplate() {

        $model = $this->getModel('Goal');
        $templates_model = $this->getModel('GoalsTemplates');
        $templates = $templates_model->getItems();
        $view = $this->getView('Goal','html');
        $view->assignRef('templates', $templates);
        $view->setLayout('default_template');
        $view->setModel($model, true);
        $view->display();

    }



}