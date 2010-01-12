<?php

if(isset($errors)):
?><div class="error">
<?php 
 foreach($errors as $error):
?><p><?php echo $error;?></p>
<?php endforeach;?>
</div>
<?php endif;
if(isset($messages)):
?><div class="info">
<?php 
 foreach($messages as $msg):
?><p><?php echo $msg;?></p>
<?php endforeach;?>
</div>
<?php endif;?>

<table class="datagrid">
<tr>
<th>Username</th>
<th>Email</th>
<th>Edit</th>
<th><?php echo form_button(array('type'=>'link', 'icon'=>'add', 'href'=> site_url('admin/users/create')), lang('add')); ?></th>

</tr>
<?php
if (isset($data) && $data):
$users = $data;
?>
<?php
if (count($users) > 0):
foreach($users as $user):
//class_name($user);
?>
<tr>
<td><?php echo $user->username;?></td>
<td><?php echo $user->emailaddress	;?></td>
<td><?php echo form_button(array('type'=>'link', 'icon'=>'edit', 'href'=> site_url('/admin/users/edit')."/".$user->id), lang('edit')); ?></td>
<td><?php echo form_button(array('type'=>'link', 'icon'=>'delete', 'href'=> site_url('/admin/users/delete')."/".$user->id), lang('delete')); ?></td>
</tr>

<?php
endforeach;
endif;
endif;?>
</table>