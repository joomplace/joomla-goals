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

class GoalsControllerHabit extends JControllerForm
{
    public function addTemplate() {

        $model = $this->getModel('Habit');
        $templates_model = $this->getModel('HabitsTemplates');
        $templates = $templates_model->getItems();
        $view = $this->getView('Habit','html');
        $view->assignRef('templates', $templates);
        $view->setLayout('default_template');
        $view->setModel($model, true);
        $view->display();
    }
}