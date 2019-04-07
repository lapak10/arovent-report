<?php
if ( ! function_exists('form_open'))
{
	/**
	 * Form Declaration
	 *
	 * Creates the opening portion of the form.
	 *
	 * @param	string	the URI segments of the form destination
	 * @param	array	a key/value pair of attributes
	 * @param	array	a key/value pair hidden data
	 * @return	string
	 */
	function form_open($action = '', $attributes = array(), $hidden = array())
	{
		//$CI =& get_instance();

		// If no action is provided then set to the current url
		if ( ! $action)
		{
			$action = current_page_url();
		}
		// If an action is not a full URL then turn it into one
		elseif (strpos($action, '://') === FALSE)
		{
			$action = get_permalink ( get_page_by_path( $action ) );
		}

		$attributes = _attributes_to_string($attributes);

		if (stripos($attributes, 'method=') === FALSE)
		{
			$attributes .= ' method="post"';
		}

		if (stripos($attributes, 'accept-charset=') === FALSE)
		{
			$attributes .= ' accept-charset="'.strtolower(get_bloginfo( 'charset' )).'"';
		}

		$form = '<form action="'.$action.'"'.$attributes.">\n";

		// Add CSRF field if enabled, but leave it out for GET requests and requests to external websites
		//if ($CI->config->item('csrf_protection') === TRUE && strpos($action, $CI->config->base_url()) !== FALSE && ! stripos($form, 'method="get"'))
		//{
		//	$hidden[$CI->security->get_csrf_token_name()] = $CI->security->get_csrf_hash();
		//}

		if (is_array($hidden))
		{
			foreach ($hidden as $name => $value)
			{
				$form .= '<input type="hidden" name="'.$name.'" value="'.html_escape($value).'" style="display:none;" />'."\n";
			}
		}

		return $form;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_open_multipart'))
{
	/**
	 * Form Declaration - Multipart type
	 *
	 * Creates the opening portion of the form, but with "multipart/form-data".
	 *
	 * @param	string	the URI segments of the form destination
	 * @param	array	a key/value pair of attributes
	 * @param	array	a key/value pair hidden data
	 * @return	string
	 */
	function form_open_multipart($action = '', $attributes = array(), $hidden = array())
	{
		if (is_string($attributes))
		{
			$attributes .= ' enctype="multipart/form-data"';
		}
		else
		{
			$attributes['enctype'] = 'multipart/form-data';
		}

		return form_open($action, $attributes, $hidden);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_hidden'))
{
	/**
	 * Hidden Input Field
	 *
	 * Generates hidden fields. You can pass a simple key/value string or
	 * an associative array with multiple values.
	 *
	 * @param	mixed	$name		Field name
	 * @param	string	$value		Field value
	 * @param	bool	$recursing
	 * @return	string
	 */
	function form_hidden($name, $value = '', $recursing = FALSE)
	{
		static $form;

		if ($recursing === FALSE)
		{
			$form = "\n";
		}

		if (is_array($name))
		{
			foreach ($name as $key => $val)
			{
				form_hidden($key, $val, TRUE);
			}

			return $form;
		}

		if ( ! is_array($value))
		{
			$form .= '<input type="hidden" name="'.$name.'" value="'.html_escape($value)."\" />\n";
		}
		else
		{
			foreach ($value as $k => $v)
			{
				$k = is_int($k) ? '' : $k;
				form_hidden($name.'['.$k.']', $v, TRUE);
			}
		}

		return $form;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_input'))
{
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

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

function esc_attr($value){

    return $value;

}

if ( ! function_exists('html_escape'))
{
function html_escape($value=''){
return esc_attr($value);
}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_password'))
{
	/**
	 * Password Field
	 *
	 * Identical to the input function but adds the "password" type
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_password($data = '', $value = '', $extra = '')
	{
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'password';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_upload'))
{
	/**
	 * Upload Field
	 *
	 * Identical to the input function but adds the "file" type
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_upload($data = '', $value = '', $extra = '')
	{
		$defaults = array('type' => 'file', 'name' => '');
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'file';

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_textarea'))
{
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
			'rows' => '10'
		);

		if ( ! is_array($data) OR ! isset($data['value']))
		{
			$val = $value;
		}
		else
		{
			$val = $data['value'];
			unset($data['value']); // textareas don't use the value attribute
		}

		return '<textarea '._parse_form_attributes($data, $defaults)._attributes_to_string($extra).'>'
			.html_escape($val)
			."</textarea>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_multiselect'))
{
	/**
	 * Multi-select menu
	 *
	 * @param	string
	 * @param	array
	 * @param	mixed
	 * @param	mixed
	 * @return	string
	 */
	function form_multiselect($name = '', $options = array(), $selected = array(), $extra = '')
	{
		$extra = _attributes_to_string($extra);
		if (stripos($extra, 'multiple') === FALSE)
		{
			$extra .= ' multiple="multiple"';
		}

		return form_dropdown($name, $options, $selected, $extra);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('form_dropdown'))
{
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

		if (is_array($data))
		{
			if (isset($data['selected']))
			{
				$selected = $data['selected'];
				unset($data['selected']); // select tags don't have a selected attribute
			}

			if (isset($data['options']))
			{
				$options = $data['options'];
				unset($data['options']); // select tags don't use an options attribute
			}
		}
		else
		{
			$defaults = array('name' => $data);
		}

		is_array($selected) OR $selected = array($selected);
		is_array($options) OR $options = array($options);

		// If no selected state was submitted we will attempt to set it automatically
		if (empty($selected))
		{
			if (is_array($data))
			{
				if (isset($data['name'], $_POST[$data['name']]))
				{
					$selected = array($_POST[$data['name']]);
				}
			}
			elseif (isset($_POST[$data]))
			{
				$selected = array($_POST[$data]);
			}
		}

		$extra = _attributes_to_string($extra);

		$multiple = (count($selected) > 1 && stripos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

		$form = '<select '.rtrim(_parse_form_attributes($data, $defaults)).$extra.$multiple.">\n";

		foreach ($options as $key => $val)
		{
			$key = (string) $key;

			if (is_array($val))
			{
				if (empty($val))
				{
					continue;
				}

				$form .= '<optgroup label="'.$key."\">\n";

				foreach ($val as $optgroup_key => $optgroup_val)
				{
					$sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
					$form .= '<option value="'.html_escape($optgroup_key).'"'.$sel.'>'
						.(string) $optgroup_val."</option>\n";
				}

				$form .= "</optgroup>\n";
			}
			else
			{
				$form .= '<option value="'.esc_attr($key).'"'
					.(in_array($key, $selected) ? ' selected="selected"' : '').'>'
					.(string) $val."</option>\n";
			}
		}

		return $form."</select>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_checkbox'))
{
	/**
	 * Checkbox Field
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @param	mixed
	 * @return	string
	 */
	function form_checkbox($data = '', $value = '', $checked = FALSE, $extra = '')
	{
		$defaults = array('type' => 'checkbox', 'name' => ( ! is_array($data) ? $data : ''), 'value' => $value);

		if (is_array($data) && array_key_exists('checked', $data))
		{
			$checked = $data['checked'];

			if ($checked == FALSE)
			{
				unset($data['checked']);
			}
			else
			{
				$data['checked'] = 'checked';
			}
		}

		if ($checked == TRUE)
		{
			$defaults['checked'] = 'checked';
		}
		else
		{
			unset($defaults['checked']);
		}

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_radio'))
{
	/**
	 * Radio Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @param	mixed
	 * @return	string
	 */
	function form_radio($data = '', $value = '', $checked = FALSE, $extra = '')
	{
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'radio';

		return form_checkbox($data, $value, $checked, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_submit'))
{
	/**
	 * Submit Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_submit($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'submit',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_reset'))
{
	/**
	 * Reset Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_reset($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'reset',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_button'))
{
	/**
	 * Form Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_button($data = '', $content = '', $extra = '')
	{
		$defaults = array(
			'name' => is_array($data) ? '' : $data,
			'type' => 'button'
		);

		if (is_array($data) && isset($data['content']))
		{
			$content = $data['content'];
			unset($data['content']); // content is not an attribute
		}

		return '<button '._parse_form_attributes($data, $defaults)._attributes_to_string($extra).'>'
			.$content
			."</button>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_label'))
{
	/**
	 * Form Label Tag
	 *
	 * @param	string	The text to appear onscreen
	 * @param	string	The id the label applies to
	 * @param	array	Additional attributes
	 * @return	string
	 */
	function form_label($label_text = '', $id = '', $attributes = array())
	{

		$label = '<label';

		if ($id !== '')
		{
			$label .= ' for="'.$id.'"';
		}

		if (is_array($attributes) && count($attributes) > 0)
		{
			foreach ($attributes as $key => $val)
			{
				$label .= ' '.$key.'="'.$val.'"';
			}
		}

		return $label.'>'.$label_text.'</label>';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_fieldset'))
{
	/**
	 * Fieldset Tag
	 *
	 * Used to produce <fieldset><legend>text</legend>.  To close fieldset
	 * use form_fieldset_close()
	 *
	 * @param	string	The legend text
	 * @param	array	Additional attributes
	 * @return	string
	 */
	function form_fieldset($legend_text = '', $attributes = array())
	{
		$fieldset = '<fieldset'._attributes_to_string($attributes).">\n";
		if ($legend_text !== '')
		{
			return $fieldset.'<legend>'.$legend_text."</legend>\n";
		}

		return $fieldset;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_fieldset_close'))
{
	/**
	 * Fieldset Close Tag
	 *
	 * @param	string
	 * @return	string
	 */
	function form_fieldset_close($extra = '')
	{
		return '</fieldset>'.$extra;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_close'))
{
	/**
	 * Form Close Tag
	 *
	 * @param	string
	 * @return	string
	 */
	function form_close($extra = '')
	{
		return '</form>'.$extra;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_prep'))
{
	/**
	 * Form Prep
	 *
	 * Formats text so that it can be safely placed in a form field in the event it has HTML tags.
	 *
	 * @deprecated	3.0.0	An alias for html_escape()
	 * @param	string|string[]	$str		Value to escape
	 * @return	string|string[]	Escaped values
	 */
	function form_prep($str)
	{
		return html_escape($str, TRUE);
	}
}

// ------------------------------------------------------------------------


// ------------------------------------------------------------------------

if ( ! function_exists('_parse_form_attributes'))
{
	/**
	 * Parse the form attributes
	 *
	 * Helper function used by some of the form helpers
	 *
	 * @param	array	$attributes	List of attributes
	 * @param	array	$default	Default values
	 * @return	string
	 */
	function _parse_form_attributes($attributes, $default)
	{
		if (is_array($attributes))
		{
			foreach ($default as $key => $val)
			{
				if (isset($attributes[$key]))
				{
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}

			if (count($attributes) > 0)
			{
				$default = array_merge($default, $attributes);
			}
		}

		$att = '';

		foreach ($default as $key => $val)
		{
			if ($key === 'value')
			{
				$val = html_escape($val);
			}
			elseif ($key === 'name' && ! strlen($default['name']))
			{
				continue;
			}

			$att .= $key.'="'.$val.'" ';
		}

		return $att;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_attributes_to_string'))
{
	/**
	 * Attributes To String
	 *
	 * Helper function used by some of the form helpers
	 *
	 * @param	mixed
	 * @return	string
	 */
	function _attributes_to_string($attributes)
	{
		if (empty($attributes))
		{
			return '';
		}

		if (is_object($attributes))
		{
			$attributes = (array) $attributes;
		}

		if (is_array($attributes))
		{
			$atts = '';

			foreach ($attributes as $key => $val)
			{
				$atts .= ' '.$key.'="'.$val.'"';
			}

			return $atts;
		}

		if (is_string($attributes))
		{
			return ' '.$attributes;
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

