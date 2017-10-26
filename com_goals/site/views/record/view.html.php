<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class GoalsViewRecord extends JViewLegacy
{
	protected $state = null;
	protected $recs = null;
	protected $cfields = null;
	protected $ufields = null;

       function display($tpl = null)
        {
        	$this->state	= $this->get('State');
			$this->recs		= $recs = $this->get('Items');
			if (isset($recs[0])) $this->rec	= $recs[0]; else $this->rec=null;

			$settings = GoalsHelperFE::getSettings();
        	$this->settings = $settings;

			if($this->rec) $this->cfields = $cfields = GoalsHelperFE::getCustomTaskFields($this->rec->cid, $this->rec->id);
			if($this->rec) $this->ufields = $ufields = GoalsHelperFE::getCustomTaskUserFields($this->rec->gid, $this->rec->id);

			//echo 'SMT DEBUG: <pre>'; print_R($cfields); echo '</pre>';
			//echo 'SMT DEBUG: <pre>'; print_R($ufields); echo '</pre>';

			if (count($errors = $this->get('Errors'))) { JError::raiseWarning(500, implode("\n", $errors));	return false;}

			JHTML::_('behavior.tooltip');

            $document =JFactory::getDocument();
            $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');

			parent::display($tpl);
        }
}
