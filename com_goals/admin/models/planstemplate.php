<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class GoalsModelPlansTemplate extends JModelAdmin
{
	protected $context = 'com_goals';

	public function getTable($type = 'planstemplate', $prefix = 'GoalsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) 
	{
		$form = $this->loadForm('com_goals.planstemplate', 'planstemplate', array('control' => 'jform', 'load_data' => false));
		if (empty($form)) {
			return false;
		}
		$item = $this->getItem();
		$form->bind($item);

		return $form;
	}


    //protected function uploadFile($id=0)
    protected function uploadFile()
    {
        GoalsHelper::uploadFile('userfile','plans');
    }
	
	public function upload()
	{
		$id = JRequest::getInt('id');
		
		if (isset($_FILES['userfile']))
		{
			$this->uploadFile($id);
		}
		
		?>
		<form method="post" action="<?php echo JRoute::_('index.php?option=com_goals&task=planstemplate.upload&id='.(int) $id); ?>" enctype="multipart/form-data" name="filename">
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
					Max size = <?php echo ini_get( 'upload_max_filesize' );?>
				</td>
			</tr>
		</table>
                <input type="hidden" name="option" value="com_goals" />
				<input type="hidden" name="task" value="planstemplate.upload">
				<input type="hidden" name="tmpl" value="component">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
		</form>
		<?php
	}
}
