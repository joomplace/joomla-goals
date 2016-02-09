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

class goalsViewPlansTemplate extends JViewLegacy
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
		$this->items			= $this->get('Items');
		$this->form			= $this->get('Form');
		$this->pagination	= $this->get('Pagination');

		//echo 'SMT DEBUG: <pre>'; print_R($this->form); echo '</pre>';
		if (empty($this->items)) {
			$authorised = ($user->authorise('core.create', 'com_goals'));
		}
		else {
			$authorised = $user->authorise('core.edit', 'com_goals');
		}

		if ($authorised !== true) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}



		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		$this->user		= $user;

        JHTML::_('behavior.tooltip');
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
		parent::display($tpl);
	}
}
