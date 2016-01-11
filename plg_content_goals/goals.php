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

class plgContentGoals extends JPlugin
{
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		$app = JFactory::getApplication();
		
		$artext = $returntext = $tag = $replace = '';
		$count = 0;
		
		$regex = '/{goal (\d+)}/i';
				
		if (!isset($article->text)) return true;
		
		$artext = $article->text;
		
		if ( strpos( $artext, '{goal ' ) === false ) {
			if ( strpos( $artext, '{goals ' ) === false ) {
				return true;
			}
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
						$gid = (int) $matches[1][$i];
						if ($gid)
						{
							$db	=& JFactory::getDBO();
								$query	= "SELECT COUNT(id) FROM `#__goals_tasks` WHERE `gid`=".$gid;
								$db->setQuery( $query );
								$c = $db->loadResult();
								if ($c) $replace = '<img src="'.JRoute::_('index.php?option=com_goals&task=goal.showGoalGraph&tmpl=component&filter=raw&id='.(int)$gid).'" />';
								else $replace='';
						}
						
					}			
					$article->text = str_replace($matches[0][$i],$replace,$article->text);
				}
			}
		}	
		
		/* users goals */
		$artext = $returntext = $tag = $replace = '';
		$count = 0;
		
		$regex = '/{goals \[(\d+)\]}/i';
				
		if (!isset($article->text)) return true;
		
		$artext = $article->text;

		if ($artext) 
		{		
			$user = JFactory::getUser();
			
			preg_match_all( $regex, $artext, $matches );
			if (count($matches[1]))
			{
				for($i=0;$i<count($matches[1]);$i++) 
				{	
					$replace='';
					if(isset($matches[1][$i])) 
					{
						$user_ids = explode(',',$matches[1][$i]);
					
						if ($user_ids)
						{
							$db	= JFactory::getDBO();
								
							$query = $db->getQuery(true);
								
							if(JFactory::getUser()->authorise('core.manage', 'com_goals')){
								$query->where('`uid` IN ('.implode(',',$user_ids).')');
							}else{
								if(in_array($user->id,$user_ids)){
									$query->where('`uid` = '.$user->id.'');
								}
							}
							$query->select('`id`')
								->from('`#__goals`');
							$db->setQuery($query);
							$gids = $db->loadAssocList();
							foreach($gids as $gid){
								$db	= JFactory::getDBO();
								$query	= "SELECT COUNT(id) FROM `#__goals_tasks` WHERE `gid`=".$gid['id'];
								$db->setQuery( $query );
								$c = $db->loadResult();
								if ($c) $replace .= '<img src="'.JRoute::_('index.php?option=com_goals&task=goal.showGoalGraph&tmpl=component&filter=raw&id='.(int)$gid['id']).'" />';
							}
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