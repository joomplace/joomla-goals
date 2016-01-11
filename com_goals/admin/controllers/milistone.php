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

class GoalsControllerMilistone extends JControllerForm
{
	public function addtemplate() {

        $model = $this->getModel('Milistone');
        $templates_model = $this->getModel('MilistonesTemplates');
        $templates = $templates_model->getItems();
        $view = $this->getView('Milistone','html');
        $view->assignRef('templates', $templates);
        $view->setLayout('default_template');
        $view->setModel($model, true);
        $view->display();

    }

}