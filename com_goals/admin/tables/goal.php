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

class GoalsTableGoal extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__goals', 'id', $db);
	}

	public function store($updateNulls = false)
	{
		$uids = array();
		if(is_array($this->uid) && count($this->uid)>1){
			$uids = $this->uid;
		}else{
			$this->uid = $this->uid[0];
		}
		
		/* code duplication - bad but quick - anyway refactoring is coming */
		if($uids){
			foreach($uids as $uid){
				$this->id = '';
				$this->uid = $uid;
				
				$settings = GoalsHelper::getSettings();
				if (!$this->uid)
				{
					$this->uid=JFactory::getUser()->id;
				}

				$isnew = false;
				if (!$this->id) $isnew = true;

				$form = JRequest::getVar('jform');

				if (parent::store($updateNulls)){
						/*** GOALS XREF ***/

						$userfields = (array)$form['userfields'];
						if(sizeof($userfields))
						{
							$db = $this->_db;
							$query = $db->getQuery(true);
								$query->delete('#__goals_xref');
								$query->where('gid='.$this->id);
								$db->setQuery($query);
							$db->query();

							foreach ( $userfields as $fid )
							{
								$query = $db->getQuery(true);
									$query->insert('#__goals_xref');
									$query->set('`gid`='.$db->quote($this->id).', `fid`='.$db->quote($fid));
									$db->setQuery($query);
								$db->query();

							}
						}

						if ($form['template']) {
							$db = JFactory::getDbo();
							$query = $db->getQuery(true);
							$query->select('*')->from('#__goals_milistonestemplates')->where('gid='.$form['template']);
							$db->setQuery($query);
							$milistones = $db->loadObjectList();
							if ($milistones) {
								foreach ($milistones as $milistone) {
									$insertObj = new JObject();
									/* Creating new object to store in the database */
									$insertObj->id = null;
									$insertObj->gid = $this->id;
									$insertObj->duedate = date('Y-m-d', time()+((int)$milistone->dayto)*24*60*60);
									$insertObj->title = $milistone->title;
									$insertObj->description = $milistone->description;
									$insertObj->status = $milistone->status;
									$insertObj->value = $milistone->value;
									$insertObj->color = $milistone->color;
									$insertObj->cdate = $milistone->cdate;
									$db->insertObject('#__goals_milistones',$insertObj);
								}
							}
						}

					if ($settings->enable_jsoc_int==1 || $settings->enable_jsoc_int==4)
					{
					/*ACTIVITY STREAM FOR GOAL JS*/
					jimport( 'joomla.filesystem.folder' );
					if (JFolder::exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_community'))
					{
					$glink = 'index.php?option=com_goals&view=goal&id='.(int)$this->id;

					$templ_mes = $settings->jsoc_activity_mess_goal;
					$a = $b = array();
					$a[] = '{goal}';		$b[] = '<a href="'.$glink.'">'.$this->title.'</a>';
					$a[] = '{due_date}';	$b[] = $this->deadline;

					$db = JFactory::getDBO();

					$mes = $db->quote(str_replace($a,$b,$templ_mes));

						$query = $db->getQuery(true);
						$today = JFactory::getDate();
						$date = $db->quote($today->toSQL());
						$query->insert('#__community_activities');
						$query->set('`actor`='.(int)$this->uid);
						$query->set('`title`='.$mes);
						$query->set('`app`="goal"');
						$query->set('`created`='.$date);
						$query->set('`comment_type`="system.message"');
						$query->set('`like_type`="system.message"');
						$query->set('`points`=0');
						$db->setQuery($query);
						$db->query();

						$id = $db->insertid();
						if ($id)
						{
							$query = $db->getQuery(true);
							$query->update('#__community_activities');
							$query->where('`id`='.(int)$id);
							$query->set('`like_id`='.(int)$id);
							$query->set('`comment_id`='.(int)$id);
							$db->setQuery($query);
							$db->query();
						}
					}



					}
				}
				else return false;
			}
		}else{
			$settings = GoalsHelper::getSettings();
			if (!$this->uid)
			{
				$this->uid=JFactory::getUser()->id;
			}

			$isnew = false;
			if (!$this->id) $isnew = true;

			$form = JRequest::getVar('jform');

			if (parent::store($updateNulls)){
					/*** GOALS XREF ***/

						$userfields = (array)$form['userfields'];
							if(sizeof($userfields))
							{
								$db = $this->_db;
								$query = $db->getQuery(true);
									$query->delete('#__goals_xref');
									$query->where('gid='.$this->id);
									$db->setQuery($query);
								$db->query();

								foreach ( $userfields as $fid )
								{
									$query = $db->getQuery(true);
										$query->insert('#__goals_xref');
										$query->set('`gid`='.$db->quote($this->id).', `fid`='.$db->quote($fid));
										$db->setQuery($query);
									$db->query();

								}
							}

						if ($form['template']) {
							$db = JFactory::getDbo();
							$query = $db->getQuery(true);
							$query->select('*')->from('#__goals_milistonestemplates')->where('gid='.$form['template']);
							$db->setQuery($query);
							$milistones = $db->loadObjectList();
							if ($milistones) {
								foreach ($milistones as $milistone) {
									$insertObj = new JObject();
									/* Creating new object to store in the database */
									$insertObj->id = null;
									$insertObj->gid = $this->id;
									$insertObj->duedate = date('Y-m-d', time()+((int)$milistone->dayto)*24*60*60);
									$insertObj->title = $milistone->title;
									$insertObj->description = $milistone->description;
									$insertObj->status = $milistone->status;
									$insertObj->value = $milistone->value;
									$insertObj->color = $milistone->color;
									$insertObj->cdate = $milistone->cdate;
									$db->insertObject('#__goals_milistones',$insertObj);
								}
							}
						}

					/*IMAGE*/
						/*
						if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.$this->image) && is_file(JPATH_SITE.DIRECTORY_SEPARATOR.$this->image))
						{

							$images_width = $settings->images_width?$settings->images_width:250;
							$images_height = $settings->images_height?$settings->images_height:250;

							$filepath = JPATH_SITE.DIRECTORY_SEPARATOR.$this->image;
											if (file_exists($filepath) &&  is_file($filepath))
											{
											list($width, $height, $type) = getimagesize($filepath);

												if ($width > $images_width){
													$new_width = $images_width;
													$new_height = round(($height*$new_width)/$width);

													$image_p = imagecreatetruecolor($new_width, $new_height);

													$background_color = imagecolorallocate($image_p, 255, 255, 255);
													imagefill ( $image_p, 0, 0, $background_color );

													switch ($type)
													{
														case 3:
															$image = imagecreatefrompng($filepath);
														break;
														case 2:
															$image = imagecreatefromjpeg($filepath);
														break;
														case 1:
															$image = imagecreatefromgif($filepath);
														break;
														case 6:
															$image = imagecreatefromwbmp($filepath);
														break;
													}

													imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

													// saving
													switch($type)
													{
														case 3:
															@imagepng($image_p, $filepath);
														break;
														case 2:
															@imagejpeg($image_p, $filepath);
														break;
														case 1:
															@imagegif($image_p, $filepath);
														break;
													}
												}
											}

						}
						*/

				if ($settings->enable_jsoc_int==1 || $settings->enable_jsoc_int==4)
				{
				/*ACTIVITY STREAM FOR GOAL JS*/
				jimport( 'joomla.filesystem.folder' );
				if (JFolder::exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_community'))
				{
				$glink = 'index.php?option=com_goals&view=goal&id='.(int)$this->id;

				$templ_mes = $settings->jsoc_activity_mess_goal;
				$a = $b = array();
				$a[] = '{goal}';		$b[] = '<a href="'.$glink.'">'.$this->title.'</a>';
				$a[] = '{due_date}';	$b[] = $this->deadline;

				$db = JFactory::getDBO();

				$mes = $db->quote(str_replace($a,$b,$templ_mes));

					$query = $db->getQuery(true);
					$today = JFactory::getDate();
					$date = $db->quote($today->toSQL());
					$query->insert('#__community_activities');
					$query->set('`actor`='.(int)$this->uid);
					$query->set('`title`='.$mes);
					$query->set('`app`="goal"');
					$query->set('`created`='.$date);
					$query->set('`comment_type`="system.message"');
					$query->set('`like_type`="system.message"');
					$query->set('`points`=0');
					$db->setQuery($query);
					$db->query();

					$id = $db->insertid();
					if ($id)
					{
						$query = $db->getQuery(true);
						$query->update('#__community_activities');
						$query->where('`id`='.(int)$id);
						$query->set('`like_id`='.(int)$id);
						$query->set('`comment_id`='.(int)$id);
						$db->setQuery($query);
						$db->query();
					}
				}



				}
					return true;
			}
			else return false;
		}
		return true;
	}


    public function complete($pks = null, $state = 1, $userId = 0)
    {
        $k = $this->_tbl_keys;

        if (!is_null($pks))
        {
            foreach ($pks AS $key => $pk)
            {
                if (!is_array($pk))
                {
                    $pks[$key] = array($this->_tbl_key => $pk);
                }
            }
        }

        $userId = (int) $userId;
        $state  = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks))
        {
            $pk = array();

            foreach ($this->_tbl_keys AS $key)
            {
                if ($this->$key)
                {
                    $pk[$this->$key] = $this->$key;
                }
                // We don't have a full primary key - return false
                else
                {
                    return false;
                }
            }

            $pks = array($pk);
        }

        foreach ($pks AS $pk)
        {
            // Update the publishing state for rows with the given primary keys.
            $query = $this->_db->getQuery(true)
                ->update('#__goals')
                ->set('`is_complete` = ' . (int) $state);

            // Determine if there is checkin support for the table.
            if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time'))
            {
                $query->where('(checked_out = 0 OR checked_out = ' . (int) $userId . ')');
                $checkin = true;
            }
            else
            {
                $checkin = false;
            }

            // Build the WHERE clause for the primary keys.
            $this->appendPrimaryKeys($query, $pk);

            $this->_db->setQuery($query);
            $this->_db->execute();

            // If checkin is supported and all rows were adjusted, check them in.
            if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
            {
                $this->checkin($pk);
            }

            $ours = true;

            foreach ($this->_tbl_keys AS $key)
            {
                if ($this->$key != $pk[$key])
                {
                    $ours = false;
                }
            }

            if ($ours)
            {
                $this->published = $state;
            }
        }

        $this->setError('');

        return true;
    }

}