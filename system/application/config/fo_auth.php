<?php
/*
|----------------------------------------------------------------------------
| FO_Auth settings
| *Foroma meta-framework addition
|----------------------------------------------------------------------------
|
| Determines if pubic user is enabled for non-authenticated users
|
*/
#$config['enable_public_user'] = TRUE;
$config['enable_public_user'] = TRUE;
/*
|----------------------------------------------------------------------------
| FO_Auth settings
| *Foroma meta-framework addition
|----------------------------------------------------------------------------
|
| Username of user that is used by system for public (people who are not logged in)
| the username SHOULD be in the user db table or behaviour will be unpredictable.
| Please make sure that the user has the minimum
| security privileges required. Do not assign any user who has an administrative role
| this if you use this, then 'enabled_public_user' should be set to TRUE
|
*/
$config['public_user'] = "public";


/* End of file fo_auth.php */
/* Location: ./system/application/config/fo_auth.php */