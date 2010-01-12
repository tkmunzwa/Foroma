<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

/*
 * Security checker called before the controller is loaded.
 * Required by Foroma if you want to centralised security checking
 */
$hook['post_controller_constructor'] = array(
								'class'    => 'FO_Auth_SecurityHook',
                                'function' => 'checkPermissions',
                                'filename' => 'fo_auth.php',
                                'filepath' => 'hooks',
								//'params'=> CI::get_instance(),
                                //'params'   => array('beer', 'wine', 'snacks')
                                );


/* End of file hooks.php */
/* Location: ./system/application/config/hooks.php */