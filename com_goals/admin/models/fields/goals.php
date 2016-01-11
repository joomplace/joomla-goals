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

class JFormFieldgoals extends JFormFieldList
{
	public $type = 'goals';

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
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();
		$selected = (array)$this->getSelected();
		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $selected, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $selected, $this->id);
		}

		return implode($html);
	}
	
	protected function getSelected()
	{
		$id = JRequest::getInt('id');
		$selected = array();
		if ($id)
		{
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select('gid');
			$query->from('#__goals_xref');
			$query->where('fid='.(int)$id);
			$db->setQuery($query);
			
			$selected = $db->loadColumn();
		}
		return $selected;
	}
	
	/**
	 * Method to get the field options.
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options =  $addmass	= array();
		$user = JFactory::getUser();
		$uid = $user->id;
		
		$defsel = new stdclass();
			$defsel->text  = JText::_('COM_GOALS_SELECT_GOAL');
			$defsel->value = null;
		$addmass[]= $defsel;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('`id` AS `value`, (`title`) AS `text`');
			$query->from('#__goals');
			$query->order('`title` ASC');
			$query->where('`uid`='.$uid);
			$db->setQuery($query);
		$options = $db->loadObjectList();
		if (sizeof($options)) {$options = array_merge($addmass,$options);} else {$options=$addmass;}
		return $options;
	}
}