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

class GoalsControllerPlan extends JControllerForm
{
	public function upload()
   	{
   		$model = $this->getModel('Plan');
   		return $model->upload();
   	}
    public function addtemplate() {

        $model = $this->getModel('Plan');
        $templates_model = $this->getModel('PlansTemplates');
        $templates = $templates_model->getItems();
        $view = $this->getView('Plan','html');
        $view->assignRef('templates', $templates);
        $view->setLayout('default_template');
        $view->setModel($model, true);
        $view->display();

    }
}