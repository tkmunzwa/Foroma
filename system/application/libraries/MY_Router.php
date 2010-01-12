<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* define the modules base path */
define('MODBASE', APPPATH.'modules/');

/* define the offset from application/controllers */
define('MODOFFSET', '../modules/');

/**
 * Modular Extensions - PHP5
 *
 * Adapted from the CodeIgniter Core Classes
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @link		http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter router class.
 *
 * Install this file as application/libraries/MY_Router.php
 *
 * @copyright 	Copyright (c) Wiredesignz 2009-05-09
 * @version 	5.2.09
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 **/
 
class Router
{
	public static $path;
	
	/** Locate the controller **/
	public static function locate($segments) {		

		/* pad the segment array */
		$segments += array($segments, NULL, NULL);
		
		/* locate the controller */
		list($module, $controller) = $segments;
	
		/* module? */
		if ($module AND is_dir(MODBASE.$module)) {
			
			self::$path = $module;
			
			/* module sub-controller? */
			if(is_file(MODBASE.$module.'/controllers/'.$controller.EXT))			
				return array($module, $controller);
				
			/* module controller? */
			return array($module, $module);
		}
			
		/* not a module controller */
		return array(FALSE, FALSE);
	}
}

class MY_Router extends CI_Router
{
	public function _validate_request($segments)
	{
		/* locate the module controller */
		list($module, $controller) = Router::locate($segments);

		/* not a module controller */
		if ($controller === FALSE) 
			return parent::_validate_request($segments);
		
		/* set the module path */
		$path = ($module) ? MODOFFSET.$module.'/controllers' : NULL;				
		
		$this->set_directory($path);
		
		($module == $controller) OR $segments = array_slice($segments, 1);
		
		return $segments;
	}
}