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

class JFormFieldPlan extends JFormFieldList
{
	public $type = 'plan';

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
		if (!$this->element['noscript'])$attr .=  ' onchange="javascript:change_plan(this.value);"';

		// Get the field options.
		$options = (array) $this->getOptions();
		
		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {

            if (JFactory::getApplication()->isSite()) {
                $value = JRequest::getInt('pid');

            } else {
                $value = $this->value;
            }


			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $value, $this->id);
		}

		return implode($html);
	}
	
		
	/**
	 * Method to get the field options.
	 */
	protected function getOptions()
	{
		$options =  $addmass	= array();
		$user = JFactory::getUser();
        $app  = JFactory::getApplication();
		$uid  = $user->id;
		
		$defsel = new stdclass();
			$defsel->text  = JText::_('COM_GOALS_SELECT_PLAN');
			$defsel->value = null;
		$addmass[]= $defsel;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
        if ($app->isSite()) {

            $query->select("`g`.`id` AS `value`, `g`.`title` AS `text`");
            $query->from("`#__goals_plans` AS  `g`");
            $query->where("`g`.`uid` =  ".(int)$uid);
            $query->order('`title` ASC');
        } else {
            $query->select("`g`.`id` AS `value`, CONCAT(`g`.`title`, ' (user: ', `u`.`username`, ')') AS `text`");
            $query->from("`#__goals_plans` AS  `g`");
            $query->join("INNER", "`#__users` AS `u` ON `u`.`id` = `g`.`uid`");
            $query->order('`title` ASC');
        }
		$db->setQuery($query);
		$options = $db->loadObjectList();
		if (sizeof($options)) {
			$options = array_merge($addmass,$options);
		} else {
			$options=$addmass;
		}

		return $options;
	}
}