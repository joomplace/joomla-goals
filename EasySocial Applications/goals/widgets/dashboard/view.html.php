<?php
/**
* Goals EasySocial widget for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );


class GoalsmanagerWidgetsProfile extends SocialAppsWidgets
{
    /**
     * This will display a simple "Hello World" message on the person's profile sidebar.
     *
     * @param   SocialUser  The user object.
     * @return  null
     */
    public function sidebarTop( SocialUser &$user )
    {
        echo "Hello World";
    }

}