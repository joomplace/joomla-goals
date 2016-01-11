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

class GoalsControllerPlans extends JControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getModel($name = 'Plan', $prefix = 'GoalsModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

    function delete()
    {


        // Get items to remove from the request.
        $id = JRequest::getVar('cid', array(), '', 'array');

        $tmpl = JRequest::getVar('tmpl');
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
                $query->select('id');
                $query->from('#__goals_stages');
                $query->where('pid IN ('.implode(',',$id).')');
                $db->setQuery($query);
                $ids = $db->loadColumn();  //SELECT id's all stages

                $query = $db->getQuery(true);
                $query->delete('#__goals_stages');
                $query->where('pid IN ('.implode(',',$id).')');
                $db->setQuery($query);
                $db->query();  //Remove all stages


                if ($ids) {
                    $query = $db->getQuery(true);
                    $query->delete('#__goals_plantasks');
                    $query->where('sid IN ('.implode(',',$ids).')');
                    $db->setQuery($query);
                    $db->query();      //Remove all tasks

                }
                $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($id)));
            } else {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $tmpl, false));
    }

}
