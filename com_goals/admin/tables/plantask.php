<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');
JLoader::register('GoalsHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'goals.php');

class GoalsTablePlanTask extends JTable
{
    function __construct(&$db)
    {
        parent::__construct('#__goals_plantasks', 'id', $db);
    }


    function store($updateNulls = false)
    {

        $isnew = false;
        if (!$this->id) $isnew = true;
        $user = JFactory::getUser();
        ;
        if (JFactory::getApplication()->isSite() && $this->status && $this->c_date=='0000-00-00 00:00:00') {
            $this->c_date = date('Y-m-d H:i:s', time());
        }

        $componentParams = &JComponentHelper::getParams('com_goals');
        $this->result_mode = $componentParams->get('result_mode', 1);

        if (JFactory::getApplication()->isSite() && !$this->status && $this->c_date!='0000-00-00 00:00:00') {
            $this->c_date = '0000-00-00 00:00:00';
        }
        if (parent::store()) {
            $settings = GoalsHelper::getSettings();
            $db = $this->_db;

            $sid = $this->sid;


            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__goals_stages');
            $query->where('`id`=' . (int)$sid);
            $db->setQuery($query);
            $pid = $db->loadObject();

            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__goals_plans');
            $query->where('`id`=' . (int)$pid->pid);
            $db->setQuery($query);
            $g = $db->loadObject();

            $query = $db->getQuery(true);

                $query->select('count(*)');
                $query->from('#__goals_plantasks');
                $query->where('sid='.$this->sid);
                $query->where('status=1');

                $db->setQuery($query);
                $count = $db->loadResult();

                $query = $db->getQuery(true);

                $query->select('count(*)');
                $query->from('#__goals_plantasks');
                $query->where('sid='.$this->sid);

                $db->setQuery($query);
                $count_all = $db->loadResult();
                if ($count_all==$count) {
                    $query = 'UPDATE #__goals_stages SET status=1 WHERE id='.$this->sid;
                    $db->setQuery($query);
                    $db->query();

                } else {
                    $query = 'UPDATE #__goals_stages SET status=0 WHERE id='.$this->sid;
                    $db->setQuery($query);
                    $db->query();
                }

            $query = $db->getQuery(true);
            $query->select('count(*)');
            $query->from('#__goals_stages');
            $query->where('pid='.$pid->pid);
            $query->where('status=1');
            $db->setQuery($query);
            $count = $db->loadResult();


            $query = $db->getQuery(true);
            $query->select('count(*)');
            $query->from('#__goals_stages');
            $query->where('pid='.$pid->pid);

            $db->setQuery($query);
            $count_all = $db->loadResult();


            if ($count_all==$count) {
                if(!$g->is_complete){
					if(is_file(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php'))
					{
						// add points
						require_once( JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php' );
						$stream     = Foundry::stream();
						$template   = $stream->getTemplate();
						$template->setActor( JFactory::getUser()->id , 'user' );
						$template->setContext( 1 , 'achievement' );
						$template->setVerb( 'add' );
						$template->setTitle( JFactory::getUser()->name." ".JText::_('COM_GOALS_USER_JUST_ACCOMPISHED')." ".$g->title." ".JText::_('COM_GOALS_ACCOMPISHED_PLAN')."!" );
						$template->setContent( $g->desc );

						$stream->add( $template );
					}
                }
                $query = 'UPDATE #__goals_plans SET is_complete=1 WHERE id='.$pid->pid;
                $db->setQuery($query);
                $db->query();
            }  else {
                if($g->is_complete){
					if(is_file(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php'))
					{
						// remove points
						require_once( JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php' );
						$stream     = Foundry::stream();
						$template   = $stream->getTemplate();
						$template->setActor( JFactory::getUser()->id , 'user' );
						$template->setContext( 1 , 'achievement' );
						$template->setVerb( 'delete' );
						$template->setTitle( JFactory::getUser()->name." ".JText::_('COM_GOALS_USER_JUST_UNACCOMPISHED')." ".$g->title." ".JText::_('COM_GOALS_ACCOMPISHED_PLAN')."!" );
						$template->setContent( $g->desc );

						$stream->add( $template );
					}
                }
                $query = 'UPDATE #__goals_plans SET is_complete=0 WHERE id='.$pid->pid;
                $db->setQuery($query);
                $db->query();
            }

            $cid = $g->cid;

            $fields = GoalsHelper::getCustomTaskFieldsPlans($cid);
            $userfields = GoalsHelper::getCustomTaskUserFieldsPlans($pid->pid);

            $ids = $fileids = $fvalues = array();
            if (sizeof($fields)) {
                foreach ($fields as $f) {
                    $ids[] = $f->id;
                    if ($f->type == 'pc') $fileids[] = $f->id;
                }
            }
            if (sizeof($userfields)) {
                foreach ($userfields as $f) {
                    $ids[] = $f->id;
                    if ($f->type == 'pc') $fileids[] = $f->id;
                }
            }

            if (sizeof($fileids)) {
                foreach ($fileids as &$filid) {
                    $file = $_FILES['f_' . $filid];
                    $tmpname = $file['tmp_name'];
                    $name = $file['name'];
                    $size = $file['size'];
                    (($file['error'] == 0) ? $error = false : $error = true);
                    $ext = JFile::getExt($name);
                    $filename = JFile::makeSafe(JFile::stripExt($name));
                    if ($name) {
                        if ($settings->file_exts) {
                            $allext = explode(',', $settings->file_exts);
                            if (!in_array($ext, $allext)) {
                                $error = true;
                                JError::raiseWarning(0, 'Invalid ext. in ' . $name);
                            }
                        }
                        if ($settings->file_sizes) {
                            if (($size / 1024) > $settings->file_sizes) {
                                $error = true;
                                JError::raiseWarning(0, 'Invalid filesize: ' . $name);
                            }
                        }
                    }

                    jimport('joomla.filesystem.folder');
                    jimport('joomla.filesystem.file');

                    if(!defined('DS')) define('DS', '/');

                    if ($error == false) {
                        $folder = JPATH_SITE . DS . 'media' . DS . 'com_goals' . DS . $user->id;
                        if (!JFolder::exists($folder)) JFolder::create($folder);
                        $dest = $folder . DS . $name;
                        $fvalues[$filid] = DS . 'media' . DS . 'com_goals' . DS . $user->id . DS . $name;
                        JFile::copy($tmpname, $dest);

                        $images_width = $settings->images_width ? $settings->images_width : 250;
                        $images_height = $settings->images_height ? $settings->images_height : 250;

                        if (file_exists($dest) && is_file($dest)) {
                            list($width, $height, $type) = getimagesize($dest);

                            if ($width > $images_width) {
                                $new_width = $images_width;
                                $new_height = round(($height * $new_width) / $width);

                                $image_p = imagecreatetruecolor($new_width, $new_height);

                                $background_color = imagecolorallocate($image_p, 255, 255, 255);
                                imagefill ( $image_p, 0, 0, $background_color );
                                switch ($type) {
                                    case 3:
                                        $image = imagecreatefrompng($dest);
                                        break;
                                    case 2:
                                        $image = imagecreatefromjpeg($dest);
                                        break;
                                    case 1:
                                        $image = imagecreatefromgif($dest);
                                        break;
                                    case 6:
                                        $image = imagecreatefromwbmp($dest);
                                        break;
                                }

                                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                                // saving
                                switch ($type) {
                                    case 3:
                                        @imagepng($image_p, $dest);
                                        break;
                                    case 2:
                                        @imagejpeg($image_p, $dest);
                                        break;
                                    case 1:
                                        @imagegif($image_p, $dest);
                                        break;
                                }
                            }
                        }

                    }

                }
            }
            //echo 'SMT DEBUG: <pre>'; print_R($fileids); echo '</pre>';die;

            if (sizeof($ids)) {

                foreach ($ids as $fid) {
                    $vname = 'f_' . $fid;
                    $value = JRequest::getVar($vname);
                    $value = str_replace("'", "`", $value);
                    if (in_array($fid, $fileids)) if ($fvalues[$fid]) $value = $fvalues[$fid]; else continue; //for pictures

                    $value = json_encode($value);
                    //echo 'SMT DEBUG: <pre>'; print_R($value); echo '</pre>';

                    $query = $db->getQuery(true);
                    $query->select('`fid`');
                    $query->from('#__goals_plantasks_xref');
                    $query->where('`fid`=' . (int)$fid);
                    $query->where('`tid`=' . (int)$this->id);
                    $db->setQuery($query);
                    $tmpid = $db->loadResult();

                    //$value = '"' . mysql_escape_string($value) . '"';
                    $value = '"'.$db->escape($value).'"';
                    if ($tmpid) {
                        $query = $db->getQuery(true);
                        $query->update('#__goals_plantasks_xref');
                        $query->where('`fid`=' . (int)$fid);
                        $query->where('`tid`=' . (int)$this->id);
                        $query->set('`values`=' . $value);
                    } else {
                        $query = $db->getQuery(true);
                        $query->insert('#__goals_plantasks_xref');
                        $query->set('`fid`=' . (int)$fid);
                        $query->set('`tid`=' . (int)$this->id);
                        $query->set('`values`=' . $value);
                    }
                    $db->setQuery($query);
                    $db->query();
                    echo 'SMT DEBUG: <pre>';
                    print_R(str_replace('#__', $db->getprefix(), $query->__toString()));
                    echo '</pre>';
                    //if ($fid==4)echo 'SMT DEBUG: <pre>'; print_R($db); echo '</pre>';
                }
            }

            if ($isnew && ($settings->enable_jsoc_int == 2 || $settings->enable_jsoc_int == 4)) {
                /*ACTIVITY STREAM FOR RECORD JS*/
                jimport( 'joomla.filesystem.folder' );
                if (JFolder::exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_community'))
                {
                $glink = 'index.php?option=com_goals&view=plan&id=' . (int)$g->id;
                $app = JApplication::getInstance('site');
                $router = & $app->getRouter();
                $uri = $router->build($glink);
                $glink = $uri->toString();

                $templ_mes = $settings->jsoc_activity_mess_task;
                $a = $b = array();
                $a[] = '{record}';
                $b[] = $this->title;
                $a[] = '{value}';
                $b[] = $this->value;
                $a[] = '{metric}';
                $b[] = $g->metric;
                $a[] = '{plan}';
                $b[] = '<a href="' . $glink . '">' . $g->title . '</a>';
                $mes = $db->quote(str_replace($a, $b, $templ_mes));

                $query = $db->getQuery(true);
                $today = JFactory::getDate();
                $date = $db->quote($today->toSQL());
                $query->insert('#__community_activities');
                $query->set('`actor`=' . (int)$g->uid);
                $query->set('`title`=' . $mes);
                $query->set('`app`="goal"');
                $query->set('`created`=' . $date);
                $query->set('`comment_type`="system.message"');
                $query->set('`like_type`="system.message"');
                $query->set('`points`=0');
                $db->setQuery($query);
                $db->query();

                $id = $db->insertid();
                if ($id) {
                    $query = $db->getQuery(true);
                    $query->update('#__community_activities');
                    $query->where('`id`=' . (int)$id);
                    $query->set('`like_id`=' . (int)$id);
                    $query->set('`comment_id`=' . (int)$id);
                    $db->setQuery($query);
                    $db->query();
                }
                }
            }

            return true;
        } else return false;
    }

    public function complete( $cid=null, $status=1, $user_id=0 )
    {
        JArrayHelper::toInteger( $cid );
        $user_id        = (int) $user_id;
        $approve        = (int) $status;
        $k              = $this->_tbl_key;

        if (count( $cid ) < 1)
        {
            if ($this->$k) {
                $cid = array( $this->$k );
            } else {
                $this->setError("No items selected.");
                return false;
            }
        }

        $cids = $k . '=' . implode( ' OR ' . $k . '=', $cid );

        $query = 'UPDATE '. $this->_tbl
            . ' SET status = ' . (int) $status
            . ' WHERE ('.$cids.')'
        ;

        $checkin = in_array( 'checked_out', array_keys($this->getProperties()) );
        if ($checkin)
        {
            $query .= ' AND (checked_out = 0 OR checked_out = '.(int) $user_id.')';
        }

        $this->_db->setQuery( $query );
        if (!$this->_db->query())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        if (count( $cid ) == 1 && $checkin)
        {
            if ($this->_db->getAffectedRows() == 1) {
                $this->checkin( $cid[0] );
                if ($this->$k == $cid[0]) {
                    $this->status = $status;
                }
            }
        }

        GoalsHelper::updateStage($this->sid);

        $this->setError('');
        return true;
    }
}