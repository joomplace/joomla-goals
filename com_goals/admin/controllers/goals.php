<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class GoalsControllerGoals extends JControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getModel($name = 'Goal', $prefix = 'GoalsModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function copy()
	{
		$db 	= JFactory::getDbo();
		$cid 	= JFactory::getApplication()->input->get('cid', false, 'DEFAULT', 'array');

		for ($i=0; $i < count($cid); $i++) {
			$query = "INSERT INTO `#__goals` (`title`, `description`, `image`, `deadline`, `metric`, `start`, `finish`, `uid`, `cid`, `is_complete`) SELECT `title`, `description`, `image`, `deadline`, `metric`, `start`, `finish`, `uid`, `cid`, `is_complete` FROM `#__goals` WHERE `id` = ".$cid[$i];
			$db->setQuery($query);
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
			}
			$new_id = $db->insertid();

			$query = "INSERT INTO `#__goals_xref` (`gid`, `fid`, `values`) SELECT ".$new_id.", `fid`, `values` FROM `#__goals_xref` WHERE `gid` = ".$cid[$i];
			$db->setQuery($query);
			if (!$db->query()) {
				$msg .= $db->getErrorMsg();
			}

			$query = "INSERT INTO `#__goals_milistones` (`title`, `description`, `duedate`, `gid`, `status`, `color`, `cdate`) SELECT `title`, `description`, `duedate`, ".$new_id.", `status`, `color`, `cdate` FROM `#__goals_milistones` WHERE `gid` = ".$cid[$i];
			$db->setQuery($query);
			if (!$db->query()) {
				$msg .= $db->getErrorMsg();
			}
		}

		$this->setRedirect('index.php?option=com_goals&view=goals', $msg);
	}

    function delete()
    {
        // Get items to remove from the request.
        $id = JFactory::getApplication()->input->get('cid', array(), '', 'array');

        $tmpl = JFactory::getApplication()->input->get('tmpl');
        if ($tmpl == 'component') $tmpl = '&tmpl=component'; else $tmpl = '';
        if (!is_array($id) || count($id) < 1) {
            JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
        } else {

            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($id);

            // Remove the items.
            if ($model->delete($id)) {

                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
				
				try{
					$query->delete('#__goals_milistones');
					$query->where('gid IN ('.implode(',',$id).')');
					$db->setQuery($query);
					$db->query();  //Remove all milistones
				}
				catch(Exception $e){}
				
                $query = $db->getQuery(true);
				
				try{
					$query->select('id');
					$query->from('#__goals_tasks');
					$query->where('gid IN ('.implode(',',$id).')');
					$db->setQuery($query);
					$ids = $db->loadColumn();
				}
				catch(Exception $e){}

                $query = $db->getQuery(true);
				
				try{
					$query->delete('#__goals_tasks');
					$query->where('gid IN ('.implode(',',$id).')');
					$db->setQuery($query);
					$db->query();      //Remove all tasks
				}	
				catch(Exception $e){}

                $query = $db->getQuery(true);
				
				try{
					$query->delete('#__goals_tasks_xref');
					$query->where('tid IN('.implode(',',$ids).')');
					$db->setQuery($query);
					$db->query();    //Remove all hrefs for tasks
				}
				catch(Exception $e){}
					
                $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($id)));
            } else {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $tmpl, false));
    }

	public function resetACL(){
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update("#__assets")
			->set('`rules`=\'{"core.admin":[],"core.manage":[],"core.create":{"1":1},"core.delete":{"1":1},"core.edit":{"1":1},"core.edit.state":{"1":1},"core.edit.own":{"1":1},"core.see_upcoming":{"1":1},"core.show_expired":{"1":1}}\'')
			->where('`name` LIKE "%com_goals%"');
		$db->setQuery($query);
		$db->execute();
		JFactory::getApplication()->enqueueMessage('COM_GOALS_ACL_RESETED');
        $this->setRedirect(JRoute::_('index.php?option=com_goals', false));
	}

}
