<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgButtonPlans extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	function onDisplay($name, $asset, $author)
	{
		$js = "
		function jSelectPlan(id) {
			var tag = '{plan '+id+'}';
			jInsertEditorText(tag, '".$name."');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
		JHTML::_('behavior.modal');
		$link = 'index.php?option=com_goals&amp;view=plans&amp;layout=modal&amp;tmpl=component';
		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_PLANS_BUTTON_TEXT'));
		$button->set('name', 'image');
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");
		return $button; 

	}
}
