<div class="error">
<?php echo validation_errors(); ?>
</div>
<?php
#var_dump($data['module']->fragment);

$controller = "";
if (isset($data) && isset($data['controller'])) $controller = $data['controller'];
echo form_open($controller); 
@$module = $data['module'];
#			var_dump($module);
?>
<input type="hidden" name="action" value="save" />
<label>Fragment</label>
<input type="text" name="fragment" value="<?php echo set_value('fragment', $module->fragment); ?>" size="50" />
<input type="hidden" name="old_fragment" value="<?php echo $module->fragment; ?>" />

<label for="name">Name</label>
<input type="text" name="name" id="name" value="<?php echo set_value('name', $module->name); ?>" size="50" />

<label for="description">Description</label>
<input type="text" name="description" id="description" value="<?php echo set_value('description', $module->description); ?>" size="50" />

<label for="icon">Icon</label>
<input id="icon" type="text" name="icon" value="<?php echo set_value('icon', $module->icon); ?>" size="50" />

<label for="parent">Parent</label>
<select name="parent" id="parent">
<option value="<null>">[None]</option>
<?php
$modules = Doctrine::getTable('Module')->findAll();
foreach($modules as $current):
?>
<option value="<?php echo $current->id; ?>" <?php if ($module->parent_id == $current->id) echo " selected=\"selected\""?> ><?php echo $current->fragment;?></option>
<?php endforeach;?>
</select>

<label for="menu">On menu?</label>
<input type="checkbox"  name="onmenu" value="1" id="onmenu" <?php if ($module->onmenu) echo 'checked="checked"';?> />

<label for="menuposition">Menu Position</label>
<input id="menuposition" type="text" name="menuposition" value="<?php echo set_value('menu_pos', $module->menuposition); ?>" size="50" />

<label for="text">Text</label>
<input id="text" type="text" name="text" value="<?php echo set_value('text', $module->text); ?>" size="50" />

<label for="hovertext">Hovertext</label>
<input id="hovertext" type="text" name="hovertext" value="<?php echo set_value('hovertext', $module->hovertext); ?>" size="50" />

<div><?php
echo form_button(array('icon'=>'save'),lang("save"));
echo form_button(array('icon'=>'cancel', 'onclick'=>"window.location.href= '".site_url("/admin/usermodules/listall")."'", 'type'=>'button'),lang("cancel"));
?></div>
<?php echo form_close();?>