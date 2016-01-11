<?php

/**

* Goals component for Joomla 3.0

* @package Goals

* @author JoomPlace Team

* @Copyright Copyright (C) JoomPlace, www.joomplace.com

* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html

*/



defined('_JEXEC') or die('Restricted access');



/**

 * Goals Component Controller

 */

class GoalsController extends JControllerLegacy
{

        function display($cachable = false, $urlparams = array())

        {



        	 //CSS

            $document = JFactory::getDocument();

            $document->addStyleSheet(JURI::root().'administrator/components/com_goals/assets/css/goals.css');

        	$view = JFactory::getApplication()->input->get('view', 'dashboard');

            JFactory::getApplication()->input->set('view', $view);



            GoalsHelper::addSubmenu($view);



               parent::display($cachable = false, $urlparams = false);

        }



        function latestVersion()

        {

        	require_once(JPATH_BASE.'/components/com_goals/helpers/Snoopy.class.php' );

			$tm_version=GoalsHelper::getOriginalVersion();



			$s = new Snoopy();

			$s->read_timeout = 90;

			$s->referer = JURI::root();

			@$s->fetch('http://www.joomplace.com/version_check/componentVersionCheck.php?component=goals&current_version='.urlencode($tm_version));

			$version_info = $s->results;

			$version_info_pos = strpos($version_info, ":");

			if ($version_info_pos === false) {

				$version = $version_info;

				$info = null;

			} else {

				$version = substr( $version_info, 0, $version_info_pos );

				$info = substr( $version_info, $version_info_pos + 1 );

			}

			if($s->error || $s->status != 200){

		    	echo '<font color="red">Connection to update server failed: ERROR: ' . $s->error . ($s->status == -100 ? 'Timeout' : $s->status).'</font>';

		    } else if($version == $tm_version){

                $version_array = explode('.', $version);

                $build = $version_array[3];

                unset($version_array[3]);

                $version = implode('.', $version_array) . ' (build ' . $build . ')';

		    	echo '<font color="green">' . $version . '</font>' . $info;

		    } else {

		    	echo '<font color="red">' . $version . '</font>&nbsp;<a href="http://www.joomplace.com/members-area.html" target="_blank">(Upgrade to the latest version)</a>' ;

		    }

		    exit();

        }



        public function latestNews()

        {

        	if (file_exists(JPATH_BASE.'/components/com_goals/helpers/Snoopy.class.php'))

        		require_once(JPATH_BASE.'/components/com_goals/helpers/Snoopy.class.php');

        	else return null;



			$s = new Snoopy();

			$s->read_timeout = 10;

			$s->referer = JURI::root();

			@$s->fetch('http://www.joomplace.com/news_check/componentNewsCheck.php?component=goals');

			$news_info = $s->results;



			if($s->error || $s->status != 200){

		    	echo '<font color="red">Connection to update server failed: ERROR: ' . $s->error . ($s->status == -100 ? 'Timeout' : $s->status).'</font>';

		    } else {

		    		echo $news_info;

				}

        }



        public function history()

        {

        	echo '<h2>'.JText::_('COM_GOALS_VERSION_HISTORY').'</h2><br/>';

        	jimport ('joomla.filesystem.file');

        	if (!JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.'/changelog.txt'))

        	{

        		echo 'History file not found.';

        	}else

        	echo '<textarea class="editor" rows="30" cols="50" style="width:100%">';

        	echo JFile::read(JPATH_COMPONENT_ADMINISTRATOR.'/changelog.txt');

        	echo '</textarea>';

        	return;

        }

    public function show_changelog()

    {

        @ob_clean;

        header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        header('Cache-Control: no-cache, must-revalidate');

        header('Pragma: no-cache');

        header('Content-Type: text/html; charset=utf-8');



        jimport ("joomla.filesystem.file");



        echo '<h2>' . JText::_("COM_GOALS_ABOUT_VERSION_HISTORY") . '</h2>';



        if (!JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."changelog.txt"))

        {

            echo JText::_("COM_GOALS_ABOUT_NOCHANGEDLOG");

        }

        else

        {

            echo '<pre style="font-size:12px;">';

            echo 	JFile::read(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."changelog.txt");

            echo '</pre>';

        }



        jexit();

    }



}