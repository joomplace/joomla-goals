<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class GoalsModelPlan extends JModelList
{
	protected function getListQuery()
	{

		$id = JRequest::getInt('id');
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select required fields from the categories.
		$query->select('p.*');
        $query->select('(SELECT COUNT(`s`.`id`) FROM `#__goals_stages` AS `s` WHERE `s`.`pid`=`p`.`id` LIMIT 1) AS `stages_count`');

		$query->select('`c`.`title` AS `catname`');
		$query->join('LEFT','`#__goals_categories` AS `c` ON `c`.`id`=`p`.`cid`');
        $query->join('LEFT','`#__goals_stages` AS `s` ON `s`.`pid`=`p`.`id`');
		$query->from('`#__goals_plans` AS `p`');
		$query->where('`p`.`id`='.$id);
		$query->order('`p`.`deadline`')->group('p.id');

        return $query;
	}

	protected function uploadFile($id=0)
	{
		$userfile2=(isset($_FILES['userfile']['tmp_name']) ? $_FILES['userfile']['tmp_name'] : "");
		$userfile_name=(isset($_FILES['userfile']['name']) ? $_FILES['userfile']['name'] : "");
		$ext = substr($userfile_name,-4);
		$directory = 'goals';
		if (isset($_FILES['userfile'])) {
			$base_Dir = JPATH_SITE."/images/goals";
			if(!file_exists($base_Dir)) {@mkdir($base_Dir, 0777);}

			if (empty($userfile_name)) {
				?><script type="text/javascript">alert("Please select a file to upload.")</script><?php return;
			}

			$filename = preg_split("/\./", $userfile_name);

			if (preg_match("/[^0-9a-zA-Z_-]/", $filename[0])) {
				?><script type="text/javascript">alert("File must only contain alphanumeric characters and no spaces please.")</script><?php return;
			}

			if (file_exists($base_Dir.'/'.$userfile_name))
				$userfile_name = str_replace($ext,'',$userfile_name).'_'.rand(1,9999).$ext;
			if (file_exists($base_Dir.'/'.$userfile_name)) {
				?><script type="text/javascript">alert("File <?php echo $userfile_name; ?> already exists.")</script><?php return;
			}

			if ((strcasecmp($ext,".gif")) && (strcasecmp($ext,".jpg")) && (strcasecmp($ext,".png")) ) {
				?><script type="text/javascript">alert("The file must be gif, jpg, or png.")</script><?php return;
			}


		if (!move_uploaded_file ($_FILES['userfile']['tmp_name'],$base_Dir.'/'.$userfile_name) || !JPath::setPermissions($base_Dir.'/'.$userfile_name)) {
				?><script type="text/javascript">alert("Upload of <?php echo $userfile_name?> failed")</script><?php return;
		} else {
			 
			?><script type="text/javascript">
				var s = window.parent.document.getElementById('gl_current_image');
				if (s)
					{
						s.set('src',"<?php echo JURI::root()."images/goals/".$userfile_name;?>");
					}
				var v = window.parent.document.getElementById('jform_image');
				if (v)
					{
						v.set('value',"<?php echo "/images/goals/".$userfile_name;?>");
					}
				alert("Upload successfully");
				window.parent.SqueezeBox.close();
			</script><?php
		}
		}
		return;
	}

	public function upload()
	{
		$id = JRequest::getInt('id');

		if (isset($_FILES['userfile']))
		{
			$this->uploadFile($id);
		}

		?>
		<form method="post" action="<?php echo JRoute::_('index.php?option=com_goals&task=plan.upload&id='.(int) $id); ?>" enctype="multipart/form-data" name="filename">
		<table class="adminform">
			<tr>
				<th class="title">
					File Upload :
				</th>
			</tr>
			<tr>
				<td align="center">
						<input class="inputbox" name="userfile" type="file" />
				</td>
			</tr>
			<tr>
				<td>
					<input class="button" type="submit" value="Upload" name="fileupload" />
					<?php //Max size = <?php echo ini_get( 'upload_max_filesize' );
					?>
				</td>
			</tr>
		</table>
                <input type="hidden" name="option" value="com_goals" />
				<input type="hidden" name="task" value="plan.upload">
				<input type="hidden" name="tmpl" value="component">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
		</form>
		<?php
	}
}