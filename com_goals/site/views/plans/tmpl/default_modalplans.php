<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');
$items = $this->goals;
$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
$function	= JRequest::getCmd('function', 'jSelectGoal');
?>
			  <?php if (sizeof($items)) {
					  	for ( $i = 0, $n = sizeof( $items ); $i < $n; $i++ )
					  	{
					  		$goal = $items[$i];

					  		$status = $goal->status;
				  			$procent = $goal->percent;

				  			switch ( $status )
				  			{
				  				case 1:	 $status_style = 'away'; 		 $pstyle = 'blue'; 	break;
				  				case 2:	 $status_style = 'pending_away'; $pstyle = 'green'; break;
				  				case 3:	 $status_style = 'pending'; 	 $pstyle = 'yellow';break;
				  				case 4:	 $status_style = 'nocomplete'; 	 $pstyle  = 'red'; 	break;
				  				default: $status_style = 'complete'; 	 $pstyle = 'gray'; break;
				  			}
				  ?>

								  <div class="gl_goals-item">
								  <div class="gl_goal_togglers">
								  		<div class="gl_goal_status_left_part">
								  			<div class="gl_goal_<?php echo $status_style;?>">&nbsp;</div>
								  		</div>
										<div class="gl_goal_left_part">
											<div class="gl_goal_title">
												<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $function;?>('<?php echo $goal->id; ?>');">
												<?php echo $this->escape($goal->title);?>
												</a>
											</div>
											<div class="gl_goal_short_details">
												<?php
												if (isset($goal->deadline))
													if ($goal->deadline!='0000-00-00 00:00:00')
													{
														echo JText::_('COM_GOALS_DUE').': '.JHtml::_('date',$goal->deadline, JText::_('DATE_FORMAT_LC2'));
													}
												?>
											</div>
											<div style="clear:both"></div>
										</div>
										<div class="gl_goal_progress">
											<div class="pb_width">
												<div class="progressbar_background">
													<div style="width: <?php echo $procent?>%;" class="progressbar_progress_<?php echo $pstyle;?>">&nbsp;</div>
												</div>
											</div>
											<div class="progressbar_label_right"><?php echo $procent;?>%</div>
										</div>
										<div class="clr"></div>
									</div>
									<div class="gl_goal_details">
										<div class="clr"></div>
										<table width="100%" class="gl_goal_details_table" cellpadding="0" cellspacing="0" border="0">
											<?php if ($goal->image && is_file(JPATH_SITE.DIRECTORY_SEPARATOR.$goal->image)) { ?>
											<tr>
												<td rowspan="6" width="15%" valign="top"><img src="<?php echo $goal->image;?>" alt="" /></td>
											</tr>
											<?php }?>
											<tr>
												<td class="gl_right_td"><?php echo JText::_('COM_GOALS_GOAL_DESCRIPTION');?>:</td>
												<td><?php echo $goal->description;?></td>
											</tr>
											<tr>
												<td class="gl_right_td"><?php echo JText::_('COM_GOALS_GOAL_CATEGORY');?>:</td>
												<td><?php echo $goal->catname;?></td>
											</tr>
											<tr>
												<td class="gl_right_td"><?php echo JText::_('COM_GOALS_GOAL_START');?>:</td>
												<td><?php echo $goal->start;?> <?php echo $goal->metric;?></td>
											</tr>
											<tr>
												<td class="gl_right_td"><?php echo JText::_('COM_GOALS_GOAL_FINISH');?>:</td>
												<td><?php echo $goal->finish;?> <?php echo $goal->metric;?></td>
											</tr>
										</table>
										<div class="clr"></div>
									</div>
								  </div>

					<?php }?>
				<?php } else {?>
					<h2><?php echo JText::_('COM_PLANS_NOT_FOUND');?></h2>
				<?php }?>