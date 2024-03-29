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

class GoalsControllerStage extends JControllerForm
{

	 public function getModel($name = 'Editstage', $prefix = 'GoalsModel', $config = array('ignore_request' => true))
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
		$app->setUserState($context.'.id',JRequest::getInt('id'));
		return parent::save();
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
