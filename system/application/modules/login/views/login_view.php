<?php if(isset($errors) && count($errors) > 0): ?>
<ul class="error">
<?php foreach($errors as $error):?>
<li><?=$error?>
<?php endforeach;?>
</ul>
<?php endif;?>
<?php if(isset($messages) && count($messages)>0): ?>
<ul class="info">
<?php foreach($messages as $msg):?>
<li><?=$msg?>
<?php endforeach;?>
</ul>
<?php endif;?>
<form method="post" action="<?php echo site_url("login/log_in"); ?>" >
<fieldset>
<label for="username" >
<span class="label">Username</span></label> <input type="text" name="username" id="username" /> 
<label for="password" >
<span class="label"> Password</span></label> <input type="password" name="password" id="password" /> 
<?php if (isset($redirect)):?>
<input type="hidden" name="redirect" id="redirect"  value="<?=$redirect?>" />
<br/>
<?php endif;
echo form_button(array(), "Login");
?>
</fieldset>
</form>
