<?php
if (isset($data) && $data):

if($data['errors']):
?><div class="error">
<?php 
 foreach($data['errors'] as $error):
?><p><?php echo $error;?></p>
<?php endforeach;?>
</div>
<?php endif;
if($data['messages']):
?><div class="info">
<?php 
 foreach($data['messages'] as $msg):
?><p><?php echo $msg;?></p>
<?php endforeach;?>
</div>
<?php endif;?>
<table class="datagrid">
<tr>
<th>ID</th>
<th>Role</th>
<th>Description</th>
<th>Edit</th>
<th><?php echo form_button(array('href'=> site_url('admin/roles/create'), 'type'=>'link', 'icon'=>'add'), lang("add")); ?></th>

</tr>
<?php 
$roles = $data['roles'];
?>
<?php
if (count($roles) > 0):
foreach($roles as $role):
//class_name($user);
?>
<tr>
<td><?php echo $role->id;?></td>
<td><?php echo $role->name;?></td>
<td><?php echo $role->description;?></td>
<td><?php echo form_button(array('type'=>'link', 'class'=>'edit', 'icon'=>'edit', 'href'=> site_url("/admin/roles/edit")."/".$role->id), lang('edit')); ?></td>
<td><?php echo form_button(array('type'=>'link', 'class'=>'edit', 'icon'=>'delete', 'href'=> site_url("/admin/roles/delete")."/".$role->id), lang('delete')); ?></td>
</tr>

<?php
endforeach;
endif;?>
</table>
<?php endif;?>
