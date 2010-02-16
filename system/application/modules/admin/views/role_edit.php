<?php
@$role = $data['role'];
@$permissions = $data['permissions'];
?>
<div class="error">
<?php echo validation_errors(); 
if (@$data['errors']) foreach($data['errors'] as $error):
?><p><?php echo $error;?></p>
<?php endforeach;?>
</div>
<?php
if(@$data['messages']):
?><div class="info">
<?php 
 foreach($data['messages'] as $msg):
?><p><?php echo $msg;?></p>
<?php endforeach;?>
</div>
<?php endif;?>
<?php
$controller = "";
if (isset($data) && isset($data['controller'])) $controller = $data['controller'];
echo form_open($controller, array("method"=>"post")); 

$role_permissions = array();
if (isset($role) && is_object($role)) :
	foreach($role->Permissions as $p):
		$role_permissions[] = $p->id; 
	endforeach;
	echo form_hidden(array("role_id"=>$role->id, "action"=>"save"));
endif;
?>
<fieldset>
<div><?php
echo form_button(array('icon'=>'save'),lang("save"));
echo form_button(array('icon'=>'cancel', 'href'=>site_url("/admin/roles"), 'type'=>'link'),lang("cancel"));
?>
</div>
</fieldset>
<fieldset>
<label for="name"><?php echo lang('name');?></label>
<input type="text" name="name" value="<?php echo set_value('name', $role->name); ?>" size="50" />
</fieldset>

<fieldset>
<label><?php echo lang('permissions');?></label>
<table class="datagrid">
<?php foreach($permissions as $permission):?>
<tr><td>
<input type="checkbox" value="<?php echo $permission->id; ?>" name="permissions[]" <?php echo set_checkbox('permissions[]', $permission->id); ?>
<?php if (in_array($permission->id, $role_permissions)) echo " checked=\"checked\""?> /><?php echo $permission->name;?>
</td></tr>
<? endforeach?>
</table>
</fieldset>
<fieldset>
<div><?php
echo form_button(array('icon'=>'save'),lang("save"));
echo form_button(array('icon'=>'cancel', 'href'=>site_url("/admin/roles"), 'type'=>'link'),lang("cancel"));
?>
</div>
</fieldset>
</form>