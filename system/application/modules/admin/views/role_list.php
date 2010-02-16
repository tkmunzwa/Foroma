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
<th><?php echo lang('name');?></th>
<th><?php echo lang('edit');?></th>
<th><?php echo form_button(array('href'=> site_url('admin/roles/create'), 'type'=>'link', 'icon'=>'add'), lang("add")); ?></th>

</tr>
<?php 
$roles = $data['roles'];
?>
<?php
if (count($roles) > 0):
foreach($roles as $group):
//class_name($user);
?>
<tr>
<td><?php echo $group->id;?></td>
<td><?php echo $group->name;?></td>
<td><?php echo form_button(array('type'=>'link', 'class'=>'edit', 'icon'=>'edit', 'href'=> site_url("/admin/roles/edit")."/".$group->id), lang('edit')); ?></td>
<td><?php echo form_button(array('type'=>'link', 'class'=>'edit', 'icon'=>'delete', 'href'=> site_url("/admin/roles/delete")."/".$group->id), lang('delete')); ?></td>
</tr>

<?php
endforeach;
endif;?>
</table>
<?php endif;?>
