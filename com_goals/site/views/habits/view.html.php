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
jimport('joomla.utilities.date');

class GoalsViewHabits extends JViewLegacy
{
    protected $state = null;
    protected $habits = null;
    protected $pagination = null;
    protected $params = null;
    protected $startDate = null;
    protected $hrefBack = null;
    protected $hrefForward = null;

    function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->habits = $habits = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->params = GoalsHelper::getSettings();

        $jinput = JFactory::getApplication()->input;

        $week = $jinput->get('week', 0, 'int');

        $this->startDate = JFactory::getDate()->toUnix() + $week * 604800;
        $this->hrefBack = JRoute::_('index.php?option=com_goals&view=allhabits&week=' . (string)($week - 1), false);
        $this->hrefForward = JRoute::_('index.php?option=com_goals&view=allhabits&week=' . (string)($week + 1), false);

        if (!empty($this->habits)) {
            $habits = array();
            foreach ($this->habits as $hab) {
                $hab->complete_count = 0;
                $hab->percent = 0;
                $hab->todaydid = false;
                $completes = GoalsHelper::getHabitLog($hab->id);
                if ($completes) {
                    if ($hab->finish > 0) $hab->percent = round(($completes / $hab->finish) * 100);
                    if ($hab->percent >= 100) $hab->complete = 1;
                }
                $habits[] = $hab;
            }

            $this->habits = $habits;
        }

        if (count($errors = $this->get('Errors'))) {
            JError::raiseWarning(500, implode("\n", $errors));
            return false;
        }

        $document = JFactory::getDocument();

        JHTML::_('behavior.tooltip');
        $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');

        if (GoalsHelper::getJoomla3Vesion()) {
            JHtml::_('jquery.framework');
            JHtml::_('jquery.ui', array('core', 'sortable'));
        } else {
            $document->addScript(JURI::root() . 'components/com_goals/assets/js/jquery-ui-1.9.2.sortable.min.js');
        }

        $document->addScriptDeclaration('
            jQuery(function() {
            jQuery( ".goals-habits-list" ).sortable({
                revert: true,
                handle: ".goals-drag-pict",
                update: function( event, ui ) {
                    var IDs = [];
                     var items = jQuery(".goals-list").find("li.goals-list-layout").each(function(){ IDs.push(this.id); });;
                     jQuery.ajax({
                        type: "POST",
                        url: "index.php?option=com_goals&task=habit.sortingFeatured",
                        data: "ids="+IDs
                    });
                }
            }); });
        ');

        parent::display($tpl);

    }

}