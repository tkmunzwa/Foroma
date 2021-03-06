<?php if ($v_errors = validation_errors()): ?>
<div class="error">
<?php echo $v_errors; ?>
</div>
<?php endif ?>
<form method="post">
<?php
$controller = "";
if (isset($data) && isset($data['controller'])) $controller = $data['controller']; 
@$user = $data['user'];
if (isset($user) && is_object($user)) :
	$user_groups = array();
	foreach($user->Groups as $group):
		$user_groups[] = $group->id; //add user's values to array
	endforeach;
	echo form_hidden(array("user_id"=>$user->id, "action"=>"save"));
endif;
$groups = $data['groups'];
$langs = $data['langs'];
if (isset($_REQUEST['groups'])) $user_groups = $_REQUEST['groups'];
?>
<fieldset>
<label><?php echo lang('username');?></label>
<input type="text" name="username" value="<?php echo set_value('username', $user->username); ?>" size="50" />
</fieldset>
<fieldset>
<label><?php echo lang('password');?></label>
<input type="password" name="password" value="<?php // echo set_value('password'); ?>" size="50" />
</fieldset>
<fieldset>
<label><?php echo lang('password_repeat');?></label>
<input type="password" name="passconf" value="<?php //echo set_value('passconf'); ?>" size="50" />
</fieldset>
<fieldset>
<label><?php echo lang('email_add');?></label>
<input type="text" name="email" value="<?php echo set_value('email', $user->emailaddress); ?>" size="50" />
</fieldset>
<fieldset>
<label><?php echo lang('language');?></label>
<select name="lang" id="lang">
	<option></option>
<?php
foreach(@$langs as $lang):
?>

<option value="<?php echo $lang->name; ?>" <?php  echo set_select('lang', $lang->name, ($lang->name == $user->language));?> ><?php echo $lang->name;?></option>
<?php endforeach;?>
</select>
</fieldset>
<fieldset name="Group Membership"> 
<label><?php echo lang('roles');?></label>
<?php
foreach(@$groups as $group):
?>
<input type="checkbox" value="<?php echo $group->id; ?>" name="groups[]" <?php echo set_checkbox('groups[]', $group->id); ?> <?php if (in_array($group->id, $user_groups)) echo " checked=\"checked\""?> /><?php echo $group->name;?><br/>
<?php endforeach;?>
</fieldset>
<fieldset>																					
<div>
<?php
echo form_button(array('icon'=>'save'),lang("save"));
echo form_button(array('icon'=>'cancel', 'href'=>site_url('/admin/users/listall'), 'type'=>'link'),lang("cancel"));
?></div>
</fieldset>
</form>