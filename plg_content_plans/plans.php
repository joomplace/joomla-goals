<?php
/**
* Goals component for Joomla 3.0
* @package Plans
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgContentPlans extends JPlugin
{
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		$app = JFactory::getApplication();
		
		$artext = $returntext = $tag = $replace = '';
		$count = 0;
		
		$regex = '/{plan (\d+)}/i';
				
		if (!isset($article->text)) return true;
		
		$artext = $article->text;
		
		if ( strpos( $artext, '{plan' ) === false ) {
			return true;
		}

		if ($artext) 
		{
			preg_match_all( $regex, $artext, $matches );
			if (count($matches[1]))
			{
				for($i=0;$i<count($matches[1]);$i++) 
				{	
					$replace='';
					if(isset($matches[1][$i])) 
					{
						$pid = (int) $matches[1][$i];
						if ($pid)
						{
							    $db	=& JFactory::getDBO();
								$query	= "SELECT id FROM `#__goals_stages` WHERE `pid`=".$pid;
								$db->setQuery( $query );
								$stages = $db->loadColumn();
                                if ($stages) {
                                    $query = $db->getQuery(true);
                                    $query->select('id')->from('#__goals_plantasks')->where('sid IN ('.implode(',',$stages).')');
                                    $c = $db->loadResult();

                                } else {
                                   $c = null;
                                }

								if ($c) $replace = '<img src="'.JRoute::_('index.php?option=com_goals&task=plan.showGoalGraph&tmpl=component&filter=raw&id='.(int)$pid).'" />';
								else $replace='';
						}
						
					}			
					$article->text = str_replace($matches[0][$i],$replace,$article->text);
				}
			}
		}	
		return;
	}
}
?>