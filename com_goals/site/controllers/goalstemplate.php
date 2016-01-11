<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class GoalsControllerGoalsTemplate extends JControllerList
{

	public function getModel($name = 'GoalsTemplate', $prefix = 'GoalsModel', $config = array('ignore_request' => true))
	{

		if (empty($name)) {
			$name = $this->context;
		}

		return parent::getModel($name, $prefix, $config);
	}

	public function upload()
	{
		$model = $this->getModel('Goal');
		return $model->upload();
	}

	function save()
	{
		$app        = JFactory::getApplication();
		$context    = "$this->option.edit.$this->context";
		$app->setUserState($context.'.id',JRequest::getInt('id'));
		return parent::save();
	}

	function delete()
	{
		// Get items to remove from the request.
		$id    = JRequest::getVar('id', array(), '', 'array');
		$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
		if (!is_array($id) || count($id) < 1) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($id);

			// Remove the items.
			if ($model->delete($id)) {
				$this->setMessage(JText::plural($this->text_prefix.'_N_ITEMS_DELETED', count($id)));
			} else {
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$tmpl, false));
	}

	function cancel()
	{
		$mainframe = JFactory::getApplication();
		$id = JRequest::getInt('id');
		$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
		$url =  JRoute::_('index.php?option=com_goals'.$tmpl);
		if (isset($id))
		{
			if ($id>0) $url = JRoute::_('index.php?option=com_goals&view=goal&id='.(int)$id.$tmpl);
		}
		$this->setRedirect($url,false);
	}

	function getuserfields()
	{
		$id = JRequest::getInt('id');
		$db        = JFactory::getDbo();
		$user = JFactory::getUser();
		$query    = $db->getQuery(true);
		$query->select('`id` AS `value`, (`title`) AS `text`');
		$query->from('#__goals_custom_fields');
		$query->where('`user`='.(int)$user->id);
		$query->order('`title` ASC');
		$db->setQuery($query);
		$options = $db->loadObjectList();

		$query    = $db->getQuery(true);
		$query->select('fid');
		$query->from('#__goals_xref');
		$query->where('gid='.(int)$id);
		$db->setQuery($query);
		$selected = $db->loadColumn();

		echo JHtml::_('select.genericlist', $options, 'jform[userfields][]', 'multiple="true" ', 'value', 'text', $selected, $id);
		die();
	}

	function showGoalGraph()
	{
		error_reporting(E_ALL & ~E_NOTICE);
		$id = JRequest::getInt('id', 0);

		$dates = $values = array();

		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
					->select('g.*')
					->from('`#__goals` AS `g`')
					->where('`g`.`id`='.$id)
					->order('`g`.`deadline` DESC');
		$db->setQuery($query);
		$goal = $db->loadObject();

		if (!$goal->id)    return null;

		$query = $db->getQuery(true)
					->select('r.*')
					->from('`#__goals_tasks` AS `r`')
					->where('`r`.`gid`='.$id)
					->order('`r`.`date` ASC');
		$db->setQuery($query);
		$records = $db->loadObjectList();

		if (sizeof($records)) {
			foreach ( $records as $record ) {
				if ($record->value !== '0') {
					$dates[] = date('d/m/Y', strtotime($record->date));
					$values[] = $record->value;
				}
			}
		}

		//sort($dates);

		/* pChart library inclusions */
		require_once (JPATH_COMPONENT.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."pChart".DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."pData.class.php");
		require_once (JPATH_COMPONENT.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."pChart".DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."pDraw.class.php");
		require_once (JPATH_COMPONENT.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."pChart".DIRECTORY_SEPARATOR."class".DIRECTORY_SEPARATOR."pImage.class.php");

		/* Create and populate the pData object */
		$myData = new pData();
		$myData->addPoints(sizeof($values) ? $values : array(VOID,VOID,VOID,VOID,VOID), "Serie1");
		$myData->setSerieDescription("Records", "Serie 1");
		$myData->setSerieOnAxis("Serie1",0);
		$myData->addPoints(sizeof($dates) ? $dates : array(VOID,VOID,VOID,VOID,VOID), "Absissa");
		$myData->setAbscissa("Absissa");
		$myData->setAxisPosition(0,AXIS_POSITION_LEFT);
		$myData->setAxisName(0,"");
		$myData->setAxisUnit(0,"");

		/* Create the pChart object */
		$myPicture = new pImage(700,230,$myData);

		/* Set the default font properties */
		$myPicture->setFontProperties(array("FontName"=>JPATH_COMPONENT.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."pChart".DIRECTORY_SEPARATOR."fonts".DIRECTORY_SEPARATOR."Forgotte.ttf","FontSize"=>14));
		$TextSettings = array(
							"Align"	=> TEXT_ALIGN_MIDDLEMIDDLE,
							"R"		=> 150,
							"G"		=> 150,
							"B"		=> 150
						);
		$myPicture->drawText(350,25,$goal->title,$TextSettings);

		/* Draw the scale and the chart */
		$myPicture->setGraphArea(50,50,675,190);
		$myPicture->setFontProperties(array("R"=>0,"G"=>0,"B"=>0,"FontName"=>JPATH_COMPONENT.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."pChart".DIRECTORY_SEPARATOR."fonts".DIRECTORY_SEPARATOR."tahoma.ttf","FontSize"=>8));
		if (!sizeof($values)) {
			$myPicture->drawText(370, 118, JText::_('COM_GOALS_CHART_NO_DATA'), array("FontSize"=>16, "Align"=>TEXT_ALIGN_BOTTOMMIDDLE, "R"=>206, "G"=>206, "B"=>206));
		}

		$Settings = array(
				"Pos" 			 	=> SCALE_POS_LEFTRIGHT,
				"Mode" 			 	=> SCALE_MODE_FLOATING,
				"LabelingMethod" 	=> LABELING_ALL,
				"GridR" 		 	=> 214,
				"GridG" 		 	=> 212,
				"GridB" 		 	=> 212,
				"GridAlpha" 	 	=> 50,
				"TickR" 		 	=> 130,
				"TickG" 		 	=> 121,
				"TickB" 		 	=> 121,
				"TickAlpha" 	 	=> 50,
				"LabelRotation"		=> 0,
				"CycleBackground" 	=> 1,
				"DrawYLines" 		=> ALL
		);
		$myPicture->drawScale($Settings);

		$Config = array(
				"DisplayValues"		=> 0,
				"ForceTransparency"	=> 50,
				"AroundZero"		=> 1
		);
		$myPicture->drawFilledSplineChart($Config);
		$myPicture->drawPlotChart(array("PlotSize"=>1,"PlotBorder"=>TRUE,"BorderSize"=>2,"BorderAlpha"=>20));
		$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
		$myPicture->setFontProperties(array("FontName"=>JPATH_COMPONENT.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."pChart".DIRECTORY_SEPARATOR."fonts".DIRECTORY_SEPARATOR."tahoma.ttf","FontSize"=>7));

		if (sizeof($values)) {
			// Get even keys from records array for label position
			foreach ($records as $record) {
				$rval[] = $record->value;
			}
			$rkeys = array_keys($rval);
			for ($i=0; $i <= count($rkeys); $i++) {
				if ($rkeys[$i] % 2 != 0) {
					unset($rkeys[$i]);
				}
			}

			$LabelSettings = array(
					"NoTitle"			=> TRUE,
					"DrawSerieColor"	=> FALSE,
					"DrawPoint"			=> FALSE,
					"DrawVerticalLine"	=> FALSE,
					"VerticalLineR"		=> 180,
					"VerticalLineG"		=> 195,
					"VerticalLineB"		=> 114,
					"HorizontalMargin"	=> 3,
					"BoxWidth"			=> 20,
					"GradientEndR"		=> 220,
					"GradientEndG"		=> 255,
					"GradientEndB"		=> 220
					);
			$myPicture->writeLabel(array("Serie1"), $rkeys, $LabelSettings);
		}

		$myPicture->stroke();
		die;
	}

}
