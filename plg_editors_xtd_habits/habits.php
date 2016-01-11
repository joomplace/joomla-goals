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

class plgButtonHabits extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	function onDisplay($name, $asset, $author)
	{
		$js = "
		function jSelectHabit(start,finish,uid) {
			var tag = '{habits,'+start+','+finish+','+uid+'}';
			jInsertEditorText(tag, '".$name."');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		JHTML::_('behavior.modal');

		$link = 'index.php?option=com_goals&amp;view=habits&amp;layout=modal&amp;tmpl=component';

		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_HABITS_BUTTON_TEXT'));
		$button->set('name', 'image');
		$button->set('options', "{handler: 'iframe', size: {x: 300, y: 200}}");
		return $button; 
	}
}
