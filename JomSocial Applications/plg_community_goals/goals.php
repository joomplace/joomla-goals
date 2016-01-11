<?php
/**
 * Goals component for Joomla 3.0
 * @package Goals
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php');

if(!class_exists('plgCommunityGoals'))
{
	class plgCommunityGoals extends CApplications
	{
		var $_user		= null;
		var $_name		= "Goals";
		var $name		= "Goals"; //old versions
		var $_path		= '';
		var $db			= null;
		var $doc		= null;
		var $my			= null;
		var $_count		= 5;

		function plgCommunityGoals($subject, $config)
		{
			$this->db   	= JFactory::getDBO();
			$this->doc		= JFactory::getDocument();
			
			$this->_path	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_goals';
			$this->_fepath	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_goals';
			parent::__construct($subject, $config);
		}

		function onProfileDisplay()
		{
			$this->_user	= CFactory::getActiveProfile();
			$this->my		= CFactory::getUser();
			
			//Load language file.
			JPlugin::loadLanguage( 'plg_goals', JPATH_ADMINISTRATOR );

			$lang = JFactory::getLanguage();
			$lang->load('com_goals', JPATH_SITE);

			//Attach JS & CSS
			$this->doc->addScript( JURI::root() . 'plugins/community/goals/goals/goalswindow.js' );
            $this->doc->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/joomsocplugins.css');

			//If JoomBlog not exists
			if(!file_exists($this->_path . DIRECTORY_SEPARATOR . 'goals.php' ) ) {
				$content = "<table class='mygoalposts-notice'>
								<tr>
									<td>
										<img src='".JURI::root()."components/com_community/assets/error.gif' alt='' />
									</td>
									<td> " .JText::_('PLG_GOALS_GOALSNOTFOUND') . "</td>
								</tr>
							</table>";
			} else {
				require_once($this->_fepath.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'goals.php');
				//Get Goals
				$items = $this->_getGoals();

				//Get owner
				$isOwner	= ($this->my->id == $this->_user->id ) ? true : false;
				$ownerId 	= (int) $this->_user->id;
				$ownerMame	= $this->_getUsernameById($ownerId);

				//Cache work
				$caching = $this->params->get('cache', 1);
				if ($caching) {
					$caching = JFactory::getApplication()->getCfg('caching');
				}

				$cache = JFactory::getCache('plgCommunityGoals');
				$cache->setCaching($caching);
				$callback = array('plgCommunityGoals', '_getGoalsHTML');
				$content = $cache->call($callback, $items, $isOwner, $this->_user->id, $ownerMame, $this->params);
			}

			return $content;
		}

		private function _getGoals()
		{
			$date 	= JFactory::getDate();
			$user 	= JFactory::getUser();
			$limit 	= $this->params->get('count', 5);

			// Select required fields from the categories.
			$query = GoalsHelper::getListQuery("goals",$this->db, $this->_user);
			$this->db->setQuery($query,0,$limit);
			$goals  = $this->db->loadObjectList();

			$jdate 	 = new JDate('now');
			$nowdate =  $jdate->Format('Y-m-d');
		
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

			if($this->db->getErrorNum()) {
				JError::raiseError( 500, $this->db->stderr());
			}

			return $goals;
		}

		function _getGoalsHTML($items=null, $isOwner=false, $uid=0, $ownerMame='', $params=null)
		{
			$html = "";

			if ($isOwner) {
				$goalModerateLink = "javascript:goalsShowWindow('".JRoute::_("index.php?option=com_goals&task=adminhome&tmpl=component")."');";
				$html .= '<div class="gl_dashboard_buttons">
							<a class="gl_new_href" href="javascript:void(0);" onclick=\'javascript:goalsShowWindow("'.JRoute::_('index.php?option=com_goals&view=editgoal&tmpl=component').'")\' >'.JHTML::tooltip(JText::_('COM_GOALS_NEW_GOAL_DESCR'), '', '', JText::_('COM_GOALS_NEW_GOAL')).'</a>
							<a class="gl_new_href" href="javascript:void(0);" onclick=\'javascript:goalsShowWindow("'.JRoute::_('index.php?option=com_goals&view=editmilistone&tmpl=component').'")\' >'.JHTML::tooltip(JText::_('COM_GOALS_NEW_MILISTONE_DESCR'), '', '', JText::_('COM_GOALS_NEW_MILISTONE')).'</a>
							<a class="gl_new_href" href="javascript:void(0);" onclick=\'javascript:goalsShowWindow("'.JRoute::_('index.php?option=com_goals&view=editrecord&tmpl=component').'")\' >'.JHTML::tooltip(JText::_('COM_GOALS_NEW_RECORD_DESCR'), '', '', JText::_('COM_GOALS_NEW_RECORD')).'</a>';
				$html .= '</div>';
				$html .= '<div class="gl_dashboard_buttons">';
				$c_link = JRoute::_('index.php?option=com_goals&view=calendar&tmpl=component');
				$html .= '<a href="javascript:void(0);" onclick=\'javascript:goalsShowWindow("'.$c_link.'")\' class="gl_calendar_href">'.JHTML::tooltip(JText::_('COM_GOALS_MY_CALENDAR'), '', '', JText::_('COM_GOALS_MY_CALENDAR')).'</a> ';
				$f_link = JRoute::_('index.php?option=com_goals&view=userfields&tmpl=component');
				$html .= '<a href="javascript:void(0);" onclick=\'javascript:goalsShowWindow("'.$f_link.'")\' class="gl_fields_href">'.JHTML::tooltip(JText::_('COM_GOALS_MY_FIELDS'), '', '', JText::_('COM_GOALS_MY_FIELDS')).'</a> ';
				$html .= '</div><br />';
			}
            $settings = GoalsHelper::getSettings();
			if(!empty($items)) {
				$limit = $params->get('count', 5);
				$t=0;

				foreach($items as $goal) {
					if ($t>$limit) break;
					$t++;

					$status  = $goal->status;

                    if ($goal->is_complete) {
                        $goal->status_image = 'completed';
                    } else {
                        if ($now_date<$date_ahead) {
                            $goal->status_image = 'ahead_plan';
                        } elseif ($now_date>$date_behind) {
                            $goal->status_image = 'behind_plan';
                        } else { $goal->status_image = 'just_in_time';}
                    }
					
					$glink = JRoute::_('index.php?option=com_goals&view=goal&tmpl=component&id='.$goal->id.'&u='.$uid);
					$goalglink = "javascript:goalsShowWindow('".$glink."');";
					if ($isOwner) {
						$goalglink_html = "<a href=\"javascript:void(0);\" onclick=\"javascript:goalsShowWindow('".$glink."');\">$goal->title</a>";
					}else{
						$goalglink_html = "<a href=\"javascript:void(0);\" onclick=\"javascript:void(0);\">$goal->title</a>";
					}

					$html .= '<div class="gl_goals-item">
								<div class="gl_goal_togglers">
									<div class="gl_goal_status_left_part">
										<div class="gl_goal_'.$goal->status_image.'">&nbsp;</div>
									</div>
									<div class="gl_goal_left_part">
										<div class="gl_goal_title">
											'.$goalglink_html.'
										</div>
										<div class="gl_goal_short_details">';
								if ($isOwner && ($goal->status_image != 'complete')) {
									$html .= '<div class="gl_left_count">';
									if (isset($goal->left)) {
										$html .= $goal->left;
									}
									$html .= '</div>';
								}
					$html .= '			</div>
										<div style="clear:both"></div>
									</div>
                                    <div class="gl_goal_progress">
										<div class="pb_width">
											<div class="progress progress-small progress-striped">
												<div style="width: '.$goal->percent.'%; color:'.$goal->status.'" class="bar">&nbsp;</div>
											</div>
										</div>
										<div class="progressbar_label_right">'.$goal->percent.'%</div>
									</div>

								</div>
							  </div>';
				}
			} else {
				$html .= '<div class="gl_left_count">'.JText::_('PLG_GOALS_NO_GOALS').'</div>';
			}
			$html .= '<div style="clear:both;"></div>';

			return $html;
		}

		function onAppDisplay()
		{
			ob_start();
			$limit=0;
			$html= $this->onProfileDisplay($limit);

			echo $html;

			$content	= ob_get_contents();
			ob_end_clean();

			return $content;
		}


		private function _getUsernameById($ownerId=42)
		{
			$db		= JFactory::getDBO();
			$query	= "SELECT `username` FROM #__users WHERE `id`='$ownerId' ";
			$db->setQuery( $query );

			return $db->loadResult();
		}
	}
}
