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
$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
?>
            <div class="a11ccordion" id="a11ccordion">
			  <?php if (sizeof($items)) {
					  	for ( $i = 0, $n = sizeof( $items ); $i < $n; $i++ )
					  	{
					  	$milistone = $items[$i];
				  ?>
                                <div class="accordion-group">
                                    <div class="accordion-heading accordion-toggle" data-toggle="collapse" data-parent="#accordion">
								  		<div class="gl_goal_status_left_part">
								  			<?php
													$status = $milistone->leftstatus;
									  				$status_style = 'pending';
										  			switch ( $status )
										  			{
										  				case 1:	 $status_style = 'away'; break;
										  				case 2:	 $status_style = 'pending_away'; break;
										  				case 3:	 $status_style = 'pending'; break;
										  				case 4:	 $status_style = 'nocomplete'; break;
										  				default: $status_style = 'complete'; break;
										  			}
											?>
								  			<div class="gl_goal_<?php echo $status_style;?>">&nbsp;</div>
								  		</div>
										<div class="gl_mil_left_part">
											<h3 class="gl_goal_title">
												<a href="acocollapse<?php echo $i; ?>" data-toggle="collapse" data-parent="#accordion<?php echo $i; ?>"><?php echo $milistone->title;?></a>
											</h3>
											<div class="gl_goal_short_details">
												<?php
												if (isset($milistone->duedate))
													if ($milistone->duedate!='0000-00-00 00:00:00')
													{
														echo JText::_('COM_GOALS_DUE').': '.JHtml::_('date',$milistone->duedate, JText::_('DATE_FORMAT_LC2'));
													}
												?>
												<span class="gl_left_count">
													<?php
													if (isset($milistone->left))
													{
														echo $milistone->left;
													}
													?>
												</span>
											</div>
											<div style="clear:both"></div>
										</div>
										<div class="gl_right_link">
											<input type="button" class="btn" onclick="goalgoto('<?php echo JRoute::_('index.php?option=com_goals&view=editmilistone&id='.(int)$milistone->id.$tmpl);?>')" value="<?php echo JText::_('COM_GOAL_MIL_EDITBUTTON'); ?>" />
											<input type="button" class="btn" onclick="if (confirm('<?php echo JText::_('COM_GOALS_DELETE_MIL_MESS'); ?>'))goalgoto('<?php echo JRoute::_('index.php?option=com_goals&task=milistone.delete&id='.(int)$milistone->id.$tmpl);?>')" value="<?php echo JText::_('COM_GOALS_DELETE_MILISTONE'); ?>" />
										</div>
                                        <div class="clr"></div>
									</div>
									<div class="accordion-body collapse" id="acocollapse<?php echo $i; ?>">
										<div class="accordion-inner">
											<div class="clr"></div>
											<table width="100%" class="gl_goal_details_table" cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td class="gl_right_td"><?php echo JText::_('COM_GOALS_MIL_DESCRIPTION');?>:</td>
													<td><?php echo $milistone->description;?></td>
												</tr>
												<tr>
													<td class="gl_right_td"><?php echo JText::_('COM_GOALS_MIL_GOAL');?>:</td>
													<td>
														<?php
														$glink = JRoute::_('index.php?option=com_goals&view=goal&id='.$milistone->gid.$tmpl);
														echo '<a href="'.$glink.'">'.$milistone->gtitle.'</a>';
														?>
													</td>
												</tr>
												<?php if ($milistone->cdate!='0000-00-00 00:00:00') {?>
												<tr>
													<td class="gl_right_td"><?php echo JText::_('COM_GOALS_MIL_CREATED');?>:</td>
													<td><?php echo $milistone->cdate;?></td>
												</tr>
												<?php } ?>
												<tr>
													<td class="gl_right_td"><?php echo JText::_('COM_GOALS_MIL_STATUS');?>:</td>
													<td><?php
													if ($milistone->status=='1') echo JText::_('COM_GOALS_MIL_STAT_COMPLETE');
													else
													{
														if ($status==4)  echo JText::_('COM_GOALS_MIL_STAT_NOCOMPLETE');
														else  echo JText::_('COM_GOALS_MIL_STAT_INPROGRESS');
													}
													?></td>
												</tr>
											</table>
											<div class="clr"></div>
										</div>
									</div>
                                </div>

					<?php }?>
				<?php } else {?>
					<h2><?php echo JText::_('COM_GOALS_MILISTONES_NOT_FOUND');?></h2>
				<?php }?>
            </div>               