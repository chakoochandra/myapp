<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('switch_input')) {
	function switch_input($type, $field)
	{
		switch ($type) {
			case 'form_hidden':
				return form_hidden($field);
			case 'form_toggler':
				return form_toggler($field);
			case 'form_switcher':
				return form_switcher($field);
			case 'form_separator':
				return '<hr>';
			case 'form_info':
				$title = isset($field['title']) ? "<h6 class='font-weight-bold'><i class='fa fa-" . (isset($field['icon']) ? $field['icon'] : 'info-circle') . "' aria-hidden='true'></i> {$field['title']}</h6>" : '';
				$message = isset($field['info']) ? $field['info'] : '';
				$class = isset($field['textClass']) ? $field['textClass'] : 'small';
				return warning_message($title . $message, 'warning', $class);
			case 'form_checkbox':
				$html = '';
				if (isset($field['data']) && is_array($field['data'])) {
					$html .= '<div class="my-1">';
					foreach ($field['data'] as $key => $data) {
						$html .= '<div class="form-check">';
						if (is_array($data)) {
							$html .= form_checkbox($field['name'] . '[]', $data['id'], isset($field['selected']) ? in_array($data['id'], $field['selected']) : false);
							$html .= '<label class="form-check-label">' . htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8') . '</label>';
						} else {
							$html .= form_checkbox($field['name'] . '[]', $key, isset($field['selected']) ? in_array($key, $field['selected']) : false);
							$html .= '<label class="form-check-label">' . htmlspecialchars($data, ENT_QUOTES, 'UTF-8') . '</label>';
						}
						$html .= '</div>';
					}
					$html .= '</div>';
				}
				return $html;
			case 'form_camera':
				if ($field['value'] && file_exists($field['value'])) {
					$field['value'] = 'data:' . mime_content_type($field['value']) . ';base64,' . base64_encode(file_get_contents($field['value']));
				}
				return get_instance()->load->view('widgets/camera', ['name' => $field['name'], 'value' => $field['value'], 'class' => $field['class'] ?: 'el'], true);
			case 'form_dropdown':
				return get_instance()->load->view('widgets/select2picker', ['field' => $field], true);
			case 'form_summernote':
				return get_instance()->load->view('widgets/summernote', ['field' => $field], true);
			case 'form_upload':
				return get_instance()->load->view('widgets/uploadpicker', ['field' => $field], true);
			case 'form_dropzone':
				$options = [
					'dropZoneId' => isset($field['id']) ? $field['id'] : 'dropZone_' . uniqid(),
					'fileInputId' => isset($field['name']) ? $field['name'] : 'document',
					'accept' => isset($field['accept']) ? $field['accept'] : '.pdf',
					'maxFiles' => isset($field['maxFiles']) ? $field['maxFiles'] : 1,
					'maxSize' => isset($field['maxSize']) ? $field['maxSize'] : 10,
					'labelText' => isset($field['labelText']) ? $field['labelText'] : 'Drag & Drop File Di Sini',
					'subText' => isset($field['subText']) ? $field['subText'] : 'atau klik untuk memilih file',
					'icon_class' => isset($field['icon_class']) ? $field['icon_class'] : 'fa-cloud-upload',
					'multiple' => isset($field['multiple']) ? $field['multiple'] : false,
					'required' => isset($field['required']) ? $field['required'] : false,
					'submitBtnId' => isset($field['submitBtnId']) ? $field['submitBtnId'] : 'submitBtn',
					'showBrowseBtn' => isset($field['showBrowseBtn']) ? $field['showBrowseBtn'] : false,
				];
				return form_dropzone($field['name'], $options);
			case 'form_datetimepicker':
				return get_instance()->load->view('widgets/datetimepicker', ['field' => $field], true);
			case 'form_datepicker':
				return get_instance()->load->view('widgets/datepicker', ['field' => $field], true);
			case 'form_timepicker':
				return get_instance()->load->view('widgets/timepicker', ['data' => $field], true);
			case 'form_textarea':
				return get_instance()->load->view('widgets/textarea', ['field' => $field], true);
			default:
				return get_instance()->load->view('widgets/textfield', ['field' => $field], true);
		}
	}
}

if (!function_exists('filter_form')) {
	function filter_form($data, $action = '', $attributes = [])
	{
		$attributes['id'] = isset($attributes['id']) ? $attributes['id'] : 'my-form-filter';
		$attributes['formClass'] = isset($attributes['formClass']) ? $attributes['formClass'] : 'mb-3';

		$html = '<form method="get" id="' . $attributes['id'] . '" class="d-flex justify-content-start bd-highlight ' . $attributes['formClass'] . '" action="' . base_url($action) . '">';

		foreach ($data as $field) {
			if (!isset($field['id']) && isset($field['name'])) {
				$field['id'] = $field['name'];
			}
			if (!isset($field['class'])) {
				$field['class'] = 'form-control';
			}

			$html .= isset($field['type']) ? switch_input($field['type'], $field) : form_input($field);
		}

		if (!isset($attributes['hideSubmitButton']) || !$attributes['hideSubmitButton']) {
			$html .= '      <button type="submit" class="btn btn-outline-primary" title="Cari"><i class="fa fa-search"></i></button>';
		}
		$html .= '      <a href="' . base_url($action) . '" class="btn btn-outline-danger ml-1" title="Refresh Pencarian"><i class="fa fa-refresh"></i></a>';

		$html .= '</form>';

		return $html;
	}
}

if (!function_exists('my_form')) {
	function my_form($data, $options = '', $attributes = [])
	{
		$html = '<div class="card m-0">';

		$html .= '<div class="card-header leaves">';
		if (isset($options['title'])) {
			$html .= '<h4 class="card-title">' . $options['title'] . '</h3>';
			$html .= '<div class="card-tools"></div>';
		}
		$html .= '</div>';

		$html .= ' <div class="card-body pb-2">';
		if (!isset($attributes['class'])) {
			$attributes['class'] = '';
		}

		if (!isset($options['ajax']) || $options['ajax']) {
			$attributes['class'] .= ' form-ajax';
		}

		if (isset($options['message']) && $options['message']) {
			$message_title = isset($options['message_title']) ? $options['message_title'] : 'Kelengkapan Data';
			$html .= alert($options['message'], $message_title, ['class' => 'small alert-danger']);
		}

		$html .= form_open_multipart(isset($options['action']) && $options['action'] ? $options['action'] : uri_string(), $attributes);

		foreach ($data as $field) {
			if (!isset($field['id']) && isset($field['name'])) {
				$field['id'] = $field['name'];
			}
			if (!isset($field['class'])) {
				$field['class'] = 'form-control';
			}

			$collapse = !isset($field['visible']) || $field['visible'] === true ? '' : 'collapse';
			$divClass = isset($field['divClass']) ? $field['divClass'] : '';

			$label = isset($field['label']) ? $field['label'] : (isset($field['placeholder']) ? $field['placeholder'] : '');
			$html .= $label ? '<label for="' . $field['name'] . '" class="' . $divClass . ' ' . $collapse . '">' . $label . '</label>' : '';
			$html .= '<div class="mb-3">';
			$html .= isset($field['type']) && (in_array($field['type'], ['form_hidden', 'form_checkbox'])) ? '' : '<div class="input-group align-items-start ' . ($divClass ?: 'justify-content-start') . ' ' . $collapse . '">';
			$html .= isset($field['type']) ? switch_input($field['type'], $field) : form_input($field);
			$html .= isset($field['type']) && (in_array($field['type'], ['form_hidden', 'form_checkbox'])) ? '' : '</div>';

			// Add help text if provided
			if (isset($field['help']) && !empty($field['help'])) {
				$html .= '<small class="form-text text-muted">' . $field['help'] . '</small>';
			}
			$html .= '</div>';
		}

		$isConfirmation = isset($options['isConfirmation']) ? $options['isConfirmation'] : false;
		$showBtnBack = isset($options['backUrl']) && $options['backUrl'];
		$showBtnCloseModal = isset($options['showBtnCloseModal']) ? $options['showBtnCloseModal'] : false;
		$submitLabel = isset($options['submitLabel']) && $options['submitLabel'] ? $options['submitLabel'] : ($isConfirmation ? 'Ya' : 'Simpan');
		$submitIcon = isset($options['submitIcon']) && $options['submitIcon'] ? $options['submitIcon'] : 'save';
		$submitClass = isset($options['submitClass']) ? $options['submitClass'] : '';

		// Process additional buttons if provided
		$additionalButtons = isset($options['additional_buttons']) && is_array($options['additional_buttons']) ? $options['additional_buttons'] : [];

		$html .= '  <div class="div-button d-flex justify-content-end mb-3">';
		// Add additional buttons first (on the left)
		foreach ($additionalButtons as $btn) {
			if (isset($btn['type']) && $btn['type'] === 'link' && isset($btn['url'])) {
				$btnClass = isset($btn['class']) ? $btn['class'] : 'btn btn-secondary';
				$html .= anchor($btn['url'], $btn['text'], ['class' => $btnClass . ' m-1']);
			} elseif (isset($btn['type']) && $btn['type'] === 'button') {
				$btnClass = isset($btn['class']) ? $btn['class'] : 'btn btn-secondary';
				$btnOnClick = isset($btn['onclick']) ? 'onclick="' . $btn['onclick'] . '"' : '';
				$html .= '<button type="button" class="' . $btnClass . ' m-1" ' . $btnOnClick . '>' . $btn['text'] . '</button>';
			}
		}
		$html .= $showBtnBack ? anchor($options['backUrl'], '<span class="fa fa-chevron-left" aria-hidden="true"></span> ' . ($isConfirmation ? 'Tidak' : 'Kembali'), ['class' => 'btn btn-outline-warning btn-back m-1', 'style' => 'width: 100px;']) : '';
		$html .= !$showBtnBack && $showBtnCloseModal ? '<button type="button" class="btn btn-outline-warning m-1" data-bs-dismiss="modal" data-dismiss="modal" style="min-width: 100px;"><i class="fa fa-times"></i> ' . ($isConfirmation ? 'Tidak' : 'Batal') . '</button>' : '';
		$html .= '      <button type="submit" class="btn btn-outline-primary m-1 ' . $submitClass . '" style="min-width: 100px;"><i class="fa fa-' . $submitIcon . '"></i> ' . $submitLabel . '</button>';
		$html .= '  </div>';

		$html .= form_close();

		$html .= ' </div>';
		$html .= ' <div class="card-footer p-0">';
		$html .= ' </div>';
		$html .= '</div>';

		return $html;
	}
}

if (! function_exists('form_input')) {
	/**
	 * Text Input Field
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_input($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'text',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);

		if (isset($data['readonly']) && $data['readonly'] !== true) {
			unset($data['readonly']);
		}
		if (isset($data['required']) && $data['required'] !== true) {
			unset($data['required']);
		}

		return '<input ' . _parse_form_attributes($data, $defaults) . _attributes_to_string($extra) . " />\n";
	}
}

if (! function_exists('form_textarea')) {
	/**
	 * Textarea field
	 *
	 * @param	mixed	$data
	 * @param	string	$value
	 * @param	mixed	$extra
	 * @return	string
	 */
	function form_textarea($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'name' => is_array($data) ? '' : $data,
			'cols' => '40',
			'rows' => '3'
		);

		if (! is_array($data) or ! isset($data['value'])) {
			$val = $value;
		} else {
			$val = $data['value'];
			unset($data['value']); // textareas don't use the value attribute
		}

		if (isset($data['readonly']) && $data['readonly'] !== true) {
			unset($data['readonly']);
		}
		if (isset($data['required']) && $data['required'] !== true) {
			unset($data['required']);
		}

		return '<textarea ' . _parse_form_attributes($data, $defaults) . _attributes_to_string($extra) . '>'
			. html_escape($val)
			. "</textarea>\n";
	}
}

if (! function_exists('form_dropdown')) {
	/**
	 * Drop-down Menu
	 *
	 * @param	mixed	$data
	 * @param	mixed	$options
	 * @param	mixed	$selected
	 * @param	mixed	$extra
	 * @return	string
	 */
	function form_dropdown($data = '', $options = array(), $selected = array(), $extra = '')
	{
		$defaults = array();

		if (is_array($data)) {
			if (isset($data['selected'])) {
				$selected = $data['selected'];
				unset($data['selected']); // select tags don't have a selected attribute
			}

			if (isset($data['options'])) {
				$options = $data['options'];
				unset($data['options']); // select tags don't use an options attribute
			}
		} else {
			$defaults = array('name' => $data);
		}

		is_array($selected) or $selected = array($selected);
		is_array($options) or $options = array($options);

		// If no selected state was submitted we will attempt to set it automatically
		if (empty($selected)) {
			if (is_array($data)) {
				if (isset($data['name'], $_POST[$data['name']])) {
					$selected = array($_POST[$data['name']]);
				}
			} elseif (isset($_POST[$data])) {
				$selected = array($_POST[$data]);
			}
		}

		$extra = _attributes_to_string($extra);

		$multiple = (count($selected) > 1 && stripos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

		if (isset($data['readonly']) && $data['readonly'] !== true) {
			unset($data['readonly']);
		}
		if (isset($data['required']) && $data['required'] !== true) {
			unset($data['required']);
		}

		$form = '<select ' . rtrim(_parse_form_attributes($data, $defaults)) . $extra . $multiple . ">\n";

		foreach ($options as $key => $val) {
			$key = (string) $key;

			if (is_array($val)) {
				if (empty($val)) {
					continue;
				}

				$form .= '<optgroup label="' . $key . "\">\n";

				foreach ($val as $optgroup_key => $optgroup_val) {
					$sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
					$form .= '<option value="' . html_escape($optgroup_key) . '"' . $sel . '>'
						. (string) $optgroup_val . "</option>\n";
				}

				$form .= "</optgroup>\n";
			} else {
				$form .= '<option value="' . html_escape($key) . '"'
					. (in_array($key, $selected) ? ' selected="selected"' : '') . '>'
					. (string) $val . "</option>\n";
			}
		}

		return $form . "</select>\n";
	}
}

if (!function_exists('form_captcha')) {
	/**
	 * Captcha Field
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_captcha($data = '', $value = '', $extra = '')
	{
		$ci = &get_instance();

		// set captcha
		$ci->load->helper('captcha');
		$cap = create_captcha([
			'img_path'      => './captcha/',
			'img_url'       => base_url('captcha'),
			'word_length'   => 5,
			'font_size'     => 20,           // Reduced size for better fit
			'img_height'    => 50,
			'img_width'     => 180,
			'pool'        => '123456789',
			// 'pool'        => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
		]);

		$query = $ci->db->insert_string('captcha', [
			'captcha_time'  => $cap['time'],
			'ip_address'    => $ci->input->ip_address(),
			'word'          => $cap['word']
		]);
		$ci->db->query($query);
		// end of set captcha

		$defaults = array(
			'type' => 'text',
			'name' => is_array($data) ? '' : $data,
			'value' => $value,
		);

		return $cap['image'] . '<input ' . _parse_form_attributes($data, $defaults) . _attributes_to_string($extra) . " />\n";
	}
}

if (!function_exists('form_upload')) {
	/**
	 * Form Upload Field
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_upload($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'text',
			'name' => is_array($data) ? '' : $data,
			'value' => $value,
		);

		if (isset($data['required']) && $data['required'] !== true) {
			unset($data['required']);
		}

		// Set default allowed file types to PDF only if not specified
		$allowed_types = isset($data['allowed_types']) ? $data['allowed_types'] : 'pdf';
		$allowed_mimes = isset($data['allowed_mimes']) ? $data['allowed_mimes'] : '';

		// Convert allowed types and mimes to accept attribute format
		$accept_values = [];

		// Process allowed file extensions
		if ($allowed_types) {
			$type_map = [
				'pdf' => '.pdf',
				'doc' => '.doc,.docx',
				'docx' => '.doc,.docx',
				'xls' => '.xls,.xlsx',
				'xlsx' => '.xls,.xlsx',
				'jpg' => '.jpg,.jpeg',
				'png' => '.png',
				'gif' => '.gif',
				'txt' => '.txt',
				'csv' => '.csv',
				'mp3' => '.mp3',
				'mp4' => '.mp4',
				'wav' => '.wav',
			];

			$types = is_array($allowed_types) ? $allowed_types : explode(',', str_replace(' ', '', $allowed_types));

			// Track which extensions have been added to avoid duplicates
			$added_extensions = [];

			foreach ($types as $type) {
				if (isset($type_map[$type])) {
					$extensions = explode(',', $type_map[$type]);
					foreach ($extensions as $ext) {
						if (!in_array($ext, $added_extensions)) {
							$accept_values[] = $ext;
							$added_extensions[] = $ext;
						}
					}
				} else {
					// For unknown types, add as is with a dot prefix if needed
					$ext = strpos($type, '.') === 0 ? $type : '.' . $type;
					if (!in_array($ext, $added_extensions)) {
						$accept_values[] = $ext;
						$added_extensions[] = $ext;
					}
				}
			}
		}

		// Process allowed MIME types
		if ($allowed_mimes) {
			$mimes = is_array($allowed_mimes) ? $allowed_mimes : explode(',', str_replace(' ', '', $allowed_mimes));
			$accept_values = array_merge($accept_values, $mimes);
		}

		$accept_attr = '';
		if (!empty($accept_values)) {
			$accept_attr = ' accept="' . implode(',', $accept_values) . '"';
		}

		return '<div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="form-control custom-file-input"' . $accept_attr . ' ' . _parse_form_attributes($data, $defaults) . _attributes_to_string($extra) . ' />
                        ' . (isset($data['placeholder']) ? '<label class="custom-file-label" for="' . $data['name'] . '">' . $data['placeholder'] . '</label>' : '') . '
                    </div>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fa fa fa-folder-open"></span>
                        </div>
                    </div>
                </div>';
	}
}

if (!function_exists('form_dropzone')) {
	/**
	 * Form Dropzone Upload Field
	 *
	 * @param	string	$name		Field name
	 * @param	array	$options	Dropzone options (id, accept, maxFiles, maxSize, labelText, subText, icon_class, multiple, required, submitBtnId, showBrowseBtn)
	 * @param	string	$extra		Additional HTML attributes
	 * @return	string
	 */
	function form_dropzone($name = 'document', $options = [], $extra = '')
	{
		$ci = &get_instance();
		
		// Default options
		$defaults = [
			'dropZoneId' => 'dropZone_' . uniqid(),
			'fileInputId' => 'document',
			'accept' => '*/*',
			'maxFiles' => 1,
			'maxSize' => 10, // MB
			'labelText' => 'Drag & Drop File Di Sini',
			'subText' => 'atau klik untuk memilih file',
			'icon_class' => 'fa-cloud-upload',
			'multiple' => false,
			'required' => true,
			'submitBtnId' => 'submitBtn',
			'showBrowseBtn' => false, // Default to hide "Pilih File" button
			'onFileSelect' => 'function(files) { window.selectedFile = files[0]; }',
			'onFileClear' => 'function() { window.selectedFile = null; }',
		];
		
		$options = array_merge($defaults, $options);
		
		// Convert maxFiles to multiple attribute
		$options['multiple'] = $options['maxFiles'] > 1 || $options['multiple'];
		
		// Load the dropzone widget view
		return $ci->load->view('widgets/dropzone', $options, true);
	}
}

if (!function_exists('form_datepicker')) {
	/**
	 * Datepicker Field
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_datepicker($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'text',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);

		// Check if readonly attribute is set in the data array
		$readonly_attr = (isset($data['readonly']) && $data['readonly']) ? ' readonly' : '';

		// $html = '<input ' . _parse_form_attributes($data, $defaults) . _attributes_to_string($extra) . " autocomplete=\"off\" readonly />\n";
		$html = '<input ' . _parse_form_attributes($data, $defaults) . _attributes_to_string($extra) . " autocomplete=\"off\"{$readonly_attr} />\n";
		$html .= '<div class="input-group-append">';
		$html .= '  <div class="input-group-text">';
		$html .= '      <span class="fa fa-calendar"></span>';
		$html .= '  </div>';
		$html .= '</div>';

		return $html;
	}
}

if (!function_exists('form_toggler')) {
	/**
	 * Toggler Field
	 *
	 * @param	mixed
	 * @return	string
	 */
	function form_toggler($data)
	{
		if (!isset($data['value'])) {
			$data['value'] = null;
		}
		if (!isset($data['options'])) {
			$data['options'] = [1 => 'Ya', 0 => 'Tidak'];
		}

		$onclick0 = isset($data['onclick']) ? 'onclick="' . $data['onclick'][0] . '"' : '';
		$onclick1 = isset($data['onclick']) ? 'onclick="' . $data['onclick'][1] . '"' : '';

		$values = array_keys($data['options']);
		$labels = array_values($data['options']);

		return '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-outline-success ' . (!is_null($data['value']) && $data['value'] == $values[0] ? 'active' : '') . '" style="min-width: ' . (isset($data['btnWidth'][0]) ? $data['btnWidth'][0] : 68) . 'px;">
                        <input type="radio" name="' . $data['name'] . '" id="' . $data['name'] . '_option0" ' . $onclick0 . ' value="' . $values[0] . '" autocomplete="off" ' . (!is_null($data['value']) && $data['value'] == $values[0] ? 'checked=""' : '') . '> ' . $labels[0] . '
                    </label>
                    <label class="btn btn-outline-success ' . (!is_null($data['value']) && $data['value'] == $values[1] ? 'active' : '') . '" style="min-width: ' . (isset($data['btnWidth'][1]) ? $data['btnWidth'][1] : 68) . 'px;">
                        <input type="radio" name="' . $data['name'] . '" id="' . $data['name'] . '_option1" ' . $onclick1 . ' value="' . $values[1] . '" autocomplete="off" ' . (!is_null($data['value']) && $data['value'] == $values[1] ? 'checked=""' : '') . '> ' . $labels[1] . '
                    </label>
                </div>';
	}
}

if (!function_exists('form_switcher')) {
	/**
	 * Switcher Field
	 *
	 * @param	mixed
	 * @return	string
	 */
	function form_switcher($data)
	{
		if (!isset($data['value'])) {
			$data['value'] = true;
		}

		return '<div class="form-check form-switch">
                    <input name="' . $data['name'] . '" class="form-check-input" type="checkbox" role="switch" style="cursor: pointer;" ' . ($data['value'] ? 'checked' : '') . ' />
                    ' . (isset($data['sublabel']) ? '<label class="form-check-label text-muted" for="switch-theme">' . $data['sublabel'] . '</label>' : '') . '
                </div>';
	}
}

if (!function_exists('anchor_confirm')) {
	/**
	 * Anchor Link
	 *
	 * Creates an anchor based on the local URL.
	 *
	 * @param	string	the URL
	 * @param	string	the link title
	 * @param	mixed	any attributes
	 * @return	string
	 */
	function anchor_confirm($uri = '', $title = '', $attributes = '')
	{
		$title = (string) $title;

		$site_url = is_array($uri)
			? site_url($uri)
			: (preg_match('#^(\w+:)?//#i', $uri) ? $uri : site_url($uri));

		if ($title === '') {
			$title = $site_url;
		}

		// if (is_array($attributes)) {
		//     $CI = get_instance();
		//     $attributes = array_merge($attributes, [
		//         'data-csrf-token-name' => $CI->security->get_csrf_token_name(),
		//         'data-csrf-hash' => $CI->security->get_csrf_hash(),
		//     ]);
		// }

		if (!isset($attributes['class'])) {
			$attributes['class'] = '';
		}
		$attributes['class'] .= ' btn-confirm';

		if ($attributes !== '') {
			$attributes = _stringify_attributes($attributes);
		}

		return '<a href="' . $site_url . '"' . $attributes . '>' . $title . '</a>';
	}
}
