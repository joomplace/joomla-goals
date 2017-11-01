<?php
/**
 * @package     JoomPlace.Plugin
 * @subpackage  System.goalshelper
 *
 * @copyright   Copyright (C) 2005 - 2017 Joomplace. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Joomla! Goalshelper Plugin.
 *
 * @since  3.5
 */
class PlgSystemGoalshelper extends JPlugin
{
	public function onAfterInitialise()
	{
		if(JFactory::getApplication()->isClient('administrator')) {
			include_once JPATH_ROOT.'/administrator/components/com_goals/helpers/goals.php';
		} 
		if(JFactory::getApplication()->isClient('site')) {
            include_once JPATH_ROOT.'/components/com_goals/helpers/goals.php';
		}
	}
}
