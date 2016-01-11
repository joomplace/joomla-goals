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

if(!class_exists('plgCommunityHabits'))
{
	class plgCommunityHabits extends CApplications
	{
		var $_user		= null;
		var $_name		= "Habits";
		var $name		= "Habits"; //old versions
		var $_path		= '';
		var $db			= null;
		var $doc		= null;
		var $my			= null;
		var $_count		= 5;

		function plgCommunityHabits($subject, $config)
		{
			$this->_user	= CFactory::getRequestUser();
			$this->db   	= JFactory::getDBO();
			$this->doc		= JFactory::getDocument();
			$this->my		= CFactory::getUser();
			$this->_path	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_goals';
			$this->_fepath	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_goals';

			parent::__construct($subject, $config);
		}

		function onProfileDisplay()
		{
			$itemid = JRequest::getInt('Itemid', 0);
			$user = CFactory::getUser();

			//Load language file.
			JPlugin::loadLanguage( 'plg_habits', JPATH_ADMINISTRATOR );

			$lang = JFactory::getLanguage();
			$lang->load('com_goals', JPATH_SITE);

			//Attach JS & CSS
		    $this->doc->addScript( JURI::root() . 'plugins/community/habits/habits/goalswindow.js' );
            $this->doc->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/joomsocplugins.css');

			//If JoomBlog not exists
			if(!file_exists($this->_path . DIRECTORY_SEPARATOR . 'goals.php' ) ) {
				$content = "<table class='mygoalposts-notice'>
								<tr>
									<td>
										<img src='".JURI::root()."components/com_community/assets/error.gif' alt='' />
									</td>
									<td> " .JText::_('PLG_HABITS_GOALSNOTFOUND') . "</td>
								</tr>
							</table>";
			} else {
				require_once($this->_fepath.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'goals.php');
				//Get Habits
				$items = $this->_getHabits();

				//Get owner
				$isOwner	= ($this->my->id == $this->_user->id ) ? true : false;
				$ownerId 	= (int) $this->_user->id;
				$ownerMame	= $this->_getUsernameById($ownerId);

				//Cache work
				$caching = $this->params->get('cache', 1);
				if ($caching) {
					$caching = JFactory::getApplication()->getCfg('caching');
				}

				$cache = JFactory::getCache('plgCommunityHabits');
				$cache->setCaching($caching);
				$callback = array('plgCommunityHabits', '_getHabitsHTML');
				$content = $cache->call($callback, $items, $isOwner, $this->_user->id, $ownerMame, $this->params);
			}

			return $content;
		}

		private function _getHabits()
		{
			$date = JFactory::getDate();
			$user = JFactory::getUser();
			if (!$this->_user->id) {
			$this->_user->id = $user->id;
			}
			$limit = $this->params->get('count', 5);

			// Select required fields from the categories.
			$query	= $this->db->getQuery(true);
            $query->select('h.*');
            $query->from('`#__goals_habits` AS `h`');
            $query->where('`h`.`complete`=0');
            $query->where('`h`.`uid`='.(int)$this->_user->id);
            $query->order('`h`.`type` DESC');
			$this->db->setQuery($query);
			$items  = $this->db->loadObjectList();
			$jdate 	 = new JDate('now');
			$nowdate =  $jdate->Format('Y-m-d');



            if (sizeof($items))
            {

                $s4 = 0;
                $habits = array();
                $nw = $jdate->Format('w');
                if ($nw==0) $nw=7;
                foreach ( $items as $hab )
                {
                    $hab->complete_count = 0;
                    $hab->procent = 0;
                    $hab->todaydid = false;
                    if ($hab->days)
                    {

                        $days = explode(',', $hab->days);

                        if (in_array($nw,$days))
                        {
                            $completes = GoalsHelper::getHabitLog($hab->id);

                            if ($completes)
                            {
                                if ($this->getTodayHabit($hab->id)) {
                                    $hab->todaydid = true;
                                }
                                $hab->complete_count= $completes;
                                if ($hab->finish>0) $hab->procent = round(($completes/$hab->finish)*100);
                                if ($hab->procent>=100) $hab->complete = 1;
                            }
                            if (!$hab->complete) $habits[] = $hab;
                        }

                    }

                }


                $items=$habits;
            }


			if($this->db->getErrorNum()) {
				JError::raiseError( 500, $this->db->stderr());
			}

			return $items;
		}

		function _getHabitsHTML($items=null, $isOwner=false, $uid=0, $ownerMame='', $params=null)
		{
            $html = "";

            if ($isOwner) {
                $html .= '<div class="gl_dashboard_buttons">
                            <a class="gl_new_href" href="javascript:void(0);" onclick=\'javascript:goalsShowWindow("'.JRoute::_('index.php?option=com_goals&view=edithabit&tmpl=component').'")\' >'.JHTML::tooltip(JText::_('COM_GOALS_NEW_HABIT_DESCR'), '', '', JText::_('COM_GOALS_NEW_HABIT')).'</a>
                            <a class="gl_new_href" href="javascript:void(0);" onclick=\'javascript:goalsShowWindow("'.JRoute::_('index.php?option=com_goals&view=managehabits&tmpl=component').'")\' >'.JHTML::tooltip(JText::_('COM_GOALS_MANAGE_HABITS_DESCR'), '', '', JText::_('COM_GOALS_MANAGE_HABITS')).'</a>';
                $html .= '</div><br />';
            }

            $html .= '<div id="goals-wrap"><ul class="goals-formedlist goals-habit-fortoday">';
        foreach($items as $habit) {


            switch ( $habit->type )
            {
                case '+': $status_style = 'good'; break;
                default:  $status_style = 'bad';break;
            }

            $procent = $habit->procent;

        $html.='<li>
            <div class="habit-item clearfix">
                <div class="goals-left">
                    <h6>'.$habit->title.'</h6>
                </div>
                <div class="right-wrapper clearfix">
                    <div class="habit-item-status">
                        <div class="goals-checkblock">'; 
							if ($isOwner) {
							    if ($habit->todaydid===false) {
                                        $html.='<span class="check-task check-task-habits checkbox_off_'.$status_style.'">
                                            <input type="checkbox" class="check-done" id="check-hab'.$habit->id.'" name="check-hab'.$habit->id.'" value="no">';
                            } else {
                                        $html.='<span class="check-task check-task-habits checkbox_on_'.$status_style.'">
                                            <input type="checkbox" class="check-done" id="check-hab'.$habit->id.'" name="check-hab'.$habit->id.'" value="yes">';
                             }
							} else {
							    if ($habit->todaydid===false) {
                                        $html.='<span class="check-task check-task-habits checkbox_off_'.$status_style.'">
                                            <input type="checkbox" class="check-done" id="check-hab'.$habit->id.'" name="check-hab'.$habit->id.'" value="no" disabled="disabled">';
                            } else {
                                        $html.='<span class="check-task check-task-habits checkbox_on_'.$status_style.'">
                                            <input type="checkbox" class="check-done" id="check-hab'.$habit->id.'" name="check-hab'.$habit->id.'" value="yes" disabled="disabled">';
                             }
							}

                         $html.='</span>
                            <label class="checkbox" for="check-hab'.$habit->id.'">
                              '.$habit->complete_count.' '.JText::_('COM_GOALS_TODAY_HABIT_CHECKS').'
                            </label>
                        </div>

                        <div class="gl_goal_progress">
                            <div class="pb_width">
                                <div class="progress progress-small progress-striped">
                                    <div style="width: '.$procent.'%;" class="bar">&nbsp;</div>
                                </div>
                            </div>
                            <div class="progressbar_label_right">'.$procent.'%</div>
                        </div>
                    </div>
                </div>
            </div>
        </li>';




        }

            ?>
        <script>

            joms.jQuery(document).ready(function(){
                /*today - habits for today checkboxes*/
                joms.jQuery(".check-task-habits").click(function(){
                    console.log(joms.jQuery(this));
                    var arrId = joms.jQuery(this).parent().find('input').attr('id').split('check-hab');
                    if(joms.jQuery(this).hasClass("checkbox_off_good")){

                        joms.jQuery(this).addClass("checkbox_on_good").removeClass("checkbox_off_good");
                        change_habit_status(arrId[1],1,joms.jQuery(this).parent().find('input').attr('id'));
                    }
                    else {
                        if(joms.jQuery(this).hasClass("checkbox_on_good")){
                            joms.jQuery(this).addClass("checkbox_off_good").removeClass("checkbox_on_good");
                            change_habit_status(arrId[1],0,joms.jQuery(this).parent().find('input').attr('id'));

                        }
                        else{
                            if(joms.jQuery(this).hasClass("checkbox_off_bad")){
                                joms.jQuery(this).addClass("checkbox_on_bad").removeClass("checkbox_off_bad");
                                change_habit_status(arrId[1],1,joms.jQuery(this).parent().find('input').attr('id'));
                            }
                            else{
                                if(joms.jQuery(this).hasClass("checkbox_on_bad")){
                                    joms.jQuery(this).addClass("checkbox_off_bad").removeClass("checkbox_on_bad");
                                    change_habit_status(arrId[1],0,joms.jQuery(this).parent().find('input').attr('id'));
                                }}
                        }
                    }

                });

            });
            function change_habit_status(hid,type,container)
            {
                if (hid)
                {
                    var url="<?php echo JURI::root()?>/index.php?option=com_goals&task=habit.addstatus&tmpl=component";
                    var dan="hid=" + hid + "&t="+ type;

                    joms.jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: dan,
                        success: function(responce){
                            resp_array =  responce.split(';');
                            joms.jQuery('#'+container).parent().parent().find('label').html(resp_array[0] + ' <?php echo JText::_('COM_GOALS_TODAY_HABIT_CHECKS') ?>');
                            joms.jQuery('#'+container).parent().parent().parent().find('div.bar').css('width',resp_array[1] + '%');
                            joms.jQuery('#'+container).parent().parent().parent().find('div.progressbar_label_right').html(resp_array[1] + '%');
                        }
                    });
                }
            }
        </script>
        <?php

        if (!count($items))  $html.='<li>'.JText::_('COM_GOALS_TODAY_NO_HABITS').'</li>';




            $html.= '</ul></div>';
            $document = JFactory::getDocument();

            ?>

            <?php

			return $html;
		}

		function onAppDisplay()
		{
			ob_start();
			$limit=0;
			$html= $this->onProfileDisplay($limit);
			echo $html;
            ?>

            <?php
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

		function onSystemStart()
		{
			if (JFactory::getApplication()->input->get('view') == 'profile' && !JFactory::getApplication()->input->get('task')){
				JFactory::getDocument()->addScript(JUri::base() . 'plugins/community/habits/habits/script.js?v3');
			}
		}

		function ajaxChangeStatus($response, $id=0, $type=0)
		{
			$hid	= $id;
			$t		= $type;
			$date	= date('Y-m-d H:i:s');

			$lang = JFactory::getLanguage();
			$lang->load('com_goals', JPATH_SITE);

			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			$query->select('h.*');
			$query->from('`#__goals_habits` AS `h`');
			$query->where('`h`.`id`='.$hid);
			$db->setQuery($query);
			$habit = $db->loadObject();

			if ($habit->type == '+') {
				if ($t == 1) {
					$st ='<div class="gl_ok gl_left_link"></div>';
				} else {
					$st='<div class="gl_bad gl_left_link"></div>';
				}
			} else {
				if ($t==1) {
					$st='<div class="gl_bad gl_left_link"></div>';
				} else {
					$st='<div class="gl_none gl_left_link"></div>';
				}
			}

			$query	= $db->getQuery(true);
			$query->insert('`#__goals_habits_log`');
			$query->set('`hid`='.$hid);
			$query->set('`date`="'.$date.'"');
			$query->set('`status`='.$t);
			$db->setQuery($query);
			$db->query();

			if ($t==0) $msg = $st.JText::_('COM_GOALS_HABIT_MES_NODID');
			else  $msg =  $st.JText::_('COM_GOALS_HABIT_MES_DID');
			$response->addAssign('habit_stat_'.$id,'innerHTML' , $msg);

		 	return $response;
		}

        public function getTodayHabit($id) {
            $day = date('Y-m-d');
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__goals_habits_log');
            $query->where('hid='.$id);
            $query->where('date LIKE "'.$day.'%"');
            $db->setQuery($query);
            $result = $db->loadObjectList();

            return $result?1:0;

        }
	}

}
