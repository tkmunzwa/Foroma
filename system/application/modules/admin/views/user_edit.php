<div class="error">
<?php echo validation_errors(); ?>
</div>
<?php
$controller = "";
if (isset($data) && isset($data['controller'])) $controller = $data['controller'];
echo form_open($controller); 
@$user = $data['user'];
if (isset($user) && is_object($user)) :
	$user_groups = array();
	foreach($user->Groups as $group):
		$user_groups[] = $group->id; //add user's values to array
	endforeach;
	echo form_hidden(array("user_id"=>$user->id, "action"=>"save"));
endif;

if (isset($_REQUEST['groups'])) $user_groups = $_REQUEST['groups'];
?>
<form>
<h5>Username</h5>
<input type="text" name="username" value="<?php echo set_value('username', $user->username); ?>" size="50" />

<h5>Password</h5>
<input type="password" name="password" value="<?php echo set_value('password'); ?>" size="50" />

<h5>Password Confirm</h5>
<input type="password" name="passconf" value="<?php echo set_value('passconf'); ?>" size="50" />

<h5>Email Address</h5>
<input type="text" name="email" value="<?php echo set_value('email', $user->emailaddress); ?>" size="50" />
<fieldset name="Group Membership"> 
<?php
$groups = Doctrine::getTable('Group')->findAll();
foreach($groups as $group):
?>
<input type="checkbox" value="<?php echo $group->id; ?>" name="groups[]" <?php if (in_array($group->id, $user_groups)) echo " checked=\"checked\""?> /><?php echo $group->name;?><br/>
<?php endforeach;?>
</fieldset>
<div>
<?php
echo form_button(array('icon'=>'save'),lang("save"));
echo form_button(array('icon'=>'cancel', 'onclick'=>"history.go(-1);", 'type'=>'button'),lang("cancel"));
?></div>
</form>