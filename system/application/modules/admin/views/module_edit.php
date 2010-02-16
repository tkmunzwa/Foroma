<?php if (validation_errors()):?>
<div class="error">
<?php echo validation_errors(); ?>
</div>
<?php endif ?>
<?php
#var_dump($data['module']->fragment);

$controller = "";
if (isset($data) && isset($data['controller'])) $controller = $data['controller'];
echo form_open($controller); 
@$module = $data['module'];
#			var_dump($module);
?>
<input type="hidden" name="action" value="save" />
<fieldset>
<label for="fragment"><?php echo lang("fragment");?></label>
<input type="text" name="fragment" value="<?php echo set_value('fragment', $module->fragment); ?>" size="50" />
</fieldset>
<input type="hidden" name="old_fragment" value="<?php echo $module->fragment; ?>" />
<fieldset>
<label for="name"><?php echo lang("name");?></label>
<input type="text" name="name" id="name" value="<?php echo set_value('name', $module->name); ?>" size="50" />
</fieldset>
<fieldset>
<label for="description"><?php echo lang("description");?></label>
<input type="text" name="description" id="description" value="<?php echo set_value('description', $module->description); ?>" size="50" />
</fieldset>
<fieldset>
<label for="icon"><?php echo lang("icon");?></label>
<input id="icon" type="text" name="icon" value="<?php echo set_value('icon', $module->icon); ?>" size="50" />
</fieldset>
<fieldset>
<label for="parent"><?php echo lang("parent");?></label>
<select name="parent" id="parent">
<option value="<null>"><?php echo lang("none");?></option>
<?php
$modules = Doctrine::getTable('Module')->findAll();
foreach($modules as $current):
?>
<option value="<?php echo $current->id; ?>" <?php if ($module->parent_id == $current->id) echo " selected=\"selected\""?> ><?php echo $current->fragment;?></option>
<?php endforeach;?>
</select>
</fieldset>
<fieldset>
<label for="menu"><?php echo lang("on_menu");?></label>
<input type="checkbox"  name="onmenu" value="1" id="onmenu" <?php if ($module->onmenu) echo 'checked="checked"';?> />
</fieldset>
<fieldset>
<label for="menuposition"><?php echo lang("menu_position");?></label>
<input id="menuposition" type="text" name="menuposition" value="<?php echo set_value('menu_pos', $module->menuposition); ?>" size="50" />
</fieldset>
<fieldset>
<label for="text"><?php echo lang("text");?></label>
<input id="text" type="text" name="text" value="<?php echo set_value('text', $module->text); ?>" size="50" />
</fieldset>
<fieldset>
<label for="hovertext"><?php echo lang("hovertext");?></label>
<input id="hovertext" type="text" name="hovertext" value="<?php echo set_value('hovertext', $module->hovertext); ?>" size="50" />
</fieldset>
<fieldset>
<div><?php
echo form_button(array('icon'=>'save'),lang("save"));
echo form_button(array('icon'=>'cancel', 'href'=>site_url("/admin/usermodules/listall"), 'type'=>'link'),lang("cancel"));
?></div>
</fieldset>
<?php echo form_close();?>