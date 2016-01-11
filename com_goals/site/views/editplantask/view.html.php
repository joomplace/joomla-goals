<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class GoalsViewEditPlanTask extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();

		// Get model data.
		$this->state		= $this->get('State');
		$this->item			= $this->get('Item');
		$this->form			= $this->get('Form');

        $getStage = JRequest::getVar('sid');
        if ($getStage) {
            $this->item->sid=$getStage;
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id')->from('#__goals_stages')->where('pid='.$db->escape($getStage));
            $planid = $db->setQuery($query)
                ->loadResult();
            if ($planid) {
                $query = $db->getQuery(true);
                $query->select('metric')->from('#__goals_plans')->where('id='.$db->escape($planid));
                $metric = $db->setQuery($query)
                    ->loadResult();
                $this->item->metric = $metric;
            }

        }
		if (empty($this->item->id)) {
			$authorised = ($user->authorise('core.create', 'com_goals'));
		}
		else {
			$authorised = $user->authorise('core.edit', 'com_goals');
		}

		if ($authorised !== true) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		if (!empty($this->item)) {
			$this->form->bind($this->item);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		$this->user		= $user;
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
		parent::display($tpl);
	}
}
