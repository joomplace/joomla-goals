<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');
$items = $this->stages;
//$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
$tmpl='';
$date_format = str_replace('%', '', $this->settings->chart_date_format);
?>
			  <?php if (sizeof($items)) {
					  	for ( $i = 0, $n = sizeof( $items ); $i < $n; $i++ )
					  	{
					  	$stage = $items[$i];
				  ?>
				  				<div class="gl_goal_togglers">
								    <div class="gl_goals-item well">
								  		<div class="gl_goal_status_left_part">
								  			<?php
													$status = $stage->leftstatus;
									  				$status_style = $stage->status_image;

											?>
								  			<div class="gl_goal_<?php echo $status_style;?>">&nbsp;</div>
								  		</div>
										<div class="gl_mil_left_part">
											<div class="gl_goal_title">
												<?php echo $stage->title;?>
											</div>
											<div class="gl_goal_short_details">
												<?php
												if (isset($stage->duedate))
													if ($stage->duedate!='0000-00-00 00:00:00')
													{
														echo JText::_('COM_GOALS_DUE').': '.JHtml::_('date',$stage->duedate, $date_format);
													}
												?>
												<span class="gl_left_count">
													<?php
													if (isset($stage->left))
													{
														echo $stage->left;
													}
													?>
												</span>
											</div>
											<div style="clear:both"></div>
										</div>
										<div class="gl_right_link">
                                            <a class="btn"
                                               href="<?php echo JRoute::_('index.php?option=com_goals&view=editplantask&pid='.JRequest::getInt('pid').'&sid='.$stage->id); ?>"><?php echo JText::_('COM_GOALS_ADD_TASK')?></a>
											<input type="button" class="btn" onclick="goalgoto('<?php echo JRoute::_('index.php?option=com_goals&view=editstage&id='.(int)$stage->id.'&pid='.JRequest::getInt('pid').$tmpl);?>')" value="<?php echo JText::_('COM_GOAL_MIL_EDITBUTTON'); ?>" />
											<input type="button" class="btn" onclick="if (confirm('<?php echo JText::_('COM_GOALS_DELETE_MIL_MESS'); ?>'))goalgoto('<?php echo JRoute::_('index.php?option=com_goals&task=stage.delete&id='.(int)$stage->id.$tmpl);?>')" value="<?php echo JText::_('COM_GOALS_DELETE_MILISTONE'); ?>" />
										</div>
                                        <div style="clear:both"></div>
									</div>
									<div class="gl_goal_details">
										<div class="clr"></div>
										<table width="100%" class="gl_goal_details_table" cellpadding="0" cellspacing="0" border="0">
											<tr>
												<th class="gl_label"><?php echo JText::_('COM_GOALS_MIL_DESCRIPTION');?>:</th>
												<td><?php echo $stage->description;?></td>
											</tr>
											<tr>
												<th class="gl_label"><?php echo JText::_('COM_GOALS_STAGE_GOAL');?>:</th>
												<td>
													<?php
													$glink = JRoute::_('index.php?option=com_goals&view=plan&id='.$stage->pid.$tmpl);
													echo '<a href="'.$glink.'">'.$stage->gtitle.'</a>';
													?>
												</td>
											</tr>
											<?php if ($stage->cdate!='0000-00-00 00:00:00') {?>
											<tr>
												<th class="gl_label"><?php echo JText::_('COM_GOALS_MIL_CREATED');?>:</th>
												<td><?php echo $stage->cdate;?></td>
											</tr>
											<?php } ?>
											<tr>
												<th class="gl_label"><?php echo JText::_('COM_GOALS_MIL_STATUS');?>:</th>
												<td><?php
												if ($stage->status=='1') echo JText::_('COM_GOALS_MIL_STAT_COMPLETE');
												else
												{
													if ($status==4)  echo JText::_('COM_GOALS_MIL_STAT_NOCOMPLETE');
													else  echo JText::_('COM_GOALS_MIL_STAT_INPROGRESS');
												}
												?></td>
											</tr>
											<tr>
												<th class="gl_label"><?php echo JText::_('COM_GOALS_PLANTASKS');?>:</th>
                                                <td>
                                                    <?php
                                                    $glink = JRoute::_('index.php?option=com_goals&view=plantasks&pid='.$stage->pid.'&sid'.$stage->id.$tmpl);
                                                    echo '<a href="'.$glink.'">' . JText::_('COM_GOALS_PLANTASKS') . ' (' . count($stage->tasks) . ')</a>';
                                                    ?>
                                                </td>
											</tr>
										</table>
										<div class="clr"></div>
									</div>
								  </div>

					<?php }?>
				<?php } else {?>
					<h2><?php echo JText::_('COM_GOALS_STAGES_NOT_FOUND');?></h2>
				<?php }?>