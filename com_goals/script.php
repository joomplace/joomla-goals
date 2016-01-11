<?php

/**

* Goals component for Joomla 3.0

* @package Goals

* @author JoomPlace Team

* @Copyright Copyright (C) JoomPlace, www.joomplace.com

* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html

*/



defined('_JEXEC') or die('Restricted access');





class com_goalsInstallerScript

{

    /**

     * method to run before an install/update/uninstall method

     *

     * @return void

     */

    function preflight($type, $parent)

    {



        jimport('joomla.application.component.helper');

        $db = JFactory::getDbo();

        jimport( 'joomla.filesystem.folder' );

        jimport( 'joomla.filesystem.file' );

        $path = JPATH_ROOT . DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_goals' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'pChart' . DIRECTORY_SEPARATOR . 'Fonts';

        $isExists = JFolder::exists($path);

        if ($isExists) {

            $del = JFolder::delete($path);

            if (!$del) {

                echo "Could not delete folder<br/>";

            }

        }

    }



    function install($parent)

    {	?>

    <font style="font-size:2em; color:#55AA55;" >Personal Goals Manager component successfully installed.</font><br/><br/>

    <?php

    }





    function uninstall($parent)

    {



        echo '<p>' . JText::_('COM_GOALS_UNINSTALL_TEXT') . '</p>';

    }



    function update($parent)

    {

        ?>

    <font style="font-size:2em; color:#55AA55;" >Personal Goals Manager component successfully updated.</font><br/><br/>

    <?php	 

    }

    function postflight($type, $parent)

    {

        $imgpath = JURI::root().'/administrator/components/com_goals/assets/images/';



        $database = JFactory::getDBO();

        $newColumns = array(
			'_milistones' => array(
				'value' => "INT( 11 ) NOT NULL AFTER `description`"
			),
			'_milistonestemplates' => array(
				'value' => "INT( 11 ) NOT NULL AFTER `description`"
			),
			'_tasks' => array(
				'result_mode' => "TINYINT( 3 ) NOT NULL DEFAULT '0'"
			),
			'_plantasks' => array(
				'result_mode' => "TINYINT( 3 ) NOT NULL DEFAULT '0'"
			),
			'' => array(
				'startup' => "datetime NOT NULL"
			),
			'_plans' => array(
				'startup' => "datetime NOT NULL"
			)
		);

		foreach ($newColumns as $table => $fields)
		{
			$oldColumns = $database->getTableColumns('#__goals'.$table);

			foreach ( $fields as $key => $value)
			{
				if ( empty($oldColumns[$key]) )
				{
					$database->setQuery('ALTER TABLE `#__goals'.$table.'` ADD `'.$key.'` '.$value);
					$database->execute();
				}
			}
		}

		$database->setQuery('CREATE TABLE IF NOT EXISTS `#__goals_custom_plan_fields` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `title` varchar(250) NOT NULL,
		  `name` varchar(250) NOT NULL,
		  `type` varchar(3) NOT NULL,
		  `values` text NOT NULL,
		  `user` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) DEFAULT CHARSET=utf8;');
		$database->execute();

        $query = $database->getQuery(true);


        $query->select('1')->from('`#__goals_categories`')->where('`id`=1');

        $catExists = $database->setQuery($query)->loadResult();

        if (!$catExists) {

            $query = $database->getQuery($query);

            $query->insert('#__goals_categories');

            $query->columns(array('`id`','`title`','`date_created`','`published`'));

            $query->values("1, 'Uncategorized', '0000-00-00 00:00:00', 1");

            $database->setQuery($query)->query();



        }

        if ($type == 'install') {

            $params = '{"file_exts":"jpg,jpeg,png,gif,bmp","file_sizes":"","images_width":"250","images_height":"250","enable_jsoc_int":"0","jsoc_activity_mess_goal":"{actor} created a new goal: {goal}  with a due date {due_date}","jsoc_activity_mess_record":"{actor} created a new record: {record}  with value: {value} {metric} for {goal}","jsoc_activity_mess_plan":"{actor} created a new plan: {plan}  with a due date {due_date}","jsoc_activity_mess_task":"{actor} created a new task: {record}  with value: {value} {metric} for {plan}","jsoc_activity_mess_hab":"{actor} created new {type} habit: {habit}","away_status_color":"#4A8BE0","away_image":"images\/goals\/","late_status_color":"#ED8787","late_image":"images\/goals\/","complete_status_color":"#CF6F08","complete_image":"images\/goals\/","justint_status_color":"#67CF6B","justint_image":"images\/goals\/","n_days_ahed":"2","m_days_behind":"2","chart_date_format":"%d-%m-%y"}';

            $database->setQuery("UPDATE `#__extensions` SET `params` = '".$params."' WHERE `name`='com_goals'");

            $database->query();



            $assets = '{"core.admin":{"1":1,"8":1},"core.manage":{"1":1},"core.create":{"1":1},"core.delete":{"1":1},"core.edit.state":{"1":1},"core.edit":{"1":1}}';

            $database->setQuery("UPDATE `#__assets` SET `rules` = '".$assets."' WHERE `name`='com_goals'");

            $database->query();



            $query = $database->getQuery($query);

            $query->insert('#__goals_dashboard_items');

            $query->columns(array('`title`, `url`, `icon`, `published`'));

            $query->values("'Manage Goals', 'index.php?option=com_goals&view=goals', '".JURI::root()."media/com_goals/images/goals48.png', 1");

            $query->values("'Manage Plans', 'index.php?option=com_goals&view=plans', '".JURI::root()."media/com_goals/images/milistones48.png', 1");

            $query->values("'Manage Habits', 'index.php?option=com_goals&view=habits', '".JURI::root()."media/com_goals/images/habits48.png', 1");

            $query->values("'Help', 'http://www.joomplace.com/video-tutorials-and-documentation/personal-goals-manager/', '".JURI::root()."media/com_goals/images/help48.png', 1");

            $database->setQuery($query)->query();

        }



        ?>

    <style type="text/css">

        .installtable

        {

            border: 1px solid #D5D5D5;

            background-color: #F7F8F9;

            width: 100%;

            padding: 10px;

            border-collapse: collapse;

        }

        .installtable tr, .installtable th, .installtable td

        {

            border: 1px solid #D5D5D5;

        }

    </style>

    <table border="1" cellpadding="5" width="100%" class="installtable">

        <tr>

            <td colspan="2" style="background-color: #e7e8e9;text-align:left; font-size:16px; font-weight:400; line-height:18px "><strong><img src="<?php echo $imgpath;?>tick.png"/> Getting started.</strong> Helpfull links:</td>

        </tr>

        <tr>

            <td colspan="2" style="padding-left:20px">

                <div style="font-size:1.2em">

                    <ul>

                        <li><a href="index.php?option=com_goals&view=dashboard">Control Panel</a></li>

                        <li><a href="http://www.joomplace.com/video-tutorials-and-documentation/personal-goals-manager/" target="_blank">Component's help</a></li>

                        <li><a href="http://www.joomplace.com/forum/joomla-components/goals.html" target="_blank">Support forum</a></li>

                        <li><a href="http://www.joomplace.com/support/helpdesk/post-purchase-questions/ticket/create" target="_blank">Submit request to our technicians</a></li>

                    </ul>

                </div>

            </td>

        </tr>

        <tr>

            <td colspan="2" style="background-color: #e7e8e9;text-align:left; font-size:14px; font-weight:400; line-height:18px "><strong><img src="<?php echo $imgpath;?>tick.png"/>Latest changes: </strong></td>

        </tr>

    </table>

    <?php

        jimport( 'joomla.filesystem.file' );

        $file		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_goals' . DIRECTORY_SEPARATOR . 'changelog.txt';

        if (file_exists($file))

        {

            $content	= JFile::read( $file );

            echo '<pre>'.$content.'</pre>';

        }

    }

}