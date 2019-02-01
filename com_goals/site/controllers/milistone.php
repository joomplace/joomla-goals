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

class GoalsControllerMilistone extends JControllerForm
{

	 public function getModel($name = 'Editmilistone', $prefix = 'GoalsModel', $config = array('ignore_request' => true))
	{
		if (empty($name)) {
			$name = $this->context;
		}

		return parent::getModel($name, $prefix, $config);
	}

	function save($key = null, $urlVar = null)
	{
        $app		= JFactory::getApplication();
        $context	= "$this->option.edit.$this->context";

        // Populate the data array:
        $data = array();
        $data['return'] = base64_decode($app->input->post->get('return', '', 'BASE64'));
        $data['id'] = $app->input->post->get('id', '', 'post');
        $data['title'] = $app->input->post->get('title', '', 'post');
        $data['result'] = $app->input->post->get('result', '', 'post');
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
	}

	function delete()
	{
		// Get items to remove from the request.
		$id	= JRequest::getVar('id', array(), '', 'array');
		$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
		if (!is_array($id) || count($id) < 1) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($id);

			// Remove the items.
			if ($model->delete($id)) {
				$this->setMessage(JText::plural($this->text_prefix.'_N_ITEMS_DELETED', count($id)));
			} else {
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$tmpl, false));
	}

	 function cancel($key = null)
	 {
         $app		= JFactory::getApplication();

         // Populate the data array:
         $data = array();
         $data['return'] = base64_decode($app->input->post->get('return', '', 'BASE64'));

         // Set the return URL if empty.
         if (empty($data['return']))
         {
             $data['return'] = 'index.php?option=com_goals';
         }

         $app->redirect(JRoute::_($data['return'], false));
	 }

}
