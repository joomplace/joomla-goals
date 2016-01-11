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
JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of categories
 *
 */
class JFormFieldcustomtypes extends JFormFieldList
{
	public $type = 'customtypes';

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		
		// Initialize JavaScript field attributes.
		$attr .=  ' onchange="javascript:change_custom_type(this.value);"';

		// Get the field options.
		$options = (array) $this->getOptions();
		
		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		return implode($html);
	}
	
		
	/**
	 * Method to get the field options.
	 */
	protected function getOptions()
	{
		$options = array();
			$options[] = JHTML::_('select.option','', '- Select -'); 
			$options[] = JHTML::_('select.option','tf', JText::_('COM_GOALS_FIELD_TEXT')); 
			$options[] = JHTML::_('select.option','ta', JText::_('COM_GOALS_FIELD_TEXTAREA')); 
			$options[] = JHTML::_('select.option','hc', JText::_('COM_GOALS_FIELD_HTML')); 
			$options[] = JHTML::_('select.option','em', JText::_('COM_GOALS_FIELD_EMAIL')); 
			$options[] = JHTML::_('select.option','wu', JText::_('COM_GOALS_FIELD_WEBURL')); 
			$options[] = JHTML::_('select.option','pc', JText::_('COM_GOALS_FIELD_PIC')); 
			$options[] = JHTML::_('select.option','in', JText::_('COM_GOALS_FIELD_INTEGER')); 
			$options[] = JHTML::_('select.option','sl', JText::_('COM_GOALS_FIELD_SELECTLIST')); 
			$options[] = JHTML::_('select.option','ml', JText::_('COM_GOALS_FIELD_MULTSELECTLIST')); 
			$options[] = JHTML::_('select.option','ch', JText::_('COM_GOALS_FIELD_CHECKBOXES')); 
			$options[] = JHTML::_('select.option','rd', JText::_('COM_GOALS_FIELD_RADIOBUTONS')); 
		return $options;
	}
}