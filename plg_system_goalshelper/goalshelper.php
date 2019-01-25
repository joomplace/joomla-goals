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
	public function onAfterRoute()
	{
        $app = JFactory::getApplication();

	    if( $app->input->get('option', '', 'get') != 'com_goals' ){
            return true;
        }

	    if($app->isClient('administrator')) {
			include_once JPATH_ROOT.'/administrator/components/com_goals/helpers/goals.php';
		}
		else if($app->isClient('site')) {
		    if( $app->input->get('task', '') == 'record.save'
                    || $app->input->get('task', '') == 'plantask.complete'
            ) {
                include_once JPATH_ROOT.'/administrator/components/com_goals/helpers/goals.php';
            } else {
                include_once JPATH_ROOT . '/components/com_goals/helpers/goals.php';
            }
		}
	}
}
