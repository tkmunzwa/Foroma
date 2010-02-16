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
<th><?php echo lang('id');?></th>
<th><?php echo lang('permission');?></th>
<th><?php echo lang('description');?></th>
<th><?php echo lang('edit');?></th>
<th><?php echo form_button(array('href'=> site_url('admin/permissions/create'), 'type'=>'link', 'icon'=>'add'), lang("add")); ?></th>

</tr>
<?php 
$permissions = $data['permissions'];
?>
<?php
if (count($permissions) > 0):
foreach($permissions as $permission):
//class_name($user);
?>
<tr>
<td><?php echo $permission->id;?></td>
<td><?php echo $permission->name;?></td>
<td><?php echo $permission->description;?></td>
<td><?php echo form_button(array('type'=>'link', 'class'=>'edit', 'icon'=>'edit', 'href'=> site_url("/admin/permissions/edit")."/".$permission->id), lang('edit')); ?></td>
<td><?php echo form_button(array('type'=>'link', 'class'=>'edit', 'icon'=>'delete', 'href'=> site_url("/admin/permissions/delete")."/".$permission->id), lang('delete')); ?></td>
</tr>

<?php
endforeach;
endif;?>
</table>
<?php endif;?>
