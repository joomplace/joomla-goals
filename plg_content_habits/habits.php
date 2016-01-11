<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgContentHabits extends JPlugin
{
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		$app = JFactory::getApplication();
		
		$artext = $returntext = $tag = $replace = '';
		$count = 0;
		
		$regex		= '/{habits,(.*?)\s*,(.*?)\s*,(\d+)}/i';
				
		if (!isset($article->text)) return true;
		
		$artext = $article->text;
		
		if ( strpos( $artext, '{habits' ) === false ) {
			return true;
		}

		if ($artext) 
		{		
			preg_match_all( $regex, $artext, $matches);
			if (count($matches[0]))
			{
				for($i=0;$i<count($matches[0]);$i++) 
				{	
					$replace='';
					if(isset($matches[0][$i])) 
					{
						$strt = htmlspecialchars($matches[1][$i]);
						$fin  = htmlspecialchars($matches[2][$i]);
						$uid  = htmlspecialchars($matches[3][$i]);
						 $replace = '<img src="'.JRoute::_('index.php?option=com_goals&amp;task=habit.showallGraph&amp;tmpl=component&amp;filter=raw&amp;stc='.$strt.'&amp;u='.(int)$uid.'&amp;ecl='.$fin).'" />';
					}			
					$article->text = str_replace($matches[0][$i],$replace,$article->text);
				}
			}
		}	
		return;
	}
}
?>