<?php
/**
* Goals EasySocial widget for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

JHTML::_('behavior.tooltip');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');

require_once(JPATH_BASE . '/components/com_goals/helpers/goals.php');
require_once(JPATH_BASE . '/components/com_goals/views/goals/view.html.php');
/**
 * Canvas view for Textbook app
 *
 * @since	1.0
 * @access	public
 */
class GoalsmanagerViewCanvas extends SocialAppsView
{
	/**
	 * This method is invoked automatically and must exist on this view.
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 * @return 	void
	 */
	public function display( $userId )
	{
		// Requires the viewer to be logged in to access this app
		Foundry::requireLogin();

		// We want the user object from EasySocial so we can do funky stuffs.
		$user 	= Foundry::user( $userId );

		// Since we are on the canvas page, we have the flexibility to change the page title.
		if( $user->isViewer() )
		{
			$title 	= JText::_( 'Your Goals Manager' );
		}
		else
		{
			$title	= JText::sprintf( 'Goals from %1s' , $user->getName() );
		}

		// Set the page title. You can use JFactory::getDocument()->setTitle( 'title' ) as well.
		Foundry::page()->title( $title );

		// Load up the model
		$model 	= $this->getModel( 'Goals' );

		// Get the list of goals created by the user.
        $result = $model->getItems( $userId );
        $goals = $result->goals;
        $pagination = $model->getPagination();

		// Since the table decorates the date, we want to load them into the tables respectively.
	//	$goals 	= array();
	
		/*
		if( $result )
		{
			foreach( $result as $row )
			{
				// Load up the textbook's ORM
				$goal 	= $this->getTable( 'Goals' );
				$goal->bind( $row );

				$goals[]	= $goal;
			}	
		}
		*/

        /*
         * OLD VIEW CODE
         */

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
                    GoalsHelper::getPercents(&$goal);
                }

                //Date away late
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
        /*
         *
         *
         *
         */



        // Assign the goals to the theme files.
		// This option is totally optional, you can use your own theme object to output files.
		$this->set( 'goals' , $goals );
		$this->set( 'pagination' , $pagination );
		$this->set( 'user'		, $user );

        // If you use the built in theme manager, the namespace is relative to the following folder,
		// /media/com_easysocial/apps/user/textbook/themes/default

		$namespace 	= 'goals/default';

		// Output the contents
		echo parent::display( $namespace );
	}
}