<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

function showJbField($form, $name='')
{
	echo '<div class="control-label">';
	echo $form->getLabel($name);
	echo '</div><div class="goals-form-datainput controls">';
	echo $form->getInput($name);
	echo '</div>';
}



JHTML::_('behavior.modal', 'a.modal');
$old = false;
if (isset($this->item->id)) $old=true;

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
			$sl_str.='<div id="sl_'.($k+1).'" class="control-group"><label class="control-label">option # '.($k+1).':</label>
						<div class="controls"><div class="input-append"><input type="text" class="text" name="sl_elmts[]" value="'.$sl_el.'"  size="20" />';
			$sl_str.='<a title="Delete option" class="add-on" href="javascript:sl_cf_del(\''.($k+1).'\');">&times;</a></div></div></div>';
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
			$ml_str.='<div id="ms_'.($k+1).'" class="control-group"><label class="control-label">option # '.($k+1).':</label>
						<div class="controls"><div class="input-append"><input type="text" class="text" name="ms_elmts[]" value="'.$ml_el.'"  size="20" />';
			$ml_str.='<a title="Delete option" class="add-on"  href="javascript:ms_cf_del(\''.($k+1).'\');">&times;</a></div></div></div>';
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
			$ch_str.='<div id="ch_'.($k+1).'" class="control-group"><label class="control-label"><input type="checkbox" class="checkbox"  name="ch_'.($k+1).'_sel[]" value="" /></label>
						<div class="controls"><div class="input-append"><input type="text" class="text" name="ch_elmts[]" value="'.$ch_el.'"  size="20" />';
			$ch_str.='<a class="add-on" title="Delete option" href="javascript:ch_cf_del(\''.($k+1).'\');">&times;</a></div></div></div>';
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
			$rb_str.='<div id="rb_'.($k+1).'"><label class="control-label"><input type="radio" class="checkbox" name="rb_sel" value="" /></label>
					<div class="controls"><div class="input-append"><input type="text" class="text" name="rb_elmts[]"  value="'.$rb_el.'" size="20" />';
			$rb_str.='<a class="add-on" title="Delete option" href="javascript:rb_cf_del(\''.($k+1).'\');">&times;</a></div></div></div>';
		}
		$count_rb=count($params->rb_elmts);
	}
}
?>
<div id="goals-wrap">
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
 	var el = new Element('div', {'id':slname, 'class':'control-group'});
	slcfparent.adopt(el);
	$(slname).set('html', '<label class="control-label">option # '+ slid +':</label><div class="controls"><div class="input-append"><input type="text" class="text" name="sl_elmts[]" value=""  size="20" /><a href="javascript:sl_cf_del('+ slid +');" class="add-on" title="Delete option">&times;</a></div></div>');
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
 	var el = new Element('div', {'id':msname, 'class':'control-group'});
	mscfparent.adopt(el);
	$(msname).set('html', '<label class="control-label">option # '+ msid +':</label><div class="controls"><div class="input-append"><input type="text" class="text" name="ms_elmts[]" value=""  size="20" /><a href="javascript:ms_cf_del('+ msid +');" class="add-on" title="Delete option">&times;</a></div></div>');
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
 	var el = new Element('div', {'id':chname, 'class':'control-group'});
	chcfparent.adopt(el);
	$(chname).set('html', '<label class="control-label"><input type="checkbox" class="checkbox" name="ch_'+ chid +'_sel[]" value="" /></label><div class="controls"><div class="input-append"><input type="text" class="text" name="ch_elmts[]" value=""  size="20" /><a class="add-on" href="javascript:ch_cf_del('+ chid +');" title="Delete option">&times;</a></div></div>');
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
 	var el = new Element('div', {'id':rbname, 'class':'control-group'});
	rbcfparent.adopt(el);
	$(rbname).set('html', '<label class="control-label"><input type="radio" class="checkbox" name="rb_sel" value="" /></label><div class="controls"><div class="input-append"><input type="text" class="text" name="rb_elmts[]" value="" size="20" /><a class="add-on" href="javascript:rb_cf_del('+ rbid +');" title="Delete option"> &times;</a></div>');
 }

 function rb_cf_del(el)
 {
 	if (rbid==el) rbid--;
 	var el = 'rb_'+el;
 	$(el).destroy();
 }
</script>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'userfield.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php //echo $this->form->getField('description')->save();
			?>
			Joomla.submitform(task);
		}
			else {
				if (!$('jform_title').get('value')) {alert('<?php echo JText::sprintf('COM_GOALS_ERROR_NOT_TITLE','field'); ?>');$('jform_title').focus();}
				else
				if (!$('jform_type').get('value')) {alert('<?php echo JText::_('COM_GOALS_ERROR_SELECT_TYPE'); ?>');$('jform_type').focus();}
			}
	}
</script>


<form action="<?php echo JRoute::_('index.php?option=com_goals&view=editfield&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
<div class="gl_dashboard">
    <h2><?php echo ($old)?JText::_('COM_GOALS_FIELD_EDIT_FORM_TITLE_EDIT'):JText::_('COM_GOALS_FIELD_EDIT_FORM_TITLE_NEW'); ?></h2>
    <div class="gl_goals">
        <div class="control-group"><?php showJbField($this->form,'title'); ?></div>
        <div class="control-group"><?php showJbField($this->form,'type'); ?></div>
            
            
            
					<div id="custom_fields">
						<div id="tf_cf" class="<?php echo (isset($item->type)?($item->type=='tf'?'nn':'cf_hiden'):'cf_hiden')?>">
							<fieldset class="adminform" >
								<legend><?php echo JText::_('COM_GOALS_FIELDS_OPTION_TFS');?>:</legend>                      
									<div class="control-group">
										<label class="control-label"><?php echo JText::_('COM_GOALS_FIELDS_OPTION_TFS_DV');?>:</label>
										<div class="controls"><input type="text" class="text" name="tf_default" value="<?php echo (isset($params->tf_default))?$params->tf_default:''?>" size="20" /></div>
									</div>
									<div class="control-group">
										<label class="control-label"><?php echo JText::_('COM_GOALS_FIELDS_OPTION_TFS_ML');?>:</label>
										<div class="controls"><input type="text" class="text" name="tf_max" value="<?php echo (isset($params->tf_max))?$params->tf_max:'70'?>"  size="3" maxlength="3" /></div>
									</div>
							</fieldset>
						</div>

						<div id="ta_cf" class="<?php echo (isset($item->type)?($item->type=='ta'?'nn':'cf_hiden'):'cf_hiden')?>">
							<fieldset class="adminform" >
								<legend><?php echo JText::_('COM_GOALS_FIELDS_OPTION_TAS');?>:</legend>

									<div class="control-group">
										<label class="control-label"><?php echo JText::_('COM_GOALS_FIELDS_OPTION_TAS_R');?>:</label>
										<div class="controls"><input type="text" class="text" name="ta_rows" value="<?php echo (isset($params->ta_rows))?$params->ta_rows:'20'?>"  size="3" maxlength="3" /></div>
									</div>
									<div class="control-group">
										<label class="control-label"><?php echo JText::_('COM_GOALS_FIELDS_OPTION_TAS_C');?>:</label>
										<div class="controls"><input type="text" class="text" name="ta_colls" value="<?php echo (isset($params->ta_colls))?$params->ta_colls:'20'?>" size="3" maxlength="3" /></div>
									</div>

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
								<div class="adminformlist" id="sl_cf_list" style="min-width:20px !important;" >
									<?php
										if ($sl_str) echo $sl_str;
										else
										{
									?>
											<div class="control-group">
                                                <label class="control-label"><?php echo JText::_('COM_GOALS_FIELDS_OPTION_SLS_O');?> <?php echo (isset($item->type)?($item->type=='sl'?$count_sl:'1'):'1')?>:</label>
                                                <div class="controls">
                                                    <input type="text" class="text" name="sl_elmts[]" value=""  size="20" />
                                                </div>
											</div>
									<?php
										}
									?>
								</div>
								<div class="control-group">
                                    <div class="controls">
                                        <a href="javascript:sl_cf_new();" class="btn"><img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/add.png" /> <?php echo JText::_('COM_GOALS_FIELDS_OPTION_SLS_NO');?></a>
                                    </div>
                                </div>

							</fieldset>
						</div>
						<div id="ml_cf" class="<?php echo (isset($item->type)?($item->type=='ml'?'nn':'cf_hiden'):'cf_hiden')?>">
							<fieldset class="adminform" >
								<legend><?php echo JText::_('COM_GOALS_FIELDS_OPTION_MSLS');?>:</legend>
								<div class="adminformlist" id="ms_cf_list" style="min-width:20px !important;" >
									<?php
										if ($ml_str) echo $ml_str;
										else
										{
									?>
									<div class="control-group">
                                        <label class="control-label"><?php echo JText::_('COM_GOALS_FIELDS_OPTION_SLS_O');?> 1:</label>
                                        <div class="controls">
                                            <input type="text" class="text" name="ms_elmts[]" value=""  size="20" />
                                        </div>
									</div>
									<?php
										}
									?>
								</div>
								<div class="control-group">
                                    <div class="controls">
                                        <a href="javascript:ms_cf_new();" class="btn"><img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/add.png" /> <?php echo JText::_('COM_GOALS_FIELDS_OPTION_SLS_NO');?></a>
                                    </div>
                                </div>
							</fieldset>
						</div>
						<div id="ch_cf" class="<?php echo (isset($item->type)?($item->type=='ch'?'nn':'cf_hiden'):'cf_hiden')?>">
							<fieldset class="adminform" >
								<legend><?php echo JText::_('COM_GOALS_FIELDS_OPTION_CFS');?>:</legend>
								<div class="adminformlist" id="ch_cf_list" style="min-width:20px !important;" >
									<?php
										if ($ch_str) echo $ch_str;
										else
										{
									?>
										<div class="control-group">
                                            <label class="control-label"><input type="checkbox" class="checkbox" name="ch_1_sel[]" value="" /></label>
                                            <div class="controls">
												<input type="text" class="text" name="ch_elmts[]" value=""  size="20" />
											</div>
										</div>                                
									<?php
										}
									?>
								</div>
								<div class="control-group">
                                    <div class="controls">
                                        <a href="javascript:ch_cf_new();" class="btn"><img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/add.png" /> <?php echo JText::_('COM_GOALS_FIELDS_OPTION_RFS_NC');?></a>
                                    </div>
                                </div>
							</fieldset>
						</div>
						<div id="rd_cf" class="<?php echo (isset($item->type)?($item->type=='rd'?'nn':'cf_hiden'):'cf_hiden')?>">
							<fieldset class="adminform" >
								<legend><?php echo JText::_('COM_GOALS_FIELDS_OPTION_RFS');?>:</legend>
								<div class="adminformlist" id="rb_cf_list" style="min-width:20px !important;" >
									<?php
										if ($rb_str) echo $rb_str;
										else
										{
									?>
										<div class="control-group">
                                            <label class="control-label"><input type="radio" class="checkbox" name="rb_sel" value="" /></label>
											<div class="controls">
                                                <input type="text" class="text" name="rb_elmts[]" value=""  size="20" />
                                            </div>
										</div>
									<?php
										}
									?>
								</div>

								<div class="control-group">
                                    <div class="controls">
                                        <a href="javascript:rb_cf_new();" class="btn"><img src="<?php echo JURI::root();?>administrator/components/com_goals/assets/images/add.png" /> <?php echo JText::_('COM_GOALS_FIELDS_OPTION_RFS_NR');?></a>
                                    </div>
                                </div>
							</fieldset>
						</div>
					</div>
                    <div class="control-group">
                        <div class="controls">
                            <input type="button" class="btn" onclick="Joomla.submitbutton('userfield.save')" value="<?php echo JText::_('JSAVE') ?>" />
                            <input type="button" class="btn" onclick="Joomla.submitbutton('userfield.cancel')" value="<?php echo JText::_('JCANCEL') ?>" />
                        </div>
                    </div>
    </div>
	<input type="hidden" name="task" value="field.edit" />
	<input type="hidden" name="id" value="<?php echo ($old)?$this->item->id:0; ?>" />
	<input type="hidden" name="gid" value="<?php echo (JRequest::getInt('gid'))?JRequest::getInt('gid'):0; ?>" />
	<?php $tmpl = JRequest::getVar('tmpl'); if ($tmpl=='component') echo'<input type="hidden" name="tmpl" value="component" />';?>
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>
</div><!-- end #goals-wrap -->