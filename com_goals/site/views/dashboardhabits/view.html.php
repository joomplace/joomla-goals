<?php/*** Goals component for Joomla 3.0* @package Goals* @author JoomPlace Team* @Copyright Copyright (C) JoomPlace, www.joomplace.com* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html**/defined('_JEXEC') or die('Restricted access');jimport('joomla.application.component.view');jimport( 'joomla.utilities.date' );class GoalsViewDashboardHabits extends JViewLegacy{	protected $state = null;	protected $habits = null;       function display($tpl = null)        {            $document = JFactory::getDocument();			JHTML::_('behavior.tooltip');            $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');            $document->addScript('http://code.jquery.com/ui/1.9.1/jquery-ui.js'); //TODO: Exclude from code, on/off in settings since 1.2.0			parent::display($tpl);        }}