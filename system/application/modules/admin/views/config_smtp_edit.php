<?php if (validation_errors()|| (isset($data) && isset($data['errors']))):?>
<div class="error">
<?php echo validation_errors();
if (isset($data) && isset($data['errors'])){
	foreach($data['errors'] as $error){
		echo $error."<br/>";
	}
}
?>
</div>
<?php endif;
$controller = "";
if (isset($data) && isset($data['controller'])) $controller = $data['controller'];
echo form_open($controller); 
?>
<input type="hidden" name="action" value="save" />
<input type="hidden" name="name" value="<?php echo $smtp->name;?>" />
<fieldset><label for="server">Server name</label><input type="text" class="required" name="server" id="server" value="<?php echo set_value('server', $smtp->server); ?>"/></fieldset>
<fieldset><label for="port">Port</label><input type="text" size="3" name="port" id="port" value="<?php echo set_value('port', $smtp->port); ?>"/></fieldset>
<fieldset><label for="username">Username</label><input type="text" autocomplete="off"  name="username" id="username" value="<?php echo set_value('username', $smtp->username); ?>"/></fieldset>
<fieldset><label for="password">Password</label><input type="password" autocomplete="off" name="password" id="password" value=""/></fieldset>
<fieldset><label for="confirmpassword">Confirm Password</label><input type="password" autocomplete="off" name="confirmpassword" id="confirmpassword" value=""/></fieldset>
<fieldset><label for="encryption">Encryption</label><select name="encryption">
<?php foreach ($encryptionschemes as $name=>$scheme):
	echo "<option value=\"{$scheme}\"".  ($scheme == $smtp->encryption ? " selected=\"selected\"":"").">$name</option>"; 
 endforeach; ?>
</select></fieldset>
<fieldset>
<div><?php
echo form_button(array('icon'=>'save'),lang("save"));
echo form_button(array('icon'=>'cancel', 'href'=>site_url("/admin/config"), 'type'=>'link'),lang("cancel"));
?></div>
</fieldset>
<?php echo form_close();?>