<?php
/**
* Goals EasySocial widget for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Import the model file from the core
Foundry::import( 'admin:/includes/model' );
require_once(JPATH_BASE . '/components/com_goals/models/dashboard.php');
//JLoader::import('GoalsModelGoals');
//Foundry::import( '/components/com_goals/models/goals' );


class DashboardModel extends EasySocialModel
{
    /**
     * Retrieves the textbooks stored from the database.
     *
     * @since	1.0
     * @access	public
     * @param	int		User's id.
     * @return	Array	A list of goals rowset.
     */
    public function getItems( $userId )
    {
        global $viewed_user;
        global $viewer_user;
        $viewed_user = $viewer_user = JFactory::getUser();
        // initial components model object
        $imodel = new GoalsModelDashboard();
        $imodel->setQueryUser($userId);

        $result = new stdClass();

        $result->state = $imodel->getState();
        $result->goals_array = $imodel->getItems();

        $result->plans_array = $imodel->getPlans();

        //$result->milistones = $imodel->getMilistones();
        $result->records = $imodel->getRecords();
        $result->habits = $imodel->getHabits();

        return $result;
    }

}