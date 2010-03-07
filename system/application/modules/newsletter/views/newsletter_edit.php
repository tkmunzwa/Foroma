<?php
@$newsletter = $data['newsletter'];
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

$newsletter_permissions = array();
if (isset($newsletter) && is_object($newsletter)) :
	echo form_hidden(array("newsletter_id"=>$newsletter->id, "action"=>"save"));
endif;
?>
<fieldset>
<div><?php
echo form_button(array('icon'=>'save'),lang("save"));
echo form_button(array('icon'=>'cancel', 'href'=>site_url("/newsletter"), 'type'=>'link'),lang("cancel"));
?>
</div>
</fieldset>
<fieldset>
<label for="name"><?php echo lang('name');?></label>
<input type="text" name="name" value="<?php echo set_value('name', $newsletter->name); ?>" size="50" />
</fieldset>

<fieldset>
<label><?php echo lang('description');?></label>
<textarea name="description"><?php echo set_value('description', $newsletter->description);?></textarea>
</fieldset>
<fieldset>
<div><?php
echo form_button(array('icon'=>'save'),lang("save"));
echo form_button(array('icon'=>'cancel', 'href'=>site_url("/newsletter"), 'type'=>'link'),lang("cancel"));
?>
</div>
</fieldset>
</form>