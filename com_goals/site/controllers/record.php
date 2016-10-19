<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class GoalsControllerRecord extends JControllerForm
{

	 public function getModel($name = 'Editrecord', $prefix = 'GoalsModel', $config = array('ignore_request' => true))
	{
		if (empty($name)) {
			$name = $this->context;
		}

		return parent::getModel($name, $prefix, $config);
	}


	public function getcustomfields()
	{
		$id = JRequest::getInt('id');
		$tid = JRequest::getInt('tid');
		$negative = JRequest::get('negative');
		if (!$id) return '<div>'.JText::_('COM_GOALS_TASK_NOT_SELECTED').'</div>';
		$model = $this->getModel();
		header('Content-Type: text/html;charset=UTF-8');
		return $model->getCustomfieldsTable($id,$tid,$negative);
	}

	function save($key = NULL, $urlVar = NULL)
	{
		$app		= JFactory::getApplication();
		$context	= "$this->option.edit.$this->context";

        // Populate the data array:
        $data = array();
        $data['return'] = base64_decode($app->input->post->get('return', '', 'BASE64'));
        $data['id'] = $app->input->post->get('id', '', 'post');
        $data['title'] = $app->input->post->get('title', '', 'post');
        $data['description'] = $app->input->post->get('description', '', 'post');
        $data['result_mode'] = $app->input->post->get('result_mode', '1', 'post');
        $data['date'] = $app->input->post->get('date', '', 'post');
        $data['value'] = $app->input->post->get('value', '', 'post');
        $data['gid'] = $app->input->post->get('gid', '', 'post');

        // Set the return URL if empty.
        if (empty($data['return']))
        {
            $data['return'] = 'index.php?option=com_goals';
        }

        // Set the return URL in the user state to allow modification by plugins
        $app->setUserState($context.'.return', $data['return']);

        if(parent::save()){
            $app->setUserState($context.'.data', array());
        }
        else{
            $app->setUserState($context.'.data', $data);
        }

        $app->redirect(JRoute::_($app->getUserState($context.'.return'), false));

	 	//$this->setRedirect($url);
	}

	function delete()
    {

        $app		= JFactory::getApplication();
        $context	= "$this->option.delete.$this->context";

        // Populate the data array:
        $data = array();
        $data['return'] = base64_decode($app->input->get->get('return', '', 'BASE64'));
        $data['id'] = $id = (int)$app->input->get->get('id', '', 'post');
        $pk = array($id);
        // Set the return URL if empty.
        if (empty($data['return']))
        {
            $data['return'] = 'index.php?option=com_goals';
        }

        $model = $this->getModel();

        // Set the return URL in the user state to allow modification by plugins
        $app->setUserState($context.'.return', $data['return']);

        if($model->delete($pk)){
            $app->setUserState($context.'.data', array());
        }
        else{
            $app->setUserState($context.'.data', $data);
        }

        $app->redirect(JRoute::_($app->getUserState($context.'.return'), false));
	}

	 function cancel($key = NULL)
	 {
	 	$mainframe = JFactory::getApplication();
	 	$id = JRequest::getInt('gid');
	 	$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
	 	$url =  JRoute::_('index.php?option=com_goals&view=records'.$tmpl, false);
	 	if (isset($id))
	 	{
	 		if ($id>0) $url = JRoute::_('index.php?option=com_goals&view=records&gid='.(int)$id.$tmpl, false);
	 	}
	 	$this->setRedirect($url);
	 }

}
