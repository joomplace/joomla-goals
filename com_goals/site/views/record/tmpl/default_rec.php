<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');
$record = $this->rec;
$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
$date_format = str_replace('%', '', $this->settings->chart_date_format);
?>
	 	<div class="gl_goals-item well">
		  		<div class="gl_goal_status_left_part">
		  			<div class="gl_record_image">&nbsp;</div>
		  		</div>
				<div class="gl_goal_left_part">
					<div class="gl_goal_title">
						<?php echo $record->title;	?>
					</div>
					<div class="gl_record_details">
						<span class="gl_task_count">
							<?php
								if (isset($record->value))
								{
									echo JText::_('COM_GOALS_REC_VALUE').':'.$record->value.' '.$record->gmetric;
								}
							?>
						</span>
					</div>
					<div style="clear:both"></div>
				</div>

				<div>
					<?php if (isset($record->date)) echo JHtml::_('date', $record->date, $date_format); ?>
					<div class="gl_right_link">
						<input type="button" class="btn" onclick="goalgoto('<?php echo JRoute::_('index.php?option=com_goals&view=editrecord&id='.(int)$record->id.'&gid='.$record->gid.$tmpl);?>')" value="<?php echo JText::_('COM_GOAL_REC_EDITBUTTON'); ?>" />
						<input type="button" class="btn" onclick="if (confirm('<?php echo JText::_('COM_GOALS_DELETE_MIL_MESS'); ?>'))goalgoto('<?php echo JRoute::_('index.php?option=com_goals&task=record.delete&id='.(int)$record->id.$tmpl);?>')" value="<?php echo JText::_('COM_GOALS_DELETE_RECORD'); ?>" />
					</div>
				</div>
		  <div style="clear:both"></div>
	 </div>

	<div class="gl_goal_details">
				<div class="clr"></div>
				<table width="100%" class="gl_goal_details_table" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<th class="gl_label"><?php echo JText::_('COM_GOALS_REC_DESCRIPTION');?>:</th>
						<td><?php echo $record->description;?></td>
					</tr>
					<tr>
						<th class="gl_label"><?php echo JText::_('COM_GOALS_REC_GOAL');?>:</th>
						<td>
							<?php
							$glink = JRoute::_('index.php?option=com_goals&view=goal&id='.$record->gid.$tmpl);
							echo '<a href="'.$glink.'">'.$record->gtitle.'</a>';
							?>
						</td>
					</tr>
					<?php if(sizeof($this->cfields)){
						foreach ( $this->cfields as $cf )
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

					if(sizeof($this->ufields)){
						foreach ( $this->ufields as $cf )
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

				</table>
				<div class="clr"></div>
	</div>