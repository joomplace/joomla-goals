<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');

if(JRequest::getVar('gid')) $inparent='&inparent=true';
$items = $this->recs;
$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';

function generateRecordActButtons($record){
    if(JRequest::getVar('gid')) $inparent='&inparent=true';
    $tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
    $html = array();
    $own = false;
    if ($record->uid == JFactory::getUser()->id) {
        $own = true;
    }
    if($own){
        $html[] ='<div class="btn-group">';
        $html[] ='<a type="button" class="btn" href="'.JRoute::_(GoalsHelperRoute::buildLink(array('view'=>'editrecord','id'=>(int)$record->id,'gid'=>(int)$record->gid))).'">'. JText::_('COM_GOAL_REC_EDITBUTTON').'</a>';
        $html[] ='<input type="button" class="btn" onclick="if (confirm(\''.JText::_('COM_GOALS_DELETE_REC_MESS').'\'))goalgoto(\''.JRoute::_(GoalsHelperRoute::buildLink(array('task'=>'record.delete','id'=>(int)$record->id,'return'=>GoalsHelperFE::getReturnURL()))).'\')" value="'.JText::_('COM_GOALS_DELETE_RECORD').'" />';
        $html[] ='</div>';
    }
    return implode('',$html);
}

?>
			  <?php if (sizeof($items)) {
					  	for ( $i = 0, $n = sizeof( $items ); $i < $n; $i++ )
					  	{
					  	$record = $items[$i];
				  ?>

                        <?php
                          echo JHtml::_('bootstrap.startAccordion', 'records', array('active' => 'rec'.JRequest::getVar('id')));
                        ?>
                        <?php
                            $date='';
                            if($record->date == '0000-00-00 00:00:00'){
                                $date = '<strong><small>'.JText::_('COM_GOALS_PLEASE_FILL_IN_THE_DATE').'</small></strong>';
                            } else {
                                $date = ''.JHtml::_('date', $record->date, JText::_('DATE_FORMAT_LC3')).'<br/>';
                            }
                            echo JHtml::_('bootstrap.addSlide', 'records', '<div class="row-fluid"><div class="span9">'.JText::_($record->title).'</div><div class="span3 text-right">'.$date.'</div></div>', 'rec'.$i);
                        ?>
                            <div class="row-fluid">
                                <div class="span6">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                        <?php
                            if (isset($record->value))
                            {
                                $sign = '';
                                if($record->value > 0 && $record->result_mode == 1) $sign = "+";
                                echo '<td><strong>'.JText::_('COM_GOALS_REC_VALUE').'</strong></td><td>'.$sign.$record->value.' '.$record->gmetric.'</td>';
                            }
                        ?>
                                        </tr>
                                        <tr>
                        <?php
                                $glink = JRoute::_('index.php?option=com_goals&view=goal&id='.$record->gid.$tmpl);
                                echo '<td><strong>'.JText::_('COM_GOALS_REC_GOAL').'</strong></td><td>'.'<a href="'.$glink.'">'.$record->gtitle.'</a></td>';
                        ?>
                                        </tr>
                                        <tr>
                        <?php
                                echo '<td><strong>'.JText::_('COM_GOALS_REC_DESCRIPTION').'</strong></td><td>'.$record->description.'</td>';
                        ?>
                                        </tr>





                                        <?php if(sizeof($record->cfields)){
                                            foreach ( $record->cfields as $cf )
                                            {

                                                if ($cf->inp_values)
                                                {
                                                    if ($cf->type!='pc') $value  = json_decode($cf->inp_values);
                                                    else
                                                    {
                                                        $value  = $cf->inp_values;
                                                        $value=str_replace('"','',$value);
                                                        if (file_exists(JPATH_SITE.$value) && is_file(JPATH_SITE.$value))
                                                        {
                                                            $value=str_replace(DS,'/',$value);
                                                            $value = '<img src="'.JURI::root().$value.'" alt="" />';
                                                        }
                                                    }
                                                    if (is_array($value)) $value = implode(', ',$value);
                                                    ?>
                                                    <tr>
                                                        <th class="gl_label"><?php echo $cf->title;?>: </th>
                                                        <td><?php echo $value;?></td>
                                                    </tr>
                                                <?php
                                                }	}

                                        } ?>
                                        <?php
                                        //echo 'SMT DEBUG: <pre>'; print_R($this->ufields); echo '</pre>';

                                        if(sizeof($record->ufields)){
                                            foreach ( $record->ufields as $cf )
                                            {
                                                // echo json_decode(str_replace('"','',$cf->inp_values),true).'<br />';
                                                if ($cf->inp_values)
                                                {

                                                    if ($cf->type!='pc') $value  = json_decode($cf->inp_values, true);
                                                    else
                                                    {
                                                        $value  = $cf->inp_values;

                                                        $value=str_replace(DS,'\\',$value);
                                                        $value=str_replace("\\","/",$value);
//fix for image
                                                        $value=str_replace('"','',$value);
                                                        if (file_exists(JPATH_SITE.$value) && is_file(JPATH_SITE.$value))
                                                        {
                                                            $value=str_replace(DS,'/',$value);
                                                            $value = '<img src="'.JURI::root().$value.'" alt="" />';
                                                        }
                                                    }
                                                    if (is_array($value)) $value = implode(', ',$value);
                                                    ?>
                                                    <tr>
                                                        <th class="gl_label"><?php echo $cf->title;?>: </th>
                                                        <td><?php echo $value;?></td>
                                                    </tr>
                                                <?php
                                                }	}

                                        } ?>
                                                </tbody>
                                            </table>
                                </div>

                                <div class="span6 text-right">
                                    <?php
                                    echo generateRecordActButtons($record);
                                    ?>
                                </div>
                            </div>
                        <?php

                            echo JHtml::_('bootstrap.endSlide');

                        ?>
					<?php }?>
				<?php } else {?>
					<h2><?php echo JText::_('COM_GOALS_RECORDS_NOT_FOUND');?></h2>
				<?php }?>