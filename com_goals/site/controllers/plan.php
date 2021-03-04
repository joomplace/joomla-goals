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

class GoalsControllerPlan extends JControllerForm
{

    public function getModel($name = 'EditPlan', $prefix = 'GoalsModel', $config = array('ignore_request' => true))
    {

        if (empty($name)) {
            $name = $this->context;
        }

        return parent::getModel($name, $prefix, $config);
    }

    public function upload()
    {
        $model = $this->getModel('Plan');

        return $model->upload();
    }

    function save($key = null, $urlVar = null)
    {
        $app = JFactory::getApplication();
        $context = "$this->option.edit.$this->context";
        $app->setUserState($context . '.id', JRequest::getInt('id'));

        return parent::save();
    }

    function delete()
    {
        // Get items to remove from the request.
        $id = JRequest::getVar('id', array(), '', 'array');

        $tmpl = JRequest::getVar('tmpl');
        if ($tmpl == 'component') $tmpl = '&tmpl=component'; else $tmpl = '';
        if (!is_array($id) || count($id) < 1) {
            JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
        } else {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($id);

            // Remove the items.
            if ($model->delete($id)) {

                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('id');
                $query->from('#__goals_stages');
                $query->where('pid='.$id[0]);
                $db->setQuery($query);
                $ids = $db->loadColumn();  //SELECT id's all stages

                $query = $db->getQuery(true);
                $query->delete('#__goals_stages');
                $query->where('pid='.$id[0]);
                $db->setQuery($query);
                $db->query();  //Remove all stages


                 if ($ids) {
                $query = $db->getQuery(true);
                $query->delete('#__goals_plantasks');
                $query->where('sid IN ('.implode(',',$ids).')');
                $db->setQuery($query);
                $db->query();      //Remove all tasks

              }
                $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($id)));
            } else {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $tmpl, false));
    }

    function cancel($key = null)
    {
        $mainframe = JFactory::getApplication();
        $id = JRequest::getInt('id');
        $tmpl = JRequest::getVar('tmpl');
        if ($tmpl == 'component') $tmpl = '&tmpl=component'; else $tmpl = '';
        $url = JRoute::_('index.php?option=com_goals' . $tmpl);
        if (isset($id)) {
            if ($id > 0) $url = JRoute::_('index.php?option=com_goals&view=plan&id=' . (int)$id . $tmpl, false);
        }
        $this->setRedirect($url, false);
    }

    function getuserfields()
    {
        $id = JRequest::getInt('id');
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $query = $db->getQuery(true);
        $query->select('`id` AS `value`, (`title`) AS `text`');
        $query->from('#_goals_plan_custom_fields');
        $query->where('`user`=' . (int)$user->id);
        $query->order('`title` ASC');
        $db->setQuery($query);
        $options = $db->loadObjectList();

        $query = $db->getQuery(true);
        $query->select('fid');
        $query->from('#__goals_plans_xref');
        $query->where('pid=' . (int)$id);
        $db->setQuery($query);
        $selected = $db->loadColumn();

        echo JHtml::_('select.genericlist', $options, 'jform[userfields][]', 'multiple="true" ', 'value', 'text', $selected, $id);
        die();
    }

    function showGoalGraph()
    {
        error_reporting(E_ALL & ~E_NOTICE);
        $settings = GoalsHelper::getSettings();
        $date_format = trim(str_replace('%', '', $settings->chart_date_format));
        
        $id = JRequest::getInt('id', 0);

        $dates = $values = array();

        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('p.*')
            ->from('`#__goals_plans` AS `p`')
            ->where('`p`.`id`=' . $id)

            ->order('`p`.`deadline` DESC');
        $db->setQuery($query);
        $plan = $db->loadObject();

        if (!$plan->id) return null;

        $query = $db->getQuery(true)
            ->select('s.*')
            ->from('`#__goals_stages` AS `s`')
            ->where('`s`.`pid`=' . $plan->id)
            ->order('`s`.`duedate` DESC');
        $db->setQuery($query);
        $stages = $db->loadColumn();

        $query = $db->getQuery(true)
            ->select('r.*')
            ->from('`#__goals_plantasks` AS `r`')
            ->where('`r`.`sid` IN (' . implode(',',$stages).')')
            ->where('`r`.`status` = 1')
            ->order('`r`.`c_date` ASC, `r`.`id` DESC');
        $db->setQuery($query);
        $records = $db->loadObjectList();

        $summary = 0;
        if (!empty($records)) {
            foreach ($records as $record) {

                    $dates[] = date($date_format, strtotime($record->c_date));
                    if($record->result_mode){
                       $summary += $record->value;
                       $values[] = $summary;
                    } else {
                        $values[] = $record->value;
                        $summary = $record->value;
                    }

            }
        }

        /* pChart library inclusions */
        require_once (JPATH_COMPONENT . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "pChart" . DIRECTORY_SEPARATOR . "class" . DIRECTORY_SEPARATOR . "pData.class.php");
        require_once (JPATH_COMPONENT . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "pChart" . DIRECTORY_SEPARATOR . "class" . DIRECTORY_SEPARATOR . "pDraw.class.php");
        require_once (JPATH_COMPONENT . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "pChart" . DIRECTORY_SEPARATOR . "class" . DIRECTORY_SEPARATOR . "pImage.class.php");

        /* Create and populate the pData object */
        $myData = new pData();
        $myData->addPoints(!empty($values) ? $values : array(VOID, VOID, VOID, VOID, VOID), "Serie1");
        $myData->setSerieDescription("Records", "Serie 1");
        $myData->setSerieOnAxis("Serie1", 0);
        $myData->addPoints(!empty($dates) ? $dates : array(VOID, VOID, VOID, VOID, VOID), "Absissa");
        $myData->setAbscissa("Absissa");
        $myData->setAxisPosition(0, AXIS_POSITION_LEFT);
        $myData->setAxisName(0, "");
        $myData->setAxisUnit(0, "");

        /* Create the pChart object */
        $myPicture = new pImage(700, 230, $myData);

        /* Set the default font properties */
        $myPicture->setFontProperties(array("FontName" => JPATH_COMPONENT . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "pChart" . DIRECTORY_SEPARATOR . "fonts" . DIRECTORY_SEPARATOR . "Forgotte.ttf", "FontSize" => 14));
        $TextSettings = array(
            "Align" => TEXT_ALIGN_MIDDLEMIDDLE,
            "R"     => 150,
            "G"     => 150,
            "B"     => 150
        );
        $myPicture->drawText(350, 25, $goal->title, $TextSettings);

        /* Draw the scale and the chart */
        $myPicture->setGraphArea(50, 50, 675, 190);
        $myPicture->setFontProperties(array("R" => 0, "G" => 0, "B" => 0, "FontName" => JPATH_COMPONENT . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "pChart" . DIRECTORY_SEPARATOR . "fonts" . DIRECTORY_SEPARATOR . "tahoma.ttf", "FontSize" => 8));
        if (empty($values)) {
            $myPicture->drawText(370, 118, JText::_('COM_GOALS_CHART_NO_DATA'), array("FontSize" => 16, "Align" => TEXT_ALIGN_BOTTOMMIDDLE, "R" => 206, "G" => 206, "B" => 206));
        }

        $Settings = array(
            "Pos"             => SCALE_POS_LEFTRIGHT,
            "Mode"            => SCALE_MODE_FLOATING,
            "LabelingMethod"  => LABELING_ALL,
            "GridR"           => 214,
            "GridG"           => 212,
            "GridB"           => 212,
            "GridAlpha"       => 50,
            "TickR"           => 130,
            "TickG"           => 121,
            "TickB"           => 121,
            "TickAlpha"       => 50,
            "LabelRotation"   => 0,
            "CycleBackground" => 1,
            "DrawYLines"      => ALL
        );
        $myPicture->drawScale($Settings);

        $Config = array(
            "DisplayValues"     => 0,
            "ForceTransparency" => 50,
            "AroundZero"        => 1
        );
        $myPicture->drawFilledSplineChart($Config);
        $myPicture->drawPlotChart(array("PlotSize" => 1, "PlotBorder" => true, "BorderSize" => 2, "BorderAlpha" => 20));
        $myPicture->setShadow(true, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));
        $myPicture->setFontProperties(array("FontName" => JPATH_COMPONENT . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "pChart" . DIRECTORY_SEPARATOR . "fonts" . DIRECTORY_SEPARATOR . "tahoma.ttf", "FontSize" => 7));

        if (!empty($values)) {
            // Get even keys from records array for label position
            foreach ($records as $record) {
                $rval[] = $record->value;
            }
            $rkeys = array_keys($rval);
            if(count($rkeys) >= 2){
                $rkeys = array($rkeys[0], end($rkeys));
            }

            $LabelSettings = array(
                "NoTitle"          => true,
                "DrawSerieColor"   => false,
                "DrawPoint"        => false,
                "DrawVerticalLine" => false,
                "VerticalLineR"    => 180,
                "VerticalLineG"    => 195,
                "VerticalLineB"    => 114,
                "HorizontalMargin" => 3,
                "BoxWidth"         => 20,
                "GradientEndR"     => 220,
                "GradientEndG"     => 255,
                "GradientEndB"     => 220
            );
            $myPicture->writeLabel(array("Serie1"), $rkeys, $LabelSettings);
        }

        $myPicture->stroke();
        die;
    }

    public function sortingFeatured()
    {
        $post = JRequest::get('post');
        $items = $post['ids'];
        $items_array = explode(',', $items);
        $i = 1;
        foreach ($items_array as $item) {
            $table = explode('-', $item);
            $id = $table[1];
            $table = $table[0];
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            if ($table == 'goal') {
                $query->update('#__goals');

            } else {
                $query->update('#__goals_plans');
            }

            $query->set('ordering=' . $i);
            $query->where('id = ' . $id);
            $db->setQuery($query);
            $db->query();
            $i++;

        }

        die;
    }

    public function featuredOnOff()
    {
        $id = JRequest::getVar('id');
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('featured');
        $query->from('#__goals_plans');
        $query->where('`id`='.$id);

        $db->setQuery($query);
        $featured = (int)$db->loadResult();
        $query = $db->getQuery(true);
        $query->update('#__goals_plans');
        $query->set('featured='.($featured?'0':'1'));
        $query->where('id='.$id);
        $db->setQuery($query)->query();

        $this->setRedirect(JRoute::_(GoalsHelperRoute::buildLink(array('view' => 'plans', 'tmpl' => $tmpl)), false));
        return;
    }


}
