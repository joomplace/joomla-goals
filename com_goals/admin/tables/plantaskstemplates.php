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

class GoalsTablePlanTasksTemplates extends JTable
{
    function __construct(&$db)
    {
        parent::__construct('#__goals_plantaskstemplates', 'id', $db);
    }


    function store($updateNulls = false)
    {
        $isnew = false;
        if (!$this->id) $isnew = true;
        $user = JFactory::getUser();

        if (parent::store()) {

            $settings = GoalsHelper::getSettings();
            $db = $this->_db;

            $sid = $this->sid;


            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__goals_stagestemplates');
            $query->where('`id`=' . (int)$sid);
            $db->setQuery($query);
            $pid = $db->loadObject();

            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__goals_planstemplates');
            $query->where('`id`=' . (int)$pid->pid);
            $db->setQuery($query);
            $g = $db->loadObject();

            $query = $db->getQuery(true);

                $query->select('count(*)');
                $query->from('#__goals_plantaskstemplates');
                $query->where('sid='.$this->sid);
                $query->where('status=1');

                $db->setQuery($query);
                $count = $db->loadResult();

                $query = $db->getQuery(true);

                $query->select('count(*)');
                $query->from('#__goals_plantaskstemplates');
                $query->where('sid='.$this->sid);

                $db->setQuery($query);
                $count_all = $db->loadResult();
                if ($count_all==$count) {
                    $query = 'UPDATE #__goals_stagestemplates SET status=1 WHERE id='.$this->sid;
                    $db->setQuery($query);
                    $db->query();

                } else {
                    $query = 'UPDATE #__goals_stagestemplates SET status=0 WHERE id='.$this->sid;
                    $db->setQuery($query);
                    $db->query();
                }

            $query = $db->getQuery(true);
            $query->select('count(*)');
            $query->from('#__goals_stagestemplates');
            $query->where('pid='.$pid->pid);
            $query->where('status=1');
            $db->setQuery($query);
            $count = $db->loadResult();


            $query = $db->getQuery(true);
            $query->select('count(*)');
            $query->from('#__goals_stagestemplates');
            $query->where('pid='.$pid->pid);

            $db->setQuery($query);
            $count_all = $db->loadResult();


            if ($count_all==$count) {
                $query = 'UPDATE #__goals_planstemplates SET is_complete=1 WHERE id='.$pid->pid;
                $db->setQuery($query);
                $db->query();
            }  else {
                $query = 'UPDATE #__goals_planstemplates SET is_complete=0 WHERE id='.$pid->pid;
                $db->setQuery($query);
                $db->query();
            }

            $cid = $g->cid;

            $fields = GoalsHelper::getCustomTaskFields($cid);
            $userfields = GoalsHelper::getCustomTaskUserFields($pid->pid);

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

                }
            }

            return true;
        } else return false;
    }
}