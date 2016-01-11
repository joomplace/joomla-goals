<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$cf_array=array();
	$cf_array[] = 'tf'; 
	$cf_array[] = 'ta'; 
	$cf_array[] = 'hc'; 
	$cf_array[] = 'em'; 
	$cf_array[] = 'wu'; 
	$cf_array[] = 'pc'; 
	$cf_array[] = 'in'; 
	$cf_array[] = 'sl'; 
	$cf_array[] = 'ml'; 
	$cf_array[] = 'ch'; 
	$cf_array[] = 'rd';
	
$item 	= $this->item; 
$params = json_decode($item->values);

//sl
$count_sl=1;
$sl_str='';
if (isset($params->sl_elmts))
{
	if (count($params->sl_elmts))
	{
		foreach ( $params->sl_elmts as $k=>$sl_el ) 
		{
			$sl_str.='<li id="sl_'.($k+1).'"><label>option # '.($k+1).':</label>
						<input type="text" class="text" name="sl_elmts[]" value="'.$sl_el.'"  size="20" />';
			$sl_str.='<div><a href="javascript:sl_cf_del(\''.($k+1).'\');"><img src="'.JURI::root().'administrator/components/com_goals/assets/images/publish_x.png" title="Delete option" /> </a></div></li>';
		}
		$count_sl=count($params->sl_elmts);
	}
}

//ml
$count_ml=1;
$ml_str='';
if (isset($params->ms_elmts))
{
	if (count($params->ms_elmts))
	{
		foreach ( $params->ms_elmts as $k=>$ml_el ) 
		{
			$ml_str.='<li id="ms_'.($k+1).'"><label>option # '.($k+1).':</label>
						<input type="text" class="text" name="ms_elmts[]" value="'.$ml_el.'"  size="20" />';
			$ml_str.='<div><a href="javascript:ms_cf_del(\''.($k+1).'\');"><img src="'.JURI::root().'administrator/components/com_goals/assets/images/publish_x.png" title="Delete option" /> </a></div></li>';
		}
		$count_ml=count($params->ms_elmts);
	}
}

//ch
$count_ch=1;
$ch_str='';
if (isset($params->ch_elmts))
{
	if (count($params->ch_elmts))
	{
		foreach ( $params->ch_elmts as $k=>$ch_el ) 
		{
			$ch_str.='<li id="ch_'.($k+1).'"><label><input type="checkbox" class="checkbox"  name="ch_'.($k+1).'_sel[]" value="" />
						</label><input type="text" class="text" name="ch_elmts[]" value="'.$ch_el.'"  size="20" />';
			$ch_str.='<div><a href="javascript:ch_cf_del(\''.($k+1).'\');"><img src="'.JURI::root().'administrator/components/com_goals/assets/images/publish_x.png" title="Delete option" /> </a></div></li>';
		}
		$count_ch=count($params->ch_elmts);
	}
}

//rb

$count_rb=1;
$rb_str='';
if (isset($params->rb_elmts))
{
	if (count($params->rb_elmts))
	{
		foreach ( $params->rb_elmts as $k=>$rb_el ) 
		{
			$rb_str.='<li id="rb_'.($k+1).'"><label><input type="radio" class="checkbox" name="rb_sel" value="" /></label>
					<input type="text" class="text" name="rb_elmts[]"  value="'.$rb_el.'" size="20" />';
			$rb_str.='<div><a href="javascript:rb_cf_del(\''.($k+1).'\');"><img src="'.JURI::root().'administrator/components/com_goals/assets/images/publish_x.png" title="Delete option" /> </a></div></li>';
		}
		$count_rb=count($params->rb_elmts);
	}
}							
?>
<script type="text/javascript">
var slid=<?php echo (isset($item->type)?($item->type=='sl'?$count_sl:'1'):'1')?>;
var msid=<?php echo (isset($item->type)?($item->type=='ml'?$count_ml:'1'):'1')?>;
var chid=<?php echo (isset($item->type)?($item->type=='ch'?$count_ch:'1'):'1')?>;
var rbid=<?php echo (isset($item->type)?($item->type=='rd'?$count_rb:'1'):'1')?>;
function change_custom_type(el)
{
	hideallcf();
	if (!el) return false;
	switch ( el ) {
		<?php 
				foreach ( $cf_array as $cf ) 
				{
					echo "case '".$cf."':	{\$('".$cf."_cf').show();} break;"."\n";
				}
		?>
		default: hideallcf(); break;
	}
}

 function hideallcf()
 {
 	<?php 
		foreach ( $cf_array as $cf ) 
		{
			echo "$('".$cf."_cf').hide();"."\n";
		}
 	?>
 }
 
 function sl_cf_new()
 {
 	slid++;
 	var slcfparent = $('sl_cf_list');
 	var slname = 'sl_'+slid;
 	var el = new Element('li', {'id':slname});
	slcfparent.adopt(el);
	$(slname).set('html', '<label>option # '+ slid +':</label><input type="text" class="text" name="sl_elmts[]" value=""  size="20" /><div><a href="javascript:sl_cf_del('+ slid +');"><img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/publish_x.png" title="Delete option" /> </a></div>');
 }
 
 function sl_cf_del(el)
 {
 	if (slid==el) slid--;
 	var el = 'sl_'+el;
 	$(el).destroy();
 }
 
  function ms_cf_new()
 {
 	msid++;
 	var mscfparent = $('ms_cf_list');
 	var msname = 'ms_'+msid;
 	var el = new Element('li', {'id':msname});
	mscfparent.adopt(el);
	$(msname).set('html', '<label>option # '+ msid +':</label><input type="text" class="text" name="ms_elmts[]" value=""  size="20" /><div><a href="javascript:ms_cf_del('+ msid +');"><img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/publish_x.png" title="Delete option" /> </a></div>');
 }
 
 function ms_cf_del(el)
 {
 	if (msid==el) msid--;
 	var el = 'ms_'+el;
 	$(el).destroy();
 }
 
 function ch_cf_new()
 {
 	chid++;
 	var chcfparent = $('ch_cf_list');
 	var chname = 'ch_'+chid;
 	var el = new Element('li', {'id':chname});
	chcfparent.adopt(el);
	$(chname).set('html', '<label><input type="checkbox" class="checkbox" name="ch_'+ chid +'_sel[]" value="" /></label><input type="text" class="text" name="ch_elmts[]" value=""  size="20" /><div><a href="javascript:ch_cf_del('+ chid +');"><img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/publish_x.png" title="Delete option" /> </a></div>');
 }
 
 function ch_cf_del(el)
 {
 	if (chid==el) chid--;
 	var el = 'ch_'+el;
 	$(el).destroy();
 }
 
  function rb_cf_new()
 {
 	rbid++;
 	var rbcfparent = $('rb_cf_list');
 	var rbname = 'rb_'+rbid;
 	var el = new Element('li', {'id':rbname});
	rbcfparent.adopt(el);
	$(rbname).set('html', '<label><input type="radio" class="checkbox" name="rb_sel" value="" /></label><input type="text" class="text" name="rb_elmts[]" value=""  size="20" /><div><a href="javascript:rb_cf_del('+ rbid +');"><img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/publish_x.png" title="Delete option" /> </a></div>');
 }
 
 function rb_cf_del(el)
 {
 	if (rbid==el) rbid--;
 	var el = 'rb_'+el;
 	$(el).destroy();
 }
 
 	Joomla.submitbutton = function(task)
	{
		if (task == 'planuserfield.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			if (!$('jform_title').get('value')) {alert('<?php echo JText::sprintf('COM_GOALS_ERROR_NOT_TITLE','user field'); ?>');$('jform_title').focus();}
			else
			if (!$('jform_user_id').get('value')) {alert('<?php echo JText::sprintf('COM_GOALS_ERROR_SELECT_USER'); ?>');$('jform_user_id').focus();}
			else
			if (!$('jform_type').get('value')) {alert('<?php echo JText::sprintf('COM_GOALS_ERROR_SELECT_TYPE'); ?>');$('jform_type').focus();}
						
		}
	}
</script>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_goals&view=planuserfield&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate form-horizontal" >
	<div class="row-fluid">
	<!-- Begin Content -->
		<div class="span10 form-horizontal">
			<ul class="nav nav-tabs">
				<?php $i=1;
				foreach ($this->form->getFieldsets() as $fieldset) {
				 echo ($i==1)?'<li class="active"><a href="#'.$fieldset->name.'" data-toggle="tab">'.JText::_($fieldset->label).'</a></li>':'<li><a href="#'.$fieldset->name.'" data-toggle="tab">'.JText::_($fieldset->label).'</a></li>'; 
				 $i++;
				} ?>
			</ul>				
					<div class="tab-content">
					<?php $j=1;
					foreach ($this->form->getFieldsets() as $fieldset) {
						$fields = $this->form->getFieldset($fieldset->name);
						
						// Begin Tabs 
						echo ($j==1)?'<div class="tab-pane active" id="'.$fieldset->name.'">':'<div class="tab-pane" id="'.$fieldset->name.'">';
								foreach($this->form->getFieldset($fieldset->name) as $field) {
									echo ($field->hidden == 1)? $field->input: '<div class="control-group"><div class="control-label">'.$field->label.'</div><div class="controls">'.$field->input.'</div></div>'; 
								}
						$j++;	

						// End tab details 
					    echo '</div>';
						}?>
						<div id="custom_fields">
						<div id="tf_cf" class="<?php echo (isset($item->type)?($item->type=='tf'?'nn':'cf_hiden'):'cf_hiden')?>">
							<fieldset class="adminform" >
								<legend><?php echo JText::_('COM_GOALS_FIELDS_OPTION_TFS');?>:</legend>
								<ul class="adminformlist">
									<li>
										<label><?php echo JText::_('COM_GOALS_FIELDS_OPTION_TFS_DV');?>:</label>
										<input type="text" class="text" name="tf_default" value="<?php echo (isset($params->tf_default))?$params->tf_default:''?>" size="20" />
									</li>
									<li>
										<label><?php echo JText::_('COM_GOALS_FIELDS_OPTION_TFS_ML');?>:</label>
										<input type="text" class="text" name="tf_max" value="<?php echo (isset($params->tf_max))?$params->tf_max:'70'?>"  size="3" maxlength="3" />
									</li>
								</ul>
							</fieldset>	
						</div>
				
						<div id="ta_cf" class="<?php echo (isset($item->type)?($item->type=='ta'?'nn':'cf_hiden'):'cf_hiden')?>">
							<fieldset class="adminform" >
								<legend><?php echo JText::_('COM_GOALS_FIELDS_OPTION_TAS');?>:</legend>
								<ul class="adminformlist">
									<li>
										<label><?php echo JText::_('COM_GOALS_FIELDS_OPTION_TAS_R');?>:</label>
										<input type="text" class="text" name="ta_rows" value="<?php echo (isset($params->ta_rows))?$params->ta_rows:'20'?>"  size="3" maxlength="3" />
									</li>
									<li>
										<label><?php echo JText::_('COM_GOALS_FIELDS_OPTION_TAS_C');?>:</label>
										<input type="text" class="text" name="ta_colls" value="<?php echo (isset($params->ta_colls))?$params->ta_colls:'20'?>" size="3" maxlength="3" />
									</li>
								</ul>
							</fieldset>	
						</div>
						<div id="hc_cf" class="cf_hiden">
						
						</div>
						<div id="em_cf" class="cf_hiden">
						
						</div>
						<div id="wu_cf" class="cf_hiden">
						
						</div>
						<div id="pc_cf" class="cf_hiden">
						
						</div>
						<div id="in_cf" class="cf_hiden">
						
						</div>
						<div id="sl_cf" class="<?php echo (isset($item->type)?($item->type=='sl'?'nn':'cf_hiden'):'cf_hiden')?>">
							<fieldset class="adminform" >
								<legend><?php echo JText::_('COM_GOALS_FIELDS_OPTION_SLS');?>:</legend>
								<ul class="adminformlist" id="sl_cf_list" style="min-width:20px !important;" >
									<?php
										if ($sl_str) echo $sl_str;
										else
										{
									?>
											<li><label><?php echo JText::_('COM_GOALS_FIELDS_OPTION_SLS_O');?><?php echo (isset($item->type)?($item->type=='sl'?$count_sl:'1'):'1')?>:</label>
												<input type="text" class="text" name="sl_elmts[]" value=""  size="20" />
											</li>
									<?php
										}
									?>																	
								</ul>
								<br class="clr" />
								<div><a href="javascript:sl_cf_new();"><img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/add.png" /> <?php echo JText::_('COM_GOALS_FIELDS_OPTION_SLS_NO');?></a></div>
								
							</fieldset>								
						</div>
						<div id="ml_cf" class="<?php echo (isset($item->type)?($item->type=='ml'?'nn':'cf_hiden'):'cf_hiden')?>">
							<fieldset class="adminform" >
								<legend><?php echo JText::_('COM_GOALS_FIELDS_OPTION_MSLS');?>:</legend>
								<ul class="adminformlist" id="ms_cf_list" style="min-width:20px !important;" >
									<?php
										if ($ml_str) echo $ml_str;
										else
										{
									?>
									<li><label><?php echo JText::_('COM_GOALS_FIELDS_OPTION_SLS_O');?> 1:</label>
										<input type="text" class="text" name="ms_elmts[]" value=""  size="20" />
									</li>
									<?php
										}
									?>										
								</ul>
								<br class="clr" />
								<div><a href="javascript:ms_cf_new();"><img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/add.png" /> <?php echo JText::_('COM_GOALS_FIELDS_OPTION_SLS_NO');?></a></div>
								
							</fieldset>	
						</div>
						<div id="ch_cf" class="<?php echo (isset($item->type)?($item->type=='ch'?'nn':'cf_hiden'):'cf_hiden')?>">
							<fieldset class="adminform" >
								<legend><?php echo JText::_('COM_GOALS_FIELDS_OPTION_CFS');?>:</legend>
								<ul class="adminformlist" id="ch_cf_list" style="min-width:20px !important;" >
									<?php
										if ($ch_str) echo $ch_str;
										else
										{
									?>
										<li><label><input type="checkbox" class="checkbox" name="ch_1_sel[]" value="" /></label>
											<input type="text" class="text" name="ch_elmts[]" value=""  size="20" />
										</li>
									<?php
										}
									?>										
								</ul>
								<br class="clr" />
								<div><a href="javascript:ch_cf_new();"><img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/add.png" /> <?php echo JText::_('COM_GOALS_FIELDS_OPTION_RFS_NC');?></a></div>
							</fieldset>	
						</div>
						<div id="rd_cf" class="<?php echo (isset($item->type)?($item->type=='rd'?'nn':'cf_hiden'):'cf_hiden')?>">
							<fieldset class="adminform" >
								<legend><?php echo JText::_('COM_GOALS_FIELDS_OPTION_RFS');?>:</legend>
								<ul class="adminformlist" id="rb_cf_list" style="min-width:20px !important;" >
									<?php
										if ($rb_str) echo $rb_str;
										else
										{
									?>
										<li><label><input type="radio" class="checkbox" name="rb_sel" value="" /></label>
											<input type="text" class="text" name="rb_elmts[]" value=""  size="20" />
										</li>	
									<?php
										}
									?>								
								</ul>
								<br class="clr" />
								<div><a href="javascript:rb_cf_new();"><img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/add.png" /> <?php echo JText::_('COM_GOALS_FIELDS_OPTION_RFS_NR');?></a></div>
							</fieldset>	
						</div>						
					</div>
				</div>	

		</div>
<div>						
<input type="hidden" name="task" value="item.edit" />
<?php echo JHtml::_('form.token'); ?>
</div>