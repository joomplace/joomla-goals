<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die('Restricted access');
$jdate = new JDate(strtotime($this->getCalendarDate()));

//$calendar_header = $jdate->toFormat(JText::_('%B %Y'));
$calendar_header = $jdate->format(JText::_('M Y'));
$calendar		=& $this->getCalendar();
$tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') $tmpl='&tmpl=component'; else $tmpl='';
$prev_month= strtotime($this->getCalendarDate(). ' -1 month');
$next_month= strtotime($this->getCalendarDate(). ' +1 month');
$itemId = JRequest::getInt('Itemid');
$itemId = $itemId?('&Itemid='.$itemId):'';
?>
<div id="mt_calendar">
<div class="rc_container rc_container_gray calendar">
	<div class="cn tl"></div>
	<div class="cn tr"></div>
	<div class="rc_container_inner">
		<h2 class="container_heading">
		<div class="left_arrow">
			<a href="javascript: void(0);" onclick="javascript: refresh_calendar('<?php echo JURI::root(); ?>index.php?option=com_goals&view=calendar&action=refresh_calendar&date=<?php echo $prev_month.$itemId?>');">&nbsp;</a>
		</div>
		&nbsp;
		<?php echo $calendar_header; ?>
		<div class="right_arrow">
			<a href="javascript: void(0);" onclick="javascript: refresh_calendar('<?php echo JURI::root(); ?>index.php?option=com_goals&view=calendar&action=refresh_calendar&date=<?php echo $next_month.$itemId?>');">&nbsp;</a>
		</div>
		&nbsp;
		</h2>
	</div>

<div id="mt_loading_cal_indicator" style="left:35%;cursor: wait;display:none; z-index:1002; padding:1px; margin:1px; position:absolute; top:45%; background: #ffffff;">
	<img src="<?php echo JURI::root();?>components/com_goals/assets/images/progress.gif" alt="Loading calendar..." title="Loading calendar..." style="border:none !important;" />
</div>

<table cellpadding="0" cellspacing="0" class="calendar_table" id="calendar_table">
	<tr style="text-align:center;">
		<td class="week_names" id="mon"><?php echo GoalsHelper::dayToStr(1, true);?></td>
		<td class="week_names" id="tue"><?php echo GoalsHelper::dayToStr(2, true);?></td>
		<td class="week_names" id="wed"><?php echo GoalsHelper::dayToStr(3, true);?></td>
		<td class="week_names" id="thu"><?php echo GoalsHelper::dayToStr(4, true);?></td>
		<td class="week_names" id="fri"><?php echo GoalsHelper::dayToStr(5, true);?></td>
		<td class="week_names" id="sat"><?php echo GoalsHelper::dayToStr(6, true);?></td>
		<td class="week_names" id="sun"><?php echo GoalsHelper::dayToStr(0, true);?></td>
	</tr>
<?php
	$rownum = 0;
	foreach ($calendar as $row) :
	$rownum++;
	?>
<tr>
	<?php foreach ($row as $i=>$day) :?>

		<td id="td<?php echo $rownum.'_'.$i; ?>" class="<?php echo $day['class']; if ($rownum == 1) echo " first_row";if ($i==6) echo " last_col";?>" <?php echo $day['style'];?> <?php echo $day['tip'];?>>
			<div class="cell">
				<?php echo $day['html'] ?>
			</div>
		</td>

	<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</table>
	</div>
		<div class="cn bl"></div>
		<div class="cn br"></div>
	</div>
<script type="text/javascript">
		function addCalendarTips(){
			jQuery(".qtip").each(function(){
			    var tipShow  = true,
                    tipHide	 = {fixed: true, when: {event: 'mouseout'}, effect: {length: 10}};

		    	// Split the title and the content
		    	var title = '',
                    content = jQuery(this).attr('title'),
                    contentArray = content.split('::');

				// Remove the 'title' attributes from the existing .jomTips classes
				jQuery(this).attr('title', '');

				if(contentArray.length == 2) {
					content = contentArray[1];
					title = {text: contentArray[0]} ;
				} else {
                    title = title = {text: ''};
                }

				var widthTooltip = 250,
                    positionTooltip = 'leftTop';
				if((window.innerWidth - this.offsetLeft) < widthTooltip){
                    positionTooltip = 'rightTop';
                    if((this.offsetLeft + this.clientWidth) < widthTooltip){
                        positionTooltip = 'topMiddle';
                    }
                }

		    	jQuery(this).qtip({
		    		content: {
					   text: content
					},
					style: {
						width: widthTooltip,
						padding: 5,
						background: '#eeeeee',
						color: 'black',
						textAlign: 'center',
						border: {
							width: 1,
							radius: 3,
							color: '#C8C8C8'
						}
					},
					position: {
						corner: {
							target: 'bottomCenter',
							tooltip: positionTooltip
						}
					},
					hide: tipHide,
					show: {solo: true, effect: {length: 100, type: 'slide'}}
			 	});
			});

		}

		function refresh_calendar(url) {
			jQuery('table#calendar_table').get(0).style.visibility = 'hidden';
            jQuery('div#mt_loading_cal_indicator').get(0).style.display = '';			
			var flag = 0;
            jQuery('div#mt_calendar').load(url, function(a, status){
				if(!flag) {
					addCalendarTips();
					flag = 1;
				}
			});
		}
		addCalendarTips();
</script>