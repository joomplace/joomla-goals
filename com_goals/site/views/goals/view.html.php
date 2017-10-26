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
jimport( 'joomla.utilities.date' );

class GoalsViewGoals extends JViewLegacy
{
	protected $state = null;
	protected $goals = null;
	protected $milistones = null;

	function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->goals		= $goals = $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));	return false;
		}
        $settings = GoalsHelper::getSettings();
        $this->settings = $settings;
        
		$jdate = new JDate('now');
        $nowdate = $jdate->__toString();

		if (sizeof($goals)) {
			foreach ($goals as $goal)	{
				$goal->milistones	 = GoalsHelper::getMilistones($goal->id);
				$goal->records 	  	 = GoalsHelper::getRecords($goal->id);
				$goal->records_count = sizeof($goal->records);
				$goal->percent		 = 0;
				if ($goal->records_count) {
					$goal = GoalsHelper::getPercents($goal);
				}

				//Date away late
                $tillleft = GoalsHelper::date_diff($nowdate, $goal->startup, 'start');
				$tillleftstr = GoalsHelper::getDateLeft($tillleft);
				$goal->tillleft = '('.$tillleftstr.' '.$tillleft['lateoraway'].')';

				$left = GoalsHelper::date_diff($nowdate, $goal->deadline);
				$leftstr = GoalsHelper::getDateLeft($left);
				$goal->left = '('.$leftstr.' '.$left['lateoraway'].')';

                $date_ahead = date('Y-m-d', strtotime($goal->deadline)-(int)$settings->n_days_ahed*24*60*60);
                $date_behind =  date('Y-m-d', strtotime($goal->deadline)+(int)$settings->m_days_behind*24*60*60);
                $now_date = date('Y-m-d', time());
                if ($goal->is_complete) {
                    $goal->status = $settings->complete_status_color;
                } else {
                    if ($now_date<$date_ahead) {
                        $goal->status = $settings->away_status_color;
                    } elseif ($now_date>$date_behind) {
                        $goal->status = $settings->late_status_color;
                    } else { $goal->status = $settings->justint_status_color;}
                }

				//Statuses
				if (sizeof($goal->milistones)) {
					$lastmildate = null;
					$lastmilstatus = 4;



					foreach ($goal->milistones as $mil) {
						//Date away late
						$left = GoalsHelper::date_diff($nowdate, $mil->duedate);
						$leftstr = GoalsHelper::getDateLeft($left);
						$mil->left = '('.$leftstr.' '.$left['lateoraway'].')';
						if ($left['lateoraway']=='away' && $mil->duedate>$nowdate) {
							$mil->leftstatus = GoalsHelper::getStatusLeft($left);
						} else {
							$mil->leftstatus = 4;
						}

						if ($mil->status==0) {
							if (!$lastmildate) {
								$lastmildate = $mil->duedate;
								$lastmilstatus = $mil->leftstatus;
							} else {
								if ($mil->duedate<$lastmildate) {
									$lastmildate=$mil->duedate;
									$lastmilstatus = $mil->leftstatus;
								}
							}
						}
					}

                }
			}
		}

        $this->return   = GoalsHelper::getReturnURL();

		JHTML::_('behavior.tooltip');
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
        $this->addFilterBar();
        $this->sidebar = JHtmlSidebar::getFilters();
		parent::display($tpl);
	}

    public function addFilterBar(){

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_CATEGORY'),
            'filter_category_id',
            JHtml::_('select.options', $this->get('Categories'), 'value', 'text', $this->state->get('filter.category_id'))
        );

    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields()
    {
        return array(
            'g.deadline' => JText::_('JGRID_HEADING_ORDERING'),
            'g.title' => JText::_('JGLOBAL_TITLE'),
            'category_title' => JText::_('JCATEGORY'),
            'g.featured' => JText::_('JFEATURED'),
            'g.id' => JText::_('JGRID_HEADING_ID')
        );
    }

    public function getGoalsOpt(){
        $options = array();
        foreach($this->goals as $goal){
            $metric = "";
            if($goal->metric) $metric = $goal->metric.', ';
            $options[] = '<option data-metric="'.$goal->metric.'" value="'.$goal->id.'">'.$metric.$goal->title.'</option>';
        }
        return implode('',$options);
    }
}
