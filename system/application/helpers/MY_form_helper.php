<?php
/**
 * Form Button
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_button'))
{
	function form_button($data = '', $content = '', $extra = '')
	{
		$defaults = array('name' => (( ! is_array($data)) ? $data : ''), 'type' => 'submit', 'class'=>'sexybutton');

		if ( is_array($data) AND isset($data['content']))
		{
			$content = $data['content'];
			unset($data['content']); // content is not an attribute
		}
		if ( is_array($data) AND isset($data['class']) AND is_array($data['class']))
		{
			$data['class'] = join(" ", $data['class']);
		}
		if (isset($data['icon']) && $data['icon']!=''){
			$content = "<span class=\"{$data['icon']}\">$content</span>";
		}
		if (is_array($data) && (!isset($data['class']) || !strpos($data['class'], "sexybutton"))){
			if (!isset($data['class'])) $data['class'] = "";
			$data['class'] .= ($data['class']? " ":"")."sexybutton";
		}
		if (is_array($data) && isset($data['type']) && $data['type'] == "link"){
			unset($data['type']);
			return "<a "._parse_form_attributes($data, array()).$extra."><span><span>".$content."</spa></span></a>";
		}
		return "<button "._parse_form_attributes($data, $defaults).$extra."><span><span>".$content."</spa></span></button>";
	}
}

// ------------------------------------------------------------------------

/**
 * Parse the form attributes
 *
 * Helper function used by some of the form helpers
 *
 * @access	private
 * @param	array
 * @param	array
 * @return	string
 */
if ( ! function_exists('_parse_form_attributes'))
{
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
			if ($key == 'value')
			{
				$val = form_prep($val);
			}

			$att .= $key . '="' . $val . '" ';
		}

		return $att;
	}
}

?>