<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* PHP5 spl_autoload */
spl_autoload_register('modules::load');
spl_autoload_register('modules::autoload');

/**
 * Modular Extensions - PHP5
 *
 * Adapted from the CodeIgniter Core Classes
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @link		http://codeigniter.com
 *
 * Description:
 * This library provides functions to load and instantiate controllers
 * and module controllers allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/libraries/Modules.php
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
class Modules
{	
	public static $registry = array();
	
	/**
	* Run a module controller method
	* Output from module is buffered and returned.
	**/
	public static function run($module) {		
		$method = 'index';
		
		if(($pos = strrpos($module, '/')) != FALSE) {
			$method = substr($module, $pos + 1);		
			$module = substr($module, 0, $pos);
		}
	
		$controller = end(explode('/', $module));
		
		if($class = self::load($module.'/'.$controller)) {
			
			if (method_exists($class, $method))	{
				ob_start();
				$args = func_get_args();
				$output = call_user_func_array(array($class, $method), array_slice($args, 1));
				$buffer = ob_get_clean();
				return ($output) ? $output : $buffer;
			}

			log_message('debug', "Module failed to run: {$controller}/{$method}");
		}
	}
	
	/** Load a module controller **/
	public static function load($module) {	
		(is_array($module)) AND list($module, $params) = each($module) OR $params = NULL;	
		
		/* get the controller class name */
		$class = strtolower(end(explode('/', $module)));

		/* don't autoload CI_, MX_ or MY_ prefixed classes */
		if (strstr($class, 'CI_') OR strstr($class, 'MX_') OR strstr($class, 'MY_')) return;
		
		/* return an existing controller from the registry */
		if (isset(self::$registry[$class])) return self::$registry[$class];
			
		/* get the module name */
		list($module) = explode('/', $module);

		/* find the module controller */
		list($module, $controller) = Router::locate(array($module, $class));
		
		if ($controller === FALSE) return $controller;

		/* set the module directory */
		$path = ($module) ? MODBASE.$module.'/controllers/' : APPPATH.'controllers/';

		/* load the controller class */
		self::load_file($controller, $path);
		
		/* create the new controller */
		$controller = ucfirst($controller);
		return new $controller($params);
	}
	
	/** Library base class autoload **/
	public static function autoload($class) {
		
		/* don't autoload CI_ or MY_ prefixed classes */
		if (strstr($class, 'CI_') OR strstr($class, 'MY_')) return;
		
		if(is_file($location = APPPATH.'libraries/'.$class.EXT)) {
			include_once $location;	
		}
	}

	/** Load a module file **/
	public static function load_file($file, $path, $type = 'other', $result = TRUE)	{
		$file = str_replace(EXT, '', $file);		
		$location = $path.$file.EXT;
		
		if ($type === 'other') {		
			if (class_exists($file, FALSE))	{
				log_message('debug', "File already loaded: {$location}");				
				return $result;
			}
			include_once $location;
		} else { 
		
			/* load config or language array */
			include $location;

			if ( ! isset($$type) OR ! is_array($$type))				
				show_error("{$location} does not contain a valid {$type} array");

			$result = $$type;
		}
		log_message('debug', "File loaded: {$location}");
		return $result;
	}

	/** 
	* Find a file
	* Scans for files located within application/modules directory.
	* Also scans application directories for models and views.
	* Generates fatal error on file not found.
	**/
	public static function find($file, $module, $base, $subpath = NULL) {
		
		/* get the module path */
		$search = (isset($module)) ? array(MODBASE.$module.'/') : array();
		
		/* is there a subdirectory in the filename? */
		if ($pos = strrpos($file, '/')) {
			$subpath = substr($file, 0, $pos);						
			$file = substr($file, $pos + 1);
	    }
		
		$path = ($subpath) ? $base.$subpath.'/' : $base;
		$file_ext = strpos($file, '.') ? $file : $file.EXT;
		
		if ($base == 'libraries/') $file_ext = ucfirst($file_ext);
				
		/* search modules */
		foreach ($search as $source) {
			if (is_file($source.$path.$file_ext)) return array($source.$path, $file);
		}
		
		/* search the application directory for views or models */
		if ($base == 'views/' OR $base == 'models/') {
			if (is_file(APPPATH.$path.$file_ext)) return array(APPPATH.$path, $file);
			show_error("Unable to locate the file: {$file_ext} in {$path}");
		}
		
		return array(FALSE, $file);	
	}
}