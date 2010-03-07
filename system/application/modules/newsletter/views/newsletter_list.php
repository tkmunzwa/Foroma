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
<th><?php echo form_button(array('href'=> site_url('newsletter/create'), 'type'=>'link', 'icon'=>'add'), lang("add")); ?></th>

</tr>
<?php 
$newsletters = $data['newsletters'];
?>
<?php
if (count($newsletters) > 0):
foreach($newsletters as $nl):
//class_name($user);
?>
<tr>
<td><?php echo $nl->id;?></td>
<td><?php echo $nl->name;?></td>
<td><?php echo form_button(array('type'=>'link', 'class'=>'edit', 'icon'=>'edit', 'href'=> site_url("/newsletter/edit")."/".$nl->id), lang('edit')); ?></td>
<td><?php echo form_button(array('type'=>'link', 'class'=>'edit', 'icon'=>'delete', 'href'=> site_url("/newsletter/delete")."/".$nl->id), lang('delete')); ?></td>
</tr>

<?php
endforeach;
endif;?>
</table>
<?php endif;?>
