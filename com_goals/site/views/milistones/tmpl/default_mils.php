<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');
$items = $this->mils;
//$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
$tmpl='';
$spec_goal=JRequest::getInt('gid');

function generateRecordActButtons($milistone){
    if(JRequest::getVar('gid')) $inparent='&inparent=true';
    $tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
    $html = array();
    $html[] ='<div class="btn-group">';
    $html[] ='<a type="button" class="btn" href="'.JRoute::_('index.php?option=com_goals&view=editmilistone&id='.(int)$milistone->id.'&gid='.$milistone->gid.$tmpl).'">'. JText::_('COM_GOAL_MIL_EDITBUTTON').'</a>';
    $html[] ='<input type="button" class="btn" onclick="if (confirm('.JText::_('COM_GOALS_DELETE_MIL_MESS').'))goalgoto('.JRoute::_('index.php?option=com_goals&task=milistone.delete&id='.(int)$milistone->id.$tmpl.$inparent).')" value="'.JText::_('COM_GOALS_DELETE_MILISTONE').'" />';
    $html[] ='</div>';
    return implode('',$html);
}

?>


<?php if (sizeof($items)) {
    for ( $i = 0, $n = sizeof( $items ); $i < $n; $i++ )
    {
        $milistone = $items[$i];
        ?>

        <?php
        echo JHtml::_('bootstrap.startAccordion', 'miles', array('active' => 'mile'.'0'));
        ?>
        <?php
        echo JHtml::_('bootstrap.addSlide', 'miles', '<div>'.JText::_($milistone->title).'</div>', 'mile'.$i);
        ?>
        <div class="row-fluid">
            <div class="span6">
                <?php
                     if (!$spec_goal){
                ?>
                <p>
                    <strong><?php echo JText::_('COM_GOALS_MIL_GOAL');?>:</strong>
                        <?php
                        $glink = JRoute::_('index.php?option=com_goals&view=goal&id='.$milistone->gid.$tmpl);
                        echo '<a href="'.$glink.'">'.$milistone->gtitle.'</a>';
                        ?>
                </p>
                <?php
                }
                ?>
                <?php if(!$milistone->status){ ?>
                    <div>
                        <div class="row-fluid">
                            <div class="span6">
                                <strong>
                                    <span class="icon-flag"> </span> <?php echo JText::_('COM_GOALS_MILESTONE_NEEDED_VALUE'); ?>
                                </strong>
                            </div>
                            <div class="span6">
                                <?php echo $milistone->value.' '.$item->metric; ?>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span6">
                                <strong>
                                    <span class="icon-clock"> </span>
                                    <?php echo JText::_('COM_GOALS_DUE'); ?>
                                </strong>
                            </div>
                            <div class="span6">
                                <?php echo  JHtml::_('date', $milistone->deadline, JText::_('DATE_FORMAT_LC3')); ?>
                            </div>
                        </div>
                    </div>
                <?php }else{ ?>
                    <div>
                        <span class="icon-checkmark"> </span> <?php echo $milistone->description; ?>
                    </div>
                <?php } ?>
            </div>

            <div class="span6 text-right">
                <?php
                echo generateRecordActButtons($milistone);
                ?>
            </div>
        </div>
        <?php

        echo JHtml::_('bootstrap.endSlide');

        ?>
    <?php }?>
<?php } else {?>
    <h2><?php echo JText::_('COM_GOALS_MILISTONES_NOT_FOUND');?></h2>
<?php }?>