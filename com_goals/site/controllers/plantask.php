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

class GoalsControllerPlanTask extends JControllerForm
{

	 public function getModel($name = 'EditPlanTask', $prefix = 'GoalsModel', $config = array('ignore_request' => true))
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

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('pid')->from('#__goals_stages')->where('id='.$id);
        $db->setQuery($query);
        $pid = $db->loadResult();

        if (!$id) return '<div>'.JText::_('COM_GOALS_TASK_NOT_SELECTED').'</div>';
        $model = $this->getModel();
        return $model->getCustomfieldsTable($pid,$tid);
	}

	function save($key = NULL, $urlVar = NULL)
	{
		$app		= JFactory::getApplication();
		$context	= "$this->option.edit.$this->context";
		$app->setUserState($context.'.id',JRequest::getInt('id'));
		$pid = $app->input->get('pid', null, 'INT');
		$sid = $app->input->get('sid', null, 'INT');
        $tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
		//return parent::save();

        parent::save();
        if (isset($sid))
        {
            $url = JRoute::_('index.php?option=com_goals&view=plantasks&pid='.$pid.'&sid='.$sid.$tmpl, false);
        }
        $this->setRedirect($url,false);

	}

	function delete()
	{

		// Get items to remove from the request.
		$id	= JRequest::getVar('id', array(), '', 'array');
		$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';

        $app		= JFactory::getApplication();
        $pid = $app->input->get('pid', null, 'INT');
        $sid = $app->input->get('sid', null, 'INT');
        if($sid) $sid='&sid='.$sid; else $sid='';
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

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.'&pid='.$pid.$sid.$tmpl, false));
	}

	 function cancel($key = NULL)
	 {
         $app = JFactory::getApplication();
	 	$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
	 	$url =  JRoute::_('index.php?option=com_goals&view=plantasks'.$tmpl);

         $pid = $app->input->get('pid', null, 'INT');
         $sid = $app->input->get('sid', null, 'INT');
         if($sid) $sid='&sid='.$sid; else $sid='';

	 	if (isset($sid))
	 	{
	 		if ($sid) $url = JRoute::_('index.php?option=com_goals&view=plantasks&pid='.$pid.$sid.$tmpl);
	 	}
	 	$this->setRedirect($url,false);
	 }

    public function complete(){

        $app		= JFactory::getApplication();
        $context	= "$this->option.chagestate.$this->context";

        // Populate the data array:
        $data = array();
        $data['return'] = base64_decode($app->input->post->get('return', '', 'BASE64'));
        $id = $data['id'] = $app->input->get('id', '');

        // Set the return URL if empty.
        if (empty($data['return']))
        {
            $data['return'] = 'index.php?option=com_goals';
        }

        // Set the return URL in the user state to allow modification by plugins
        $app->setUserState($context.'.return', $data['return']);

        $model		= $this->getModel();
        if(JFactory::getUser()->id == $GLOBALS["viewed_user"]->id){
        $table		= $model->getTable();
        $table->load($id);

        if($table->complete(array($id),$table->status?0:1)){
            $app->setUserState($context.'.data', array());
        }
        else{
            $app->setUserState($context.'.data', $data);
        }

        $app->redirect(JRoute::_($app->getUserState($context.'.return'), false));

        //$this->setRedirect($url);
        }else{
            $app->setUserState($context.'.data', $data);
            JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
            $app->redirect(JRoute::_($app->getUserState($context.'.return'), false));
        }
    }

    public function addstatus()
    {

        $hid	= JRequest::getInt('hid', 0);
        $t		= JRequest::getInt('t', 0);
        $date	= date('Y-m-d H:i:s');
        $db		= JFactory::getDbo();
        $query	= $db->getQuery(true);
        $query->update('`#__goals_plantasks`');
        $query->set('status='.$t);
        $query->where('`id`='.$hid);
        $db->setQuery($query);
        $db->query();

    }


}
