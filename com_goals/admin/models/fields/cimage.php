<?php
/**
 * Goals component for Joomla 3.0
 * @package Goals
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldCImage extends JFormFieldMedia
{
    public $type = 'cimage';

    // to compleced, but may be cool to use  https://github.com/baamenabar/jQuery-File-Upload-and-Crop

/*
    protected function getInput()
    {
        /*

        if (!self::$initialised)
        {
            // Load the modal behavior script.
            JHtml::_('behavior.modal');

            // Include jQuery
            JHtml::_('jquery.framework');

            // Build the script.
            $script = array();
            $script[] = '	function jInsertFieldValue(value, id) {';
            $script[] = '		var $ = jQuery.noConflict();';
            $script[] = '		var old_value = $("#" + id).val();';
            $script[] = '		if (old_value != value) {';
            $script[] = '			var $elem = $("#" + id);';
            $script[] = '			$elem.val(value);';
            $script[] = '			$elem.trigger("change");';
            $script[] = '			if (typeof($elem.get(0).onchange) === "function") {';
            $script[] = '				$elem.get(0).onchange();';
            $script[] = '			}';
            $script[] = '			jMediaRefreshPreview(id);';
            $script[] = '		}';
            $script[] = '	}';

            $script[] = '	function jMediaRefreshPreview(id) {';
            $script[] = '		var $ = jQuery.noConflict();';
            $script[] = '		var value = $("#" + id).val();';
            $script[] = '		var $img = $("#" + id + "_preview");';
            $script[] = '		if ($img.length) {';
            $script[] = '			if (value) {';
            $script[] = '				$img.attr("src", "' . JUri::root() . '" + value);';
            $script[] = '				$("#" + id + "_preview_empty").hide();';
            $script[] = '				$("#" + id + "_preview_img").show()';
            $script[] = '			} else { ';
            $script[] = '				$img.attr("src", "")';
            $script[] = '				$("#" + id + "_preview_empty").show();';
            $script[] = '				$("#" + id + "_preview_img").hide();';
            $script[] = '			} ';
            $script[] = '		} ';
            $script[] = '	}';

            $script[] = '	function jMediaRefreshPreviewTip(tip)';
            $script[] = '	{';
            $script[] = '		var $ = jQuery.noConflict();';
            $script[] = '		var $tip = $(tip);';
            $script[] = '		var $img = $tip.find("img.media-preview");';
            $script[] = '		$tip.find("div.tip").css("max-width", "none");';
            $script[] = '		var id = $img.attr("id");';
            $script[] = '		id = id.substring(0, id.length - "_preview".length);';
            $script[] = '		jMediaRefreshPreview(id);';
            $script[] = '		$tip.show();';
            $script[] = '	}';

            // Add the script to the document head.
            JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

            self::$initialised = true;
        }

        $html = array();
        $attr = '';

        // Initialize some field attributes.
        $attr .= !empty($this->class) ? ' class="input-small ' . $this->class . '"' : ' class="input-small"';
        $attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';

        // Initialize JavaScript field attributes.
        $attr .= !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

        // The text field.
        $html[] = '<div class="input-prepend input-append">';

        // The Preview.
        $showPreview = true;
        $showAsTooltip = false;

        switch ($this->preview)
        {
            case 'no': // Deprecated parameter value
            case 'false':
            case 'none':
                $showPreview = false;
                break;

            case 'yes': // Deprecated parameter value
            case 'true':
            case 'show':
                break;

            case 'tooltip':
            default:
                $showAsTooltip = true;
                $options = array(
                    'onShow' => 'jMediaRefreshPreviewTip',
                );
                JHtml::_('behavior.tooltip', '.hasTipPreview', $options);
                break;
        }

        if ($showPreview)
        {
            if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
            {
                $src = JUri::root() . $this->value;
            }
            else
            {
                $src = '';
            }

            $width = $this->previewWidth;
            $height = $this->previewHeight;
            $style = '';
            $style .= ($width > 0) ? 'max-width:' . $width . 'px;' : '';
            $style .= ($height > 0) ? 'max-height:' . $height . 'px;' : '';

            $imgattr = array(
                'id' => $this->id . '_preview',
                'class' => 'media-preview',
                'style' => $style,
            );

            $img = JHtml::image($src, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $imgattr);
            $previewImg = '<div id="' . $this->id . '_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
            $previewImgEmpty = '<div id="' . $this->id . '_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
                . JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '</div>';

            if ($showAsTooltip)
            {
                $html[] = '<div class="media-preview add-on">';
                $tooltip = $previewImgEmpty . $previewImg;
                $options = array(
                    'title' => JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'),
                    'text' => '<i class="icon-eye"></i>',
                    'class' => 'hasTipPreview'
                );

                $html[] = JHtml::tooltip($tooltip, $options);
                $html[] = '</div>';
            }
            else
            {
                $html[] = '<div class="media-preview add-on" style="height:auto">';
                $html[] = ' ' . $previewImgEmpty;
                $html[] = ' ' . $previewImg;
                $html[] = '</div>';
            }
        }

        $html[] = '	<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="'
            . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" readonly="readonly"' . $attr . ' />';

        if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
        {
            $folder = explode('/', $this->value);
            $folder = array_diff_assoc($folder, explode('/', JComponentHelper::getParams('com_media')->get('image_path', 'images')));
            array_pop($folder);
            $folder = implode('/', $folder);
        }
        elseif (file_exists(JPATH_ROOT . '/' . JComponentHelper::getParams('com_media')->get('image_path', 'images') . '/' . $this->directory))
        {
            $folder = $this->directory;
        }
        else
        {
            $folder = '';
        }

        // The button.
        if ($this->disabled != true)
        {
            JHtml::_('bootstrap.tooltip');

            $html[] = '<a class="modal btn" title="' . JText::_('JLIB_FORM_BUTTON_SELECT') . '" href="'
                . ($this->readonly ? ''
                    : ($this->link ? $this->link
                        : 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=' . $asset . '&amp;author='
                            . $this->form->getValue($this->authorField)) . '&amp;fieldid=' . $this->id . '&amp;folder=' . $folder) . '"'
                . ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
            $html[] = JText::_('JLIB_FORM_BUTTON_SELECT') . '</a><a class="btn hasTooltip" title="' . JText::_('JLIB_FORM_BUTTON_CLEAR') . '" href="#" onclick="';
            $html[] = 'jInsertFieldValue(\'\', \'' . $this->id . '\');';
            $html[] = 'return false;';
            $html[] = '">';
            $html[] = '<i class="icon-remove"></i></a>';
        }

        $html[] = '</div>';

        return implode("\n", $html);
        */

    /*
            $asset = $this->asset;

            if ($asset == '')
            {
                $asset = JFactory::getApplication()->input->get('option');
            }

            if (!self::$initialised)
            {
                // Load the modal behavior script.
                JHtml::_('behavior.modal');

                // Include jQuery
                JHtml::_('jquery.framework');

                // Build the script.
                $script = array();
    /*
                $script[] = '{% for (var i=0, file; file=o.files[i]; i++) { %}';
                $script[] = '<tr class="template-upload fade">';
                $script[] = '<td class="preview"><span class="fade"></span></td>';
                $script[] = '<td class="name"><span>{%=file.name%}</span></td>';
                $script[] = '<td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>';
                $script[] = '{% if (file.error) { %}';
                $script[] = '<td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>';
                $script[] = '{% } else if (o.files.valid && !i) { %}';
                $script[] = '<td>';
                $script[] = '<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>';
                $script[] = '</td>';
                $script[] = '<td class="start">{% if (!o.options.autoUpload) { %}';
                $script[] = '<button class="btn btn-primary">';
                $script[] = '<i class="icon-upload icon-white"></i>';
                $script[] = '<span>{%=locale.fileupload.start%}</span>';
                $script[] = '</button>';
                $script[] = '{% } %}</td>';
                $script[] = '{% } else { %}';
                $script[] = '<td colspan="2"></td>';
                $script[] = '{% } %}';
                $script[] = '<td class="cancel">{% if (!i) { %}';
                $script[] = '<button class="btn btn-warning">';
                $script[] = '<i class="icon-ban-circle icon-white"></i>';
                $script[] = '<span>{%=locale.fileupload.cancel%}</span>';
                $script[] = '</button>';
                $script[] = '{% } %}</td>';
                $script[] = '</tr>';
                $script[] = '{% } %}';




                $script[] = '{% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-download fade">
            {% if (file.error) { %}
                <td></td>
                <td class="name"><span>{%=file.name%}</span></td>
                <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
                <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
            {% } else { %}
                <td class="preview">{% if (file.thumbnail_url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
                {% } %}</td>
                <td class="name">
                    <a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&\'gallery\'%}" download="{%=file.name%}">{%=file.name%}</a>
                </td>
                <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
                <td colspan="2"></td>
            {% } %}
            <td class="delete">
                <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
                    <i class="icon-trash icon-white"></i>
                    <span>{%=locale.fileupload.destroy%}</span>
                </button>
                <input type="checkbox" name="delete" value="1">
            </td>
        </tr>
    {% } %}';
    */
    /*
                $css_path = JUri::base() .'components'. DIRECTORY_SEPARATOR .$asset. DIRECTORY_SEPARATOR .'assets'. DIRECTORY_SEPARATOR .'css'. DIRECTORY_SEPARATOR .'uploader'. DIRECTORY_SEPARATOR;
                $js_path = JUri::base() .'components'. DIRECTORY_SEPARATOR .$asset. DIRECTORY_SEPARATOR .'assets'. DIRECTORY_SEPARATOR .'js'. DIRECTORY_SEPARATOR .'uploader'. DIRECTORY_SEPARATOR;
                JFactory::getDocument()->addScript( $js_path.'vendor'. DIRECTORY_SEPARATOR .'jquery.ui.widget.js');
                JFactory::getDocument()->addScript( $js_path.'tmpl.min.js');
                JFactory::getDocument()->addScript( $js_path.'load-image.min.js');
                JFactory::getDocument()->addScript( $js_path.'canvas-to-blob.min.js');
                JFactory::getDocument()->addScript( $js_path.'jquery.iframe-transport.js');
                JFactory::getDocument()->addScript( $js_path.'jquery.fileupload.js');
                JFactory::getDocument()->addScript( $js_path.'jquery.fileupload-fp.js');
                JFactory::getDocument()->addScript( $js_path.'jquery.fileupload-ui.js');
                JFactory::getDocument()->addScript( $js_path.'locale.js');
                JFactory::getDocument()->addScript( $js_path.'jquery.zclip.js');
                JFactory::getDocument()->addScript( $js_path.'jquery.Jcrop.min.js');
                JFactory::getDocument()->addScript( $js_path.'main.js');
                JFactory::getDocument()->addStyleSheet( $css_path.'style.css');
                JFactory::getDocument()->addStyleSheet( $css_path.'jquery.Jcrop.css');
                JFactory::getDocument()->addStyleSheet( $css_path.'jquery.fileupload-ui.css');
                JFactory::getDocument()->addStyleSheet( $css_path.'jquery.furac.ui.css');
                /*
    <!-- Bootstrap JS and Bootstrap Image Gallery are not required, but included for the demo -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-image-gallery.min.js"></script>
    */
    /*
                // Add the script to the document head.
                JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

                self::$initialised = true;
            }









            $html=array();
            $html[]= '<div class="row-fluid fileupload-buttonbar">';

            $html[]= '<div class="span7">';
            $html[]= '<span class="btn btn-success fileinput-button">';
            $html[]= '<i class="icon-plus icon-white"></i>';
            $html[]= '<span>Add files...</span>';
            $html[]= '<input type="file" name="files[]" multiple>';
            $html[]= '</span>';
            $html[]= '</div>';

            $html[]= '<!-- The global progress information -->';
            $html[]= '<div class="span5 fileupload-progress fade">';
            $html[]= '<!-- The global progress bar -->';
            $html[]= '<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">';
            $html[]= '<div class="bar" style="width:0%;"></div>';
            $html[]= '</div>';
            $html[]= '<!-- The extended global progress information -->';
            $html[]= '<div class="progress-extended">&nbsp;</div>';
            $html[]= '</div>';

            $html[]= '</div>';

            $html[]= '<div class="row-fluid">';

            $html[]= '<!-- The loading indicator is shown during file processing -->';
            $html[]= '<div class="fileupload-loading"></div>';
            $html[]= '<br>';
            $html[]= '<!-- The table listing the files available for upload/download -->';
            $html[]= '<table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>';

            $html[]= '</div>';

            return implode("\n", $html);
        }
    */
}