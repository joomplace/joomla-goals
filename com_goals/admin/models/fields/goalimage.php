<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldgoalimage extends JFormField
{
	public $type = 'goalimage';

	protected function getInput()
	{
		$value = $this->value;
		if (!$value) $value = 'components/com_goals/assets/images/noimage.png'; 
		echo '<div class="gl_current_image"><img id="gl_current_image" style="max-width: 400px; max-height: 400px;" src="'.JURI::root().$value.'" /></div>';
		?>
		<input type="hidden" id="jform_image" name="jform[image]" value="<?php echo $value?>" />
		<div>
			<span title="<?php echo JText::_('COM_GOALS_UPLOAD')?>::<small>&nbsp;* Select gif, jpg, png files</small>" class="hasTip">
            	<a class="popup btn" href="<?php echo "index.php?option=com_goals&amp;task=goal.upload&tmpl=component"?>" rel="{handler: 'iframe', size: {x: 300, y: 150}}">
            		<img src="<?php echo JURI::root().'/components/com_goals/assets/images/upload.png'?>" border="0" width="16" height="16" alt="upload" />
            	</a>
            </span>
        </div>
        <?php
	}
}