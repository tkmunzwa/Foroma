<?php if (validation_errors()):?>
<div class="error">
<?php echo validation_errors(); ?>
</div>
<?php endif;
$controller = "";
if (isset($data) && isset($data['controller'])) $controller = $data['controller'];
echo form_open($controller); 
?>
<input type="hidden" name="action" value="save" />
<input type="hidden" name="name" value="" />
<fieldset><label for="site">Site name</label><input type="text" name="site" id="site" value="<?php echo set_value('server', ""); ?>"/></fieldset>
<fieldset><label for="url">URL</label><input type="text" name="url" id="url" value="<?php echo set_value('port', ""); ?>"/></fieldset>
<fieldset><label for="dateformat">Date format</label><input type="text" name="dateformat" id="dateformat" value="<?php echo set_value('username', ""); ?>"/></fieldset>
<fieldset><label for="timezone">Server timezone</label>
<select name="timezone">
<?php foreach ($timezones as $name=>$tz):
	echo "<option value=\"{$tz}\"".  ($tz == "addfadfa" ? " selected=\"selected\"":"").">$name</option>"; 
 endforeach; ?>
</select></fieldset>
<fieldset>
<div><?php
echo form_button(array('icon'=>'save'),lang("save"));
echo form_button(array('icon'=>'cancel', 'href'=>site_url("/admin/config"), 'type'=>'link'),lang("cancel"));
?></div>
</fieldset>
<?php echo form_close();?>