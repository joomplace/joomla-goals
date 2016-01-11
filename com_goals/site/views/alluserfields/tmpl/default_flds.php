<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');
$items = $this->userfields;
$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
?>
<table class="table table-condensed" border="0" width="100%">
	<tr>
		<th><?php echo JText::_('COM_GOALS_FIELD_NAME'); ?></th>
		<th><?php echo JText::_('COM_GOALS_FIELD_TYPE'); ?></th>
		<th style="text-align:center;"><?php echo JText::_('COM_GOALS_HABITACTIONS'); ?></th>
	</tr>
			 <?php if (sizeof($items)) {
				  	for ( $i = 0, $n = sizeof( $items ); $i < $n; $i++ )
				  	{
				  		$field = $items[$i];
			  ?>
			  <tr>
			  		<td width="30%">
			  			<?php  if (isset($field->title)) echo $field->title; ?>
					</td>
			  		<td>
			  			<?php  if (isset($field->type))
			  			{
			  				switch ( $field->type )
			  				{
								case 'tf': 	echo JText::_('COM_GOALS_FIELD_TEXTFIELD');	break;
								case 'ta':	echo JText::_('COM_GOALS_FIELD_TEXTAREA');	break;
								case 'hc':	echo JText::_('COM_GOALS_FIELD_HTMLCODE');	break;
								case 'em':	echo JText::_('COM_GOALS_FIELD_EMAIL');	break;
								case 'wu':	echo JText::_('COM_GOALS_FIELD_WEBURL');	break;
								case 'pc':	echo JText::_('COM_GOALS_FIELD_PICTURE');	break;
								case 'in':	echo JText::_('COM_GOALS_FIELD_INTEGER');	break;
								case 'sl':	echo JText::_('COM_GOALS_FIELD_SELECTLIST');	break;
								case 'ml':	echo JText::_('COM_GOALS_FIELD_MSELECT');	break;
								case 'ch':	echo JText::_('COM_GOALS_FIELD_CHECKBOX');	break;
								case 'rd':	echo JText::_('COM_GOALS_FIELD_RADIOBUTTON');	break;
								default:	break;
							}
			  			}
			  			?>
			  		</td>
				  	<td style="text-align:center;">
				  		<input type="button" class="btn btn-small" onclick="goalgoto('<?php echo JRoute::_('index.php?option=com_goals&view=edituserfield&id='.(int)$field->id.$tmpl);?>')" value="<?php echo JText::_('COM_GOAL_USERFIELD_EDITBUTTON'); ?>" />
						<input type="button" class="btn btn-small" onclick="if (confirm('<?php echo JText::_('COM_GOALS_DELETE_USERFIELD_MESS'); ?>'))goalgoto('<?php echo JRoute::_('index.php?option=com_goals&task=userfield.deletelog&id='.(int)$field->id.$tmpl);?>')" value="<?php echo JText::_('COM_GOALS_DELETE_RECORD'); ?>" />
				  	</td>
			  </tr>
			<?php
				}
				} else {?>
                    <tr><td colspan="3"><div class="gl_msntf"><?php echo JText::_('COM_GOALS_FIELDS_NOT_FOUND');?></div></td></tr>
			<?php }?>

</table>