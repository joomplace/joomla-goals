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

class GoalsViewDashboard extends JViewLegacy
{
    protected $state = null;
    protected $goals = null;
    protected $milistones = null;
    public $_calendar = null;
    public $_calendar_date = 'now';

    function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->goals_array = $this->get('Items');

        $this->plans_array = $this->get('Plans');

        //$this->milistones = $milistones = $this->get('Milistones');
        $this->records = $records = $this->get('Records');
        $this->habits = $milistones = $this->get('Habits');
        $this->pagination = $this->get('Pagination');

        $data = $this->prepareData($this);

        foreach($data as $var => $value)
            $this->$var = $value;

        if (count($errors = $this->get('Errors'))) {
            JError::raiseWarning(500, implode("\n", $errors));

            return false;
        }

        parent::display($tpl);
    }

    public function sorting($a, $b)
    {
        if ($a->ordering == $b->ordering) {
            return ($a->id < $b->id) ? -1 : 1;
        } else {
            return ($a->ordering < $b->ordering) ? -1 : 1;
        }

    }
    public function setDocument() {
        JHTML::_('behavior.tooltip');
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
        $document->addScript(JURI::root() . 'components/com_goals/assets/js/jquery-ui-1.9.2.sortable.min.js',"text/javascript",true);


        $document->addScriptDeclaration('
        jQuery(function() {
            jQuery( ".goals-list" ).sortable({
                revert: true,
                handle: ".goals-drag-pict",
                update: function( event, ui ) {
                    var IDs = [];
                     var items = jQuery(".goals-list").find("li.goals-list-layout").each(function(){ IDs.push(this.id); });;
                     jQuery.ajax({
                    type: "POST",
                    url: "index.php?option=com_goals&task=goal.sortingFeatured",
                    data: "ids="+IDs
                    });
                }
            });
        });

    ');
    }

    public function prepareData($data){
        if($GLOBALS["viewed_user"]->id==$GLOBALS["viewer_user"]->id) $data->manage_allowed = true;

        $data->goals = $goals = array_merge((array)$data->goals_array, (array)$data->plans_array);

        usort($data->goals, array("GoalsViewDashboard", "sorting"));

        $settings = GoalsHelperFE::getSettings();

        $jdate = new JDate('now');

        $nowdate = $jdate->__toString();

        if (sizeof($goals)) {
            foreach ($goals as $goal) {

                if ($goal->type == 'goal') {
                    $goal->milistones = GoalsHelperFE::getMilistones($goal->id);
                    $goal->records = GoalsHelperFE::getRecords($goal->id);
                    $goal->records_count = sizeof($goal->records);
                    $goal->percent = 0;
                    if ($goal->records_count) {
                        $goal = GoalsHelperFE::getPercents($goal);
                    }

                    //DAte away late
                    $tillleft = GoalsHelperFE::date_diff($nowdate, $goal->startup, 'start');
                    $tillleftstr = GoalsHelperFE::getDateLeft($tillleft);
                    $goal->tillleft = '('.$tillleftstr.' '.$tillleft['lateoraway'].')';

                    $left = GoalsHelperFE::date_diff($nowdate, $goal->deadline);
                    $leftstr = GoalsHelperFE::getDateLeft($left);
                    $goal->left = '(' . $leftstr . ' ' . $left['lateoraway'] . ')';


                    //
                    $goal->milistones_count = count($goal->milistones);
                    //Statuses
                    if (sizeof($goal->milistones)) {
                        $lastmildate = null;

                        foreach ($goal->milistones as $k => $mil) {
                            //Date away late
                            $left = GoalsHelperFE::date_diff($nowdate, $mil->duedate);
                            $leftstr = GoalsHelperFE::getDateLeft($left);
                            $mil->left = '(' . $leftstr . ' ' . $left['lateoraway'] . ')';
                            //
                            if ($left['lateoraway'] == 'away' && $mil->duedate > $nowdate) {
                                $mil->leftstatus = GoalsHelperFE::getStatusLeft($left);

                            } else {
                                $mil->leftstatus = 4;
                            }

                            if ($mil->status == 0) {
                                if (!$lastmildate) {
                                    $lastmildate = $mil->duedate;
                                } else {
                                    if ($mil->duedate < $lastmildate) {
                                        $lastmildate = $mil->duedate;
                                    }
                                }
                            } else if ($mil->status == 1) {
                                $mil->leftstatus = 0;
                            }
                        }


                    }


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


                } else { // Plans


                    $goal->stages = GoalsHelperFE::getStages($goal->id);
                    $goal->plantasks = GoalsHelperFE::getPlanTasks($goal->id);
                    $goal->plantasks_done = GoalsHelperFE::getPlanTasksCount($goal->id, 1);
                    $goal->percent = 0;
                    $goal->plantasks_count = count($goal->plantasks);
				
					if ($goal->plantasks_count) {
						$goal->percent 	= round($goal->plantasks_done/$goal->plantasks_count*100);
					}


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

                    //DAte away late
                    $tillleft = GoalsHelperFE::date_diff($nowdate, $goal->startup, 'start');
                    $tillleftstr = GoalsHelperFE::getDateLeft($tillleft);
                    $goal->tillleft = '('.$tillleftstr.' '.$tillleft['lateoraway'].')';
                    
                    $left = GoalsHelperFE::date_diff($nowdate, $goal->deadline);
                    $leftstr = GoalsHelperFE::getDateLeft($left);
                    $goal->left = '(' . $leftstr . ' ' . $left['lateoraway'] . ')';

                    $goal->stages_count = count($goal->stages);
                    //Statuses
                    if (sizeof($goal->stages)) {
                        $lastmildate = null;

                        foreach ($goal->stages as $k => $mil) {
                            //Date away late
                            $left = GoalsHelperFE::date_diff($nowdate, $mil->duedate);
                            $leftstr = GoalsHelperFE::getDateLeft($left);
                            $mil->left = '(' . $leftstr . ' ' . $left['lateoraway'] . ')';
                            //
                            if ($left['lateoraway'] == 'away' && $mil->duedate > $nowdate) {
                                $mil->leftstatus = GoalsHelperFE::getStatusLeft($left);

                            } else {
                                $mil->leftstatus = 4;
                            }

                            if ($mil->status == 0) {
                                if (!$lastmildate) {
                                    $lastmildate = $mil->duedate;
                                } else {
                                    if ($mil->duedate < $lastmildate) {
                                        $lastmildate = $mil->duedate;

                                    }
                                }
                            } else if ($mil->status == 1) {
                                $mil->leftstatus = 0;
                            }
                        }

                    }
                }
            }
        }
        $this->setDocument();

        return $data;
    }

}
