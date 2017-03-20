<?php
/**
* Goals component for Joomla 3.x
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

class GoalsHelper
{
	public static function addSubmenu($submenu) 
	{

	}

	public static function getActions($categoryId = 0, $postId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($postId) && empty($categoryId)) {
			$assetName = 'com_goals';
		}
		else if (empty($postId)) {
			$assetName = 'com_goals.category.'.(int)$categoryId;
		}
		else {
			$assetName = 'com_goals.article.'.(int)$postId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}

    public static function getVersion()
    {
        $params = self::getManifest();
        if (isset($params->version)) {
            $version_array = explode('.', $params->version);
            $build = $version_array[3];
            unset($version_array[3]);
            $new_version = implode('.', $version_array) . ' (build ' . $build . ')';

            return $new_version;
        } else {
            return -1;
        }


    }

    public static function getOriginalVersion()
    {
        $params = self::getManifest();
        if (isset($params->version)) {
            return $params->version;
        } else {
            return -1;
        }
    }


    public static function getMilistones($gid = 0)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select('m.*');

        $query->from('`#__goals_milistones` AS `m`');

        $query->where('`m`.`gid` = ' . $gid);

        $query->order('`m`.`duedate` DESC');

        $db->setQuery($query);



        return $db->loadObjectList();
    }


    public static function getStages($pid = 0, $recursive = false)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('m.*');
        $query->from('`#__goals_stages` AS `m`');
        if($pid) $query->where('`m`.`pid` = ' . $pid);
        //$query->order('`m`.`duedate` DESC');
        $db->setQuery($query);
        $stages = $db->loadObjectList('id');
        if($recursive){
            foreach($stages as &$stage){
                $stage->tasks = self::getStageTasks($stage->id, $recursive);
            }
        }
        return $stages;
    }


    public function getRecords($gid = 0, $limit = 0)
    {

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->select('t.*');

        $query->from('`#__goals_tasks` AS `t`');

        $query->where('`t`.`gid` = ' . $gid);

        $query->order('`t`.`date` ASC');

        if (!$limit) $db->setQuery($query); else $db->setQuery($query, 0, $limit);



        return $db->loadObjectList();

    }

    public function getPlan($id = 0)
    {
        if($id){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.*');
            $query->from('`#__goals_plans` AS `p`');
            $query->where('`p`.`id` = ' . $id);
            $db->setQuery($query);
            return $db->loadObject();
        }
        else return false;
    }
    public function getGoal($id = 0)
    {
        if($id){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('g.*');
            $query->from('`#__goals` AS `g`');
            $query->where('`g`.`id` = ' . $id);
            $db->setQuery($query);
            return $db->loadObject();
        }
        else return false;
    }

    public function updateGoal($id){
        $goal = new stdClass();
        $goal = GoalsHelper::getGoal($id);
        $goalTable = JTable::getInstance('goal', 'GoalsTable');
        $milistoneTable = JTable::getInstance('milistone', 'GoalsTable');
        if($goal){
            JLoader::register('GoalsTableGoal', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . 'goal.php');
            JLoader::register('GoalsTableMilistone', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . 'milistone.php');

            $goal->records = GoalsHelper::getRecords($goal->id);
            $goal->milestones = GoalsHelper::getMilistones($goal->id);
            $goal->summary = GoalsHelper::calculateGoal($goal->records, $goal->start, $goal->finish)->summary;
            if(($goal->summary >= $goal->finish && $goal->finish >= $goal->start) || ($goal->summary <= $goal->finish && $goal->finish <= $goal->start)) {
                //GoalsTableGoal::complete(array($goal->id), '1');
                $goalTable->complete(array($goal->id), '1');
            }
            else {
                //GoalsTableGoal::complete(array($goal->id), '0');
                $goalTable->complete(array($goal->id), '0');
            }

            $good_stones = array();
            $bad_stones = array();

            foreach($goal->milestones as $stone){
                if((($stone->value) <= $goal->summary) && ($goal->start <= $goal->finish)){
                    $good_stones[] = $stone->id;
                    if($stone->status == 0)
                        JFactory::getApplication()->enqueueMessage($stone->desc, 'success');
                }
                else{
                    if((($stone->value) >= $goal->summary) && ($goal->start >= $goal->finish)){
                        $good_stones[] = $stone->id;
                        if($stone->status == 0)
                            JFactory::getApplication()->enqueueMessage($stone->desc, 'success');
                    }
                    else $bad_stones[] = $stone->id;
                }
            }

            $milistoneTable->complete($good_stones, '1');
            $milistoneTable->complete($bad_stones, '0');
        }
        else{
            return false;
        }
    }

    public function updatePlan($id){
        $plan = new stdClass();
        $plan = GoalsHelper::getPlan($id);
        if($plan){
            JLoader::register('GoalsTablePlan', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . 'plan.php');
            JLoader::register('GoalsTableStage', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . 'stage.php');

            $plan->stages = self::getStages($plan->id, true);
            foreach($plan->stages as $stage){
                GoalsHelper::updateStage($stage->id,$stage);
                if($stage->tasks) foreach($stage->tasks as $task){
                    if($task->status){
                        $plan->tasks[] = $task;
                    }
                }
            }
            $table = new GoalsTablePlan(JFactory::getDbo());
            $plan->percents = GoalsHelper::calculatePlan($plan->stages,$plan->tasks)->percents;
            if($plan->percents>=100 && !$plan->is_complete) $table->complete(array($plan->id), '1');
            else if($plan->is_complete) $table->complete(array($plan->id), '0');
        }
        else{
            return false;
        }
    }

    public function calculateGoal($data = array(), $start = 0, $finish = 0){
        $response = new stdClass();
        $response->summary = $start;
        $response->dinamic = array();
        $response->dinamic[] = array($start,'');
        $response->percents = 0;

        foreach($data as $in){
            if($in->result_mode == 0){
                $response->summary = $in->value;
            }else{
                $response->summary += $in->value;
            }
            $response->dinamic[] = array(($response->summary + $in->value), $in->date);
        }

        if($start == $finish || $response->summary == $finish) $response->percents = 100;
        else $response->percents = round(100 * (($response->summary - $start) / ($finish - $start)));

        //$response->dinamic = array($finish,'');
        return $response;
    }

    public function calculatePlan($stages = array(), $tasks = array()){
        $response = new stdClass();
        $response->summary = 0;
        foreach($stages as $stage){
            if($stage->status) $response->summary++;
        }
        $response->dinamic = array();
        $response->percents = $response->summary/count($stages) * 100;

        $response->summary = 0;
        if($tasks) foreach($tasks as $in){
            if($in->result_mode == 0){
                $response->summary = $in->value;
            }else{
                $response->summary += $in->value;
            }
            $response->dinamic[] = array(($response->summary + $in->value), $in->date);
        }

        //$response->dinamic = array($finish,'');
        return $response;
    }

    public static function updateStage($id, $stage = null, $initiator = "plan"){
        if(!$stage){
            $initiator = "self";
            $stage = new stdClass();
            $stage = self::getStage($id, true);
        }
        if($stage){
            JLoader::register('GoalsTableStage', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . 'stage.php');
            $status = 1;
            if($stage->tasks) foreach($stage->tasks as $task){
                if($task->status != 1){
                    $status = 0;
                }
            }
            if($status!=$stage->status) {
                $table = new GoalsTableStage(JFactory::getDbo());
                $table->complete(array($stage->id), $status);
            }
            if($initiator!="plan") self::updatePlan($stage->pid);
        }
        else{
            return false;
        }
    }

    public static function getStage($id = 0, $recursive = false)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('s.*');
        $query->from('`#__goals_stages` AS `s`');
        if(id) $query->where('`s`.`id` = ' . $id);
        //$query->order('`m`.`duedate` DESC');
        $db->setQuery($query);
        $stage = $db->loadObject();
        if($recursive){
            $stage->tasks = self::getStageTasks($stage->id);
        }
        return $stage;
    }

    public static function getStageTasks($sid = 0, $done = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__goals_plantasks');
        $query->where('`sid` = '.$db->quote($sid));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function prepareFoldersStructure($path, $permissions = '0777'){
        jimport( 'joomla.filesystem.folder' );
        jimport('joomla.filesystem.file');
        $destination= explode(DIRECTORY_SEPARATOR , $path);
        $creatingFolder = JPATH_SITE;

        foreach($destination as $folder){
            if($folder){
                $creatingFolder.= DIRECTORY_SEPARATOR . $folder;
                if (!JFolder::create($creatingFolder, $permissions)){
                    return false;
                }
            }
        }

        return true;
    }

    public function processFile($folder = array('shared'), $selector = 'file_upload', $must_be = array()){

        $response = new stdClass();
        $response->filename = false;
        $response->errors = array();

        $input = JFactory::getApplication()->input;
        $file = $input->files->get($selector);

        jimport('joomla.filesystem.file');

        $folder = implode(DIRECTORY_SEPARATOR, $folder);
        $path = 'images'.DIRECTORY_SEPARATOR.$folder;
        if(GoalsHelper::prepareFoldersStructure($path)){
            $filename = JFactory::getUser()->id.'_'.JFile::makeSafe($file['name']).'_'.mktime();
            if(!empty($must_be)){
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if(!in_array($ext,$must_be)){
                    $response->errors[] = 'COM_GOALS_WRONG_FILE_TYPE';
                    return $response;
                }
            }
            $src = $file['tmp_name'];
            $path .= DIRECTORY_SEPARATOR.$filename;

            if(!JFile::upload($src, $path)){
                $response->errors[] = 'COM_GOALS_CANT_UPLOAD_FILE';
                return $response;
            }

            $response->filename = $filename;

        }else{
            $response->errors[] = 'COM_GOALS_CANT_MAKE_FOLDER_STRUCTURE';
        }

        return $response;
    }

    public function uploadFile($selector, $folder= 'shared')
    {
        $folder = array('com_goals', $folder, 'original');
        $file = GoalsHelper::processFile($folder,$selector,array('gif','jpeg','jpg','png'));
        if($file->filename){

            return true;
            /*
            ?><script type="text/javascript">
                var s = window.parent.document.getElementById('gl_current_image');
                if (s)
                {
                    s.set('src',"<?php echo JURI::root()."images".DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,$folder).DIRECTORY_SEPARATOR.$file->filename;?>");
                }
                var v = window.parent.document.getElementById('jform_image');
                if (v)
                {
                    v.set('value',"<?php echo "/images".DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,$folder).DIRECTORY_SEPARATOR.$file->filename;?>");
                }
                alert("Upload successfully");
                window.parent.SqueezeBox.close();
            </script><?php
            */

        }else{
            $string = '';
            foreach($file->errors as $error){
                ?><script type="text/javascript">alert("<?php echo JText::_($error); ?>")</script><?php
            }
            return false;
        }
    }

	public static function getManifest()
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT `manifest_cache` FROM #__extensions WHERE element="com_goals"');
		$params = json_decode($db->loadResult());
		return $params;
	}
	
	public static function getSettings()
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT `params` FROM #__extensions WHERE element="com_goals"');
		$params = json_decode($db->loadResult());
		return $params;
	}
	
	public function getCustomCatFields($cid=0)
	{
		//if (!$cid) return null;
		$db		= JFactory::getDBO();
		
		$groups = $selected = array();
		
		$query	= $db->getQuery(true);
			$query->select('*');
			$query->from('`#__goals_custom_groups`');
			$query->order('`title` ASC');
			$db->setQuery($query);
		$customgr = $db->loadObjectList();
		
		if (sizeof($customgr))
		{
			foreach ( $customgr as $gr ) 
			{
				$groups[$gr->id] = $gr;
			}
		}
		
		$ungroupped = new stdclass();
		$ungroupped->id = -1;
		$ungroupped->title = JText::_('COM_GOAL_CATEGORY_UNGROUP');
		$groups[-1] = $ungroupped;
		
		$query	= $db->getQuery(true);
			$query->select('f.*');
			$query->from('`#__goals_custom_fields` AS `f`');
			$query->order('f.`title` ASC');
			$query->where('f.`user`=0');
			$db->setQuery($query);
		$fields = $db->loadObjectList();
		
		try{
            $query	= $db->getQuery(true);
                $query->select('`fid`');
                $query->from('`#__goals_categories_xref`');
                $query->where('`cid`='.$cid);
                $db->setQuery($query);
            $selected = $db->loadColumn();
        }
        catch(Exception $e){}

		if (sizeof($fields))
		{
			foreach ( $fields as $field ) 
			{
				$query	= $db->getQuery(true);
						$query->select('g.`gid`');
						$query->from('`#__goals_custom_groups_xref` AS `g`');
						$query->where('`g`.`fid`='.$field->id);
						$db->setQuery($query);
				$gids = $db->loadColumn();		
				
				$field->selected=false;
				if (sizeof($selected))if (in_array($field->id,$selected)) {$field->selected=true;}
				
				if (sizeof($gids))
				{
					foreach ( $gids as $gid ) 
					{
						if (isset($gid) && isset($groups[$gid]))	
						{
							$groups[$gid]->fields[] = $field;
						}else $groups[-1]->fields[] = $field;
					}
				} else $groups[-1]->fields[] = $field;
				
			}
		}
	return $groups;		
	}
	
	public function getCustomTaskFields($cid=0, $tid=0)
	{

		//if (!$cid) return null;
		$db		= JFactory::getDBO();
		$cfields = array();		
		$query	= $db->getQuery(true);
			$query->select('f.*');
			$query->from('`#__goals_custom_fields` AS `f`');
			//$query->select('`x`.`values` AS `inp_values`');
			$query->leftJoin('`#__goals_xref` AS `x` ON `x`.`fid`=`f`.`id`');
			$query->order('f.`title` ASC');
			$db->setQuery($query);
			
		$fields = $db->loadObjectList();

        try{
            $query	= $db->getQuery(true);
                $query->select('`fid`');
                $query->from('`#__goals_categories_xref`');
                $query->where('`cid`='.$cid);
                $db->setQuery($query);
            $selected = $db->loadColumn();
        }
        catch(Exception $e){}
		if (sizeof($fields))
		{
			foreach ( $fields as $field ) 
			{
				if (sizeof($selected))
					if (in_array($field->id,$selected)) 
							{
								$field->inp_values=null;
								if ($tid)
								{
									$query = $db->getQuery(true);
									$query->select('`values`');
									$query->from('#__goals_tasks_xref');
									$query->where('`fid`='.(int)$field->id);
									$query->where('`tid`='.(int)$tid);
									$db->setQuery($query);
									$field->inp_values = $db->loadResult();									
								}								
								$cfields[] = $field;
								
							}				
			}
		}
	return $cfields;		
	}

    public function getCustomTaskFieldsPlans($cid=0, $tid=0)
    {

        //if (!$cid) return null;
        $db		= JFactory::getDBO();
        $cfields = array();
        $query	= $db->getQuery(true);
        $query->select('f.*');
        $query->from('`#__goals_custom_fields` AS `f`');
        //$query->select('`x`.`values` AS `inp_values`');
        $query->leftJoin('`#__goals_xref` AS `x` ON `x`.`fid`=`f`.`id`');
        $query->order('f.`title` ASC');
        $db->setQuery($query);

        $fields = $db->loadObjectList();

        try{
            $query	= $db->getQuery(true);
            $query->select('`fid`');
            $query->from('`#__goals_categories_xref`');
            $query->where('`cid`='.$cid);
            $db->setQuery($query);
            $selected = $db->loadColumn();
        }
        catch(Exception $e){}
        if (sizeof($fields))
        {
            foreach ( $fields as $field )
            {
                if (sizeof($selected))
                    if (in_array($field->id,$selected))
                    {
                        $field->inp_values=null;
                        if ($tid)
                        {
                            $query = $db->getQuery(true);
                            $query->select('`values`');
                            $query->from('#__goals_plantasks_xref');
                            $query->where('`fid`='.(int)$field->id);
                            $query->where('`tid`='.(int)$tid);
                            $db->setQuery($query);
                            $field->inp_values = $db->loadResult();
                        }
                        $cfields[] = $field;

                    }
            }
        }
        return $cfields;
    }
	public function getCustomTaskUserFields($id=0, $tid=0)
	{
		if (!$id) return null;
		$db		= JFactory::getDBO();
		$cfields = array();		
						
		$query	= $db->getQuery(true);
			$query->select('`cf`.*,`x`.fid');
			//$query->select('`x`.`values` AS `inp_values`');
			$query->from('`#__goals_xref` AS `x`');
			$query->join('LEFT','`#__goals_custom_fields` AS `cf` ON `cf`.`id`=`x`.`fid`');
			$query->where('`gid`='.$id);
			$db->setQuery($query);
		$cfields = $db->loadObjectList();
		if (sizeof($cfields))
		{
			foreach ( $cfields as $field ) 
			{
				$field->inp_values=null;
				if ($tid)
				{
					$query = $db->getQuery(true);
					$query->select('`values`');
					$query->from('#__goals_tasks_xref');
					$query->where('`fid`='.(int)$field->id);
					$query->where('`tid`='.(int)$tid);
					$db->setQuery($query);
					$field->inp_values = $db->loadResult();									
				}
			}
		}	
	return $cfields;		
	}



    public function getCustomTaskUserFieldsPlans($id=0, $tid=0)
    {
        if (!$id) return null;
        $db		= JFactory::getDBO();
        $cfields = array();

        $query	= $db->getQuery(true);
        $query->select('`cf`.*,`x`.fid');
        //$query->select('`x`.`values` AS `inp_values`');
        $query->from('`#__goals_plans_xref` AS `x`');
        $query->join('LEFT','`#__goals_plan_custom_fields` AS `cf` ON `cf`.`id`=`x`.`fid`');
        $query->where('`pid`='.$id);
        $db->setQuery($query);
        $cfields = $db->loadObjectList();
        if (sizeof($cfields))
        {
            foreach ( $cfields as $field )
            {
                $field->inp_values=null;
                if ($tid)
                {
                    $query = $db->getQuery(true);
                    $query->select('`values`');
                    $query->from('#__goals_plantasks_xref');
                    $query->where('`fid`='.(int)$field->id);
                    $query->where('`tid`='.(int)$tid);
                    $db->setQuery($query);
                    $field->inp_values = $db->loadResult();
                }
            }
        }

        return $cfields;
    }

	public function dayToStr($day, $abbr = false)
	{
		switch ($day) {
			case 0: return $abbr ? JText::_('SUN') : JText::_('SUNDAY');
			case 1: return $abbr ? JText::_('MON') : JText::_('MONDAY');
			case 2: return $abbr ? JText::_('TUE') : JText::_('TUESDAY');
			case 3: return $abbr ? JText::_('WED') : JText::_('WEDNESDAY');
			case 4: return $abbr ? JText::_('THU') : JText::_('THURSDAY');
			case 5: return $abbr ? JText::_('FRI') : JText::_('FRIDAY');
			case 6: return $abbr ? JText::_('SAT') : JText::_('SATURDAY');
		}
	}
	
	function showCustoGroupFields($custom_cat_fields=null)
	{
	?>
	<fieldset class="adminform" > <legend><?php echo JText::_('COM_GOAL_CATEGORY_FIELDS'); ?></legend>
					<?php
					// echo JHtml::_('tabs.panel',JText::_('COM_GOAL_CATEGORY_FIELDS'), 'gl-category-fields');
					 	$cfs = $custom_cat_fields;
					 	 if (sizeof($cfs))
					 	 {
					 	 	foreach ( $cfs as $gf ) 
					 	 	{
					 	 		if (isset($gf->fields) && isset($gf->title))
					 	 		{
					 	 			if (sizeof($gf->fields))
					 	 			{
					 	 				?>
					 	 					<fieldset class="adminform gls-cfs-fdst" >
					 	 						<legend><?php echo $gf->title;?></legend>
					 	 						<ul class="gls-cfs-list">
													<?php
														foreach($gf->fields as $field) 
														{
													?>  	<li>
																<input id="cf_<?php echo $gf->id.'_'.$field->id;?>" type="checkbox" class="checkbox" name="cf[]" value="<?php echo $field->id; ?>" <?php if ($field->selected) echo 'checked="checked"'; ?> />
																<label for="cf_<?php echo $gf->id.'_'.$field->id;?>">
																	<?php echo $field->title;?>
																</label>
															</li>
													<?php } ?>
												</ul>
					 	 					</fieldset>
					 	 				<?php					 	 				
					 	 			}
					 	 		}
					 	 	}
					 	 }
					?>
					<div class="clr"></div>
	<?php
	}
	
	function showUserFieldsValues($custom_fields=null, $negative = false)
	{
		ob_start();
	?>
	<fieldset class="adminform" > <legend><?php echo JText::_('COM_GOAL_USER_CASTOM_FIELDS'); ?></legend>
					<?php
					 	 	$emchecks = $urlchecks = array();
					 	 	if (sizeof($custom_fields))
					 	 			{
					 	 				?>
					 	 						<ul class="gls-cfs-list">
													<?php
														foreach($custom_fields as $field) 
														{
															$type = $field->type;
															$params = json_decode($field->values);
															if ($field->type!='pc') $ivals  = json_decode($field->inp_values); else $ivals  = $field->inp_values;
															$id = $field->id;
													?>  	<li>
																	<label for="cf_<?php echo $id;?>">
																		<?php echo $field->title;?>
																	</label>
																<?php															
																 $default_text = '';
																 switch ( $type ) 
																	 {
																		case 'tf':
																			$tf_max = 70;
																			if ($ivals) $default_text = $ivals;
																			else if (isset($params->tf_default)) $default_text = $params->tf_default;
																			if (isset($params->tf_max)) $tf_max = $params->tf_max;
																		?>
																			<input type="text" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>" value="<?php echo $default_text;?>" maxlength="<?php echo $tf_max;?>" />
																			<div class="gl_descfield"><?php echo JText::_('COM_GOALS_FIELD_TEXT'); ?></div>
																		<?php															
																		break;																		
																		case 'ta':
																			$ta_rows=30;
																			$ta_colls=10;	
																			$text = '';
																			if (isset($params->ta_rows))  $ta_rows = $params->ta_rows;
																			if (isset($params->ta_colls)) $ta_colls = $params->ta_colls;
																			if ($ivals) $text = $ivals;																			
																		?>
																			<textarea id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>" rows="<?php echo $ta_rows;?>" cols="<?php echo $ta_colls;?>" wrap="off">
																			<?php echo $text;?>
																			</textarea>
																			<div class="gl_descfield"><?php echo JText::_('COM_GOALS_FIELD_TEXTAREA'); ?></div>
																		<?php					
																		break;
																		
																		case 'hc':
																		$text='';
																		if ($ivals) $text = $ivals;
																		$editor = JFactory::getEditor('codemirror');
																		$params = array();
																		echo $editor->display( 'f_'.$id, $text , '250', '250', '60', '20', false, $params ) ;
																		echo '<div class="gl_descfield">'.JText::_('COM_GOALS_FIELD_HTML').'</div> ';         	
																		break;
																		
																		case 'em':
																		if ($ivals) $default_text = $ivals;	
																		$emchecks[]='cf_'.$id;
																		?>
																			<input id="cf_<?php echo $id;?>" type="text" class="text" name="f_<?php echo $id;?>" value="<?php echo $default_text;?>" maxlength="70" />
																			<div class="gl_descfield"><?php echo JText::_('COM_GOALS_FIELD_EMAIL'); ?></div>
																		<?php
																		break;
																		
																		case 'wu':
																		if ($ivals) $default_text = $ivals;	
																		$urlchecks[]="cf_".$id;
																		?>
																			<input type="text" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>" value="<?php echo $default_text; ?>" maxlength="" />
																			<div class="gl_descfield"><?php echo JText::_('COM_GOALS_FIELD_WEBURL'); ?></div>
																		<?php															
																		break;
																		case 'pc':	
																		if ($ivals) 
																		{
																			$ivals=str_replace('"','',$ivals);
																																						
																			if (file_exists(JPATH_SITE.$ivals) && is_file(JPATH_SITE.$ivals)) 
																			{
																				$ivals=str_replace(DS,'/',$ivals);
																				$default_text = '<img src="'.JURI::root().$ivals.'" alt="" />';
																			}
																		}		
																		echo $default_text;
																		?>
																			<input type="file" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>" />
																			<div class="gl_descfield"><?php echo JText::_('COM_GOALS_FIELD_PIC'); ?></div>
																		<?php															
																		break;
																		case 'in':	
																		if ($ivals) $default_text = $ivals;	
																		?>
																			<?php if($negative=0){ ?>
																			<small class="pre-negative-int">
																			<?php echo JText::_('COM_GOALS_NEGATIVE_INPUT_NOTE'); ?>
																			</small>
																			<?php } ?>
																			<input type="text" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>" value="<?php echo $default_text; ?>" maxlength="" />
																			<div class="gl_descfield"><?php echo JText::_('COM_GOALS_FIELD_INTEGER'); ?></div>
																		<?php															
																		break;
																		case 'sl':
																		$list = array();
																		$selected = NULL;
																		if (isset($ivals)) $selected = $ivals;	
																			if (isset($params->sl_elmts))  
																			{
																				if (sizeof($params->sl_elmts))
																				{
																					foreach ( $params->sl_elmts as $el ) 
																					{
																						$list[] = JHTML::_('select.option',$el,$el);																		
																					}
																				}
																			}
																		echo JHTML::_('select.genericlist',  $list, $name = 'f_'.$id, null, 'value', 'text', $selected, false, false );
																		echo '<div class="gl_descfield">'.JText::_('COM_GOALS_FIELD_SELECTLIST').'</div> ';         	
																																		
																		break;
																		case 'ml':	
																		$list = array();
																		$selected = NULL;
																		if (isset($ivals)) $selected = $ivals;	
																			if (isset($params->ms_elmts))  
																			{
																				if (sizeof($params->ms_elmts))
																				{
																					foreach ( $params->ms_elmts as $el ) 
																					{
																						$list[] = JHTML::_('select.option',$el,$el);																		
																					}
																				}
																			}
																		echo JHTML::_('select.genericlist',  $list, $name = 'f_'.$id.'[]', ' multiple="multiple" size="3"', 'value', 'text', $selected, false, false );
																		echo '<div class="gl_descfield">'.JText::_('COM_GOALS_FIELD_MULTSELECTLIST').'</div> ';         	
																		
																		break;
																		case 'ch':
																		$selected = array();	
																		if (isset($ivals)) $selected = $ivals;
																		if (!is_array($selected)) $selected=array();
																			if (isset($params->ch_elmts))  
																			{
																				if (sizeof($params->ch_elmts))
																				{
																					foreach ( $params->ch_elmts as $k=>$el ) 
																					{
																						?>
																						<div>
																						<input id="cf_ch_<?php echo $k;?>" type="checkbox" class="text" value="<?php echo $el;?>" <?php if (in_array($el,$selected)) echo 'checked="checked"';?> name="f_<?php echo $id;?>[]" />
																						<label for="cf_ch_<?php echo $k;?>"><?php echo $el;?></label>
																						</div>
																						<?php
																					}
																				  
																				}
																			}
																			echo '<div class="gl_descfield">'.JText::_('COM_GOALS_FIELD_CHECKBOXES').'</div> ';
																		break;
																		case 'rd':	
																		$selected = null;	
																		if (isset($ivals)) $selected = $ivals;	
																			if (isset($params->rb_elmts))  
																			{
																				if (sizeof($params->rb_elmts))
																				{
																					foreach ( $params->rb_elmts as $k=>$el ) 
																					{
																						?>
																						<div>
																						<input id="cf_rb_<?php echo $k;?>" type="radio" class="text" value="<?php echo $el;?>" name="f_<?php echo $id;?>"  <?php if ($el==$selected) echo 'checked="checked"';?> />
																						<label for="cf_rb_<?php echo $k;?>"><?php echo $el;?></label>
																						</div>
																						<?php
																					}
																					  
																				}
																			}
																			echo '<div class="gl_descfield">'.JText::_('COM_GOALS_FIELD_RADIOBUTONS').'</div> ';
																		break;
																		default:	break;
																	}																
																?>
																<div class="clr"></div>																
															</li>
													<?php } ?>
												</ul>
					 	 					</fieldset>
					 	 				<?php				 	 				
					 	 			
					 	 }
					 	 ?>
					 	 	<script type="text/javascript">
					 	 	function isValidUserURLs()
					 	 	{
					 	 		var RegExp = /^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/;
    							var tmpurl = '';
    							<?php if (sizeof($urlchecks)){			
					 	 		foreach ( $urlchecks as $urlid ) 
    							{
    								
    							?>
    							tmpurl = $('<?php echo $urlid;?>').get('value');
    							if (tmpurl!='')
    							{
    								if(RegExp.test(tmpurl)){}
    								else
    								{ 
    									$('<?php echo $urlid;?>').focus();
    									alert('Invalid url'); 
    									$('<?php echo $urlid;?>').set('class','invalid');
    									return false;
    								}
    							}
    							$('<?php echo $urlid;?>').set('class','text');
    							<?php }} ?>
    							return true;
    						}
    						</script>
							<?php
					 	
					 	 
					 	 	?>
					 	 	<script type="text/javascript">
					 	 	function isValidUserEmails()
					 	 	{
					 	 		var RegExp = /^((([a-z]|[0-9]|!|#|$|%|&|'|\*|\+|\-|\/|=|\?|\^|_|`|\{|\||\}|~)+(\.([a-z]|[0-9]|!|#|$|%|&|'|\*|\+|\-|\/|=|\?|\^|_|`|\{|\||\}|~)+)*)@((((([a-z]|[0-9])([a-z]|[0-9]|\-){0,61}([a-z]|[0-9])\.))*([a-z]|[0-9])([a-z]|[0-9]|\-){0,61}([a-z]|[0-9])\.)[\w]{2,4}|(((([0-9]){1,3}\.){3}([0-9]){1,3}))|(\[((([0-9]){1,3}\.){3}([0-9]){1,3})\])))$/
    							<?php  if (sizeof($emchecks)) 
					 	 		{
    							foreach ( $emchecks as $emlid ) 
    							{
    							?>
    							if ($('<?php echo $emlid;?>').get('value')!='') 
    							{
    							
    								if(RegExp.test($('<?php echo $emlid;?>').get('value'))){}
    								else
    								{ 
    									$('<?php echo $emlid;?>').focus();
    									alert('Invalid Email'); 
    									$('<?php echo $emlid;?>').set('class','invalid');
    									return false;
    								}
    							}
    							$('<?php echo $emlid;?>').set('class','text');
    							<?php }} ?>
    								return true;
    						}
    						</script>
							<?php
					?>
					<div class="clr"></div>
	<?php
	$content = ob_get_contents();
	ob_get_clean();
	if (sizeof($custom_fields)) echo $content;
	return $content;
	}
	
	function showCustoGroupFieldsValues($custom_fields=null, $negative = false)
	{
		if (sizeof($custom_fields))
		{
		?>
		<fieldset class="adminform" > <legend><?php echo JText::_('COM_GOAL_CASTOM_FIELDS'); ?></legend>
					<?php
					// echo JHtml::_('tabs.panel',JText::_('COM_GOAL_CATEGORY_FIELDS'), 'gl-category-fields');
					 	$emchecks = $urlchecks = array();
					 	$cfs = $custom_fields;
					 	 	if (sizeof($cfs))
					 	 			{
					 	 				?>	
					 	 						<ul class="gls-cfs-list">
													<?php
														foreach($cfs as $field) 
														{
															$type = $field->type;
															$params = json_decode($field->values);
															if ($field->type!='pc') $ivals  = json_decode($field->inp_values); else $ivals  = $field->inp_values;
															$id = $field->id;
													?>  	<li>
																	<label for="cf_<?php echo $id;?>">
																		<?php echo $field->title;?>
																	</label>
																<?php															
																 $default_text = '';
																 switch ( $type ) 
																	 {
																		case 'tf':
																			$tf_max = 70;
																			if ($ivals) $default_text = $ivals;
																			else if (isset($params->tf_default)) $default_text = $params->tf_default;
																			if (isset($params->tf_max)) $tf_max = $params->tf_max;
																		?>
																			<input type="text" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>" value="<?php echo $default_text;?>" maxlength="<?php echo $tf_max;?>" />
																			<div class="gl_descfield"><?php echo JText::_('COM_GOALS_FIELD_TEXT'); ?></div>																			
																		<?php															
																		break;																		
																		case 'ta':
																			$ta_rows=30;
																			$ta_colls=10;	
																			$text = '';
																			if (isset($params->ta_rows))  $ta_rows = $params->ta_rows;
																			if (isset($params->ta_colls)) $ta_colls = $params->ta_colls;
																			if ($ivals) $text = $ivals;																			
																		?>
																			<textarea id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>" rows="<?php echo $ta_rows;?>" cols="<?php echo $ta_colls;?>" wrap="off">
																			<?php echo $text;?>
																			</textarea>
																			<div class="gl_descfield"><?php echo JText::_('COM_GOALS_FIELD_TEXTAREA'); ?></div>
																		<?php					
																		break;
																		
																		case 'hc':
																		$text='';
																		if ($ivals) $text = $ivals;
																		$editor = JFactory::getEditor('codemirror');
																		$params = array();
																		echo $editor->display( 'f_'.$id, $text , '250', '250', '60', '20', false, $params ) ;
																		echo '<div class="gl_descfield">'.JText::_('COM_GOALS_FIELD_HTML').'</div> ';         	          	
																		break;
																		
																		case 'em':
																		if ($ivals) $default_text = $ivals;	
																		
																		$emchecks[]='cf_'.$id;
																		?>
																			<input id="cf_<?php echo $id;?>" type="text" class="text" name="f_<?php echo $id;?>" value="<?php echo $default_text;?>" maxlength="70" />
																				<div class="gl_descfield"><?php echo JText::_('COM_GOALS_FIELD_EMAIL'); ?></div>
																		<?php
																		break;
																		
																		case 'wu':
																		if ($ivals) $default_text = $ivals;	
																		$urlchecks[]="cf_".$id;
																		?>
																			<input type="text" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>" value="<?php echo $default_text; ?>" maxlength="" />
																			<div class="gl_descfield"><?php echo JText::_('COM_GOALS_FIELD_WEBURL'); ?></div>
																		<?php															
																		break;
																		case 'pc':	
																		if ($ivals) 
																		{
																			$ivals=str_replace('"','',$ivals);
																																						
																			if (file_exists(JPATH_SITE.$ivals) && is_file(JPATH_SITE.$ivals)) 
																			{
																				$ivals=str_replace(DS,'/',$ivals);
																				$default_text = '<img src="'.JURI::root().$ivals.'" alt="" />';
																			}
																		}		
																		echo $default_text;
																		?>
																			<input type="file" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>" />
																			<div class="gl_descfield"><?php echo JText::_('COM_GOALS_FIELD_PIC'); ?></div>
																		<?php															
																		break;
																		case 'in':	
																		if ($ivals) $default_text = $ivals;	
																		?>
																			<?php if($negative=0){ ?>
																			<small class="pre-negative-int">
																			<?php echo JText::_('COM_GOALS_NEGATIVE_INPUT_NOTE'); ?>
																			</small>
																			<?php } ?>
																			<input type="text" class="text" id="cf_<?php echo $id;?>" name="f_<?php echo $id;?>" value="<?php echo $default_text; ?>" maxlength="" />
																			<div class="gl_descfield"><?php echo JText::_('COM_GOALS_FIELD_INTEGER'); ?></div>
																		<?php															
																		break;
																		case 'sl':
																		$list = array();
																		$selected = NULL;
																		if (isset($ivals)) $selected = $ivals;	
																			if (isset($params->sl_elmts))  
																			{
																				if (sizeof($params->sl_elmts))
																				{
																					foreach ( $params->sl_elmts as $el ) 
																					{
																						$list[] = JHTML::_('select.option',$el,$el);																		
																					}        	
																				}
																			}
																		echo JHTML::_('select.genericlist',  $list, $name = 'f_'.$id, null, 'value', 'text', $selected, false, false );
																		echo '<div class="gl_descfield">'.JText::_('COM_GOALS_FIELD_SELECTLIST').'</div> '; 																	
																		break;
																		case 'ml':	
																		$list = array();
																		$selected = NULL;
																		if (isset($ivals)) $selected = $ivals;	
																			if (isset($params->ms_elmts))  
																			{
																				if (sizeof($params->ms_elmts))
																				{
																					foreach ( $params->ms_elmts as $el ) 
																					{
																						$list[] = JHTML::_('select.option',$el,$el);																		
																					}																					         	
																				}
																			}
																		echo JHTML::_('select.genericlist',  $list, $name = 'f_'.$id.'[]', ' multiple="multiple" size="3" ', 'value', 'text', $selected, false, false );
																		echo '<div class="gl_descfield">'.JText::_('COM_GOALS_FIELD_MULTSELECTLIST').'</div> ';
																		break;
																		case 'ch':
																		$selected = array();	
																		if (isset($ivals)) $selected = $ivals;
																		if (!is_array($selected)) $selected=array();
																			if (isset($params->ch_elmts))  
																			{
																				if (sizeof($params->ch_elmts))
																				{
																					foreach ( $params->ch_elmts as $k=>$el ) 
																					{
																						?>
																						<div>
																						<input id="cf_ch_<?php echo $k;?>" type="checkbox" class="text" value="<?php echo $el;?>" <?php if (in_array($el,$selected)) echo 'checked="checked"';?> name="f_<?php echo $id;?>[]" />
																						<label for="cf_ch_<?php echo $k;?>"><?php echo $el;?></label>
																						</div>
																						<?php
																					}
																				}
																			}
																			echo '<div class="gl_descfield">'.JText::_('COM_GOALS_FIELD_CHECKBOXES').'</div> ';
																		break;
																		case 'rd':	
																		$selected = null;	
																		if (isset($ivals)) $selected = $ivals;	
																			if (isset($params->rb_elmts))  
																			{
																				if (sizeof($params->rb_elmts))
																				{
																					foreach ( $params->rb_elmts as $k=>$el ) 
																					{
																						?>
																						<div>
																						<input id="cf_rb_<?php echo $k;?>" type="radio" class="text" value="<?php echo $el;?>" name="f_<?php echo $id;?>"  <?php if ($el==$selected) echo 'checked="checked"';?> />
																						<label for="cf_rb_<?php echo $k;?>"><?php echo $el;?></label>
																						</div>
																						<?php
																					}
																				}
																			}
																			echo '<div class="gl_descfield">'.JText::_('COM_GOALS_FIELD_RADIOBUTONS').'</div> ';
																		break;
																		default:	break;
																	}																
																?>
																<div class="clr"></div>																
															</li>
													<?php } ?>
												</ul>
					 	 					</fieldset>	
					<div class="clr"></div>
					 	<?php
					 	 }
					?>

					<script type="text/javascript">
					 	 	function isValidURLs()
					 	 	{
					 	 		var RegExp = /^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/;
    							var tmpurl = '';
    							<?php if (sizeof($urlchecks)){			
					 	 		foreach ( $urlchecks as $urlid ) 
    							{
    								
    							?>
    							tmpurl = $('<?php echo $urlid;?>').get('value');
    							if (tmpurl!='')
    							{
    								if(RegExp.test(tmpurl)){}
    								else
    								{ 
    									$('<?php echo $urlid;?>').focus();
    									alert('Invalid url'); 
    									$('<?php echo $urlid;?>').set('class','invalid');
    									return false;
    								}
    							}
    							$('<?php echo $urlid;?>').set('class','text');
    							<?php }} ?>
    								return true;
    						}
    					</script>
					 	<script type="text/javascript">
					 	 	function isValidEmails()
					 	 		{
					 	 		var RegExp = /^((([a-z]|[0-9]|!|#|$|%|&|'|\*|\+|\-|\/|=|\?|\^|_|`|\{|\||\}|~)+(\.([a-z]|[0-9]|!|#|$|%|&|'|\*|\+|\-|\/|=|\?|\^|_|`|\{|\||\}|~)+)*)@((((([a-z]|[0-9])([a-z]|[0-9]|\-){0,61}([a-z]|[0-9])\.))*([a-z]|[0-9])([a-z]|[0-9]|\-){0,61}([a-z]|[0-9])\.)[\w]{2,4}|(((([0-9]){1,3}\.){3}([0-9]){1,3}))|(\[((([0-9]){1,3}\.){3}([0-9]){1,3})\])))$/
    							<?php  if (sizeof($emchecks)) 
					 	 		{
    							foreach ( $emchecks as $emlid ) 
    							{
    							?>
    							if ($('<?php echo $emlid;?>').get('value')!='') 
    							{
    							
    								if(RegExp.test($('<?php echo $emlid;?>').get('value'))){}
    								else
    								{ 
    									$('<?php echo $emlid;?>').focus();
    									alert('Invalid Email'); 
    									$('<?php echo $emlid;?>').set('class','invalid');
    									return false;
    								}
    							}
    							$('<?php echo $emlid;?>').set('class','text');
    							<?php }} ?>
    								return true;
    						}
    						</script>
	<?php
		}
	}
}
