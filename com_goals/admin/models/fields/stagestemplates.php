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

class JFormFieldStagesTemplates extends JFormFieldList
{
	public $type = 'stagestemplates';

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
		//if (!$this->element['noscript'])$attr .=  ' onchange="javascript:change_plan(this.value);"';

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
		$options =  $addmass	= array();
		$user = JFactory::getUser();
        $app  = JFactory::getApplication();
		$uid  = $user->id;
		
		$defsel = new stdclass();
			$defsel->text  = JText::_('COM_GOALS_SELECT_STAGE');
			$defsel->value = null;
		$addmass[]= $defsel;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
        if ($app->isSite()) {
            $query->select("`g`.`id` AS `value`, `g`.`title` AS `text`");
            $query->from("`#__goals_planstemplates` AS  `g`");
            $query->where("`g`.`uid` =  ".(int)$uid);
            $query->order('`title` ASC');
        } else {
            //$query->select("`g`.`id` AS `value`, `g`.`title` AS `text`");
            //$query->from("`#__goals_stagestemplates` AS  `g`");
            //$query->order('`title` ASC');

            $query->select("`s`.`id` AS `value`, CONCAT(`s`.`title`, ' (plan: ', `p`.`title`, ')') AS `text`");
            $query->from("`#__goals_stagestemplates` AS  `s`");
            $query->join('left','#__goals_planstemplates as `p` ON s.pid=p.id');
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