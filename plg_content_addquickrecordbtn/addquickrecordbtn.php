<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class PlgContentAddquickrecordbtn extends JPlugin
{
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
    }

    public function onContentAfterDisplay($context, &$row, &$params, $page = 0)
    {
        JLoader::register('GoalsHelper', JPATH_BASE.'components/com_goals/helpers/goals.php');
        JLoader::load('GoalsHelper');

        $form = "<form class=\"row\" action=\"".JRoute::_('index.php?option=com_goals&view=goal&task=record.save')."\" method=\"post\">
                        <div class=\"col-xs-12\">
                            <div class=\"input-append\">
                                <div class=\"form-group\">
                                    <div class=\"input-group \">
                                        <input name=\"jform[value]\" class=\"span3\" value=\"1\" type=\"number\">
                                        <button class=\"btn\" type=\"submit\">Add quick Record</button>
                                    </div>
                                </div>                                              
                                <input name=\"jform[title]\" value=\"Quick record\" type=\"hidden\">
                                <input name=\"jform[gid]\" value=\"$1\" type=\"hidden\">
                                <input name=\"jform[result_mode]\" value=\"1\" type=\"hidden\">
                                <input name=\"return\" value=\"".GoalsHelper::getReturnURL()."\" type=\"hidden\">
                                ".JHtml::_('form.token')."                                      
                            </div>
                        </div>
                    </form>";
        $row->text = preg_replace("({addgoal ([0-9]+)})", $form, $row->text);
    }
}
