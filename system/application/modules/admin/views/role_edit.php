<?php
@$role = $data['role'];

$selected = array();
foreach(@$role->Modules as $m):
	$selected[$m->id] = $m->id;
endforeach;

$q = Doctrine_Query::create()
	->from('Module m')
	->orderBy('m.fragment');

$modules = $q->execute();
$m_arr = array();
//set allmodules in a flat array
foreach($modules as $module){
	$m_arr[$module->id] = $module;
}
//link modules to parents
foreach($m_arr as $module){
	if ($module->parent_id != "")
		$m_arr[$module->parent_id]->Children[] = $module;
}
//remove non-root nodes from array
foreach($m_arr as $module){
	if ($module->parent_id != "")
	unset($m_arr[$module->id]);
}

function displayModule($parentID, $mod, &$selected, $level=1){
	if ($mod =="" || !isset($mod->id)) return;
	if($mod->id == $parentID) return FALSE;//DANGER! Will Robinson. parent == child? could cause infinite loop.
	echo "<tr><td>";
	for($cnt=0; $cnt < $level; $cnt++){
		echo "&nbsp;&nbsp;";
	}
	echo "<input type=\"checkbox\" name=modules[] value=\"$mod->id\"".
			(isset($selected[$mod->id])? " checked=\"checked\"":"")."/>";
	echo $mod->fragment;
	echo "</td><td>$mod->text</td>";
	echo "<td>$mod->description</td></tr>";
	foreach($mod->Children as $child){
		displayModule($mod->id, $child, $selected, $level+1);
	}
}

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

if (isset($role) && is_object($role)) :
	echo form_hidden(array("role_id"=>$role->id, "action"=>"save"));
endif;
?>
<fieldset>
<label for="name"><?php echo lang('role');?></label>
<input type="text" name="name" value="<?php echo set_value('name', $role->name); ?>" size="50" />
</fieldset>
<fieldset>
<label for="description"><?php echo lang('description');?></label>
<textarea name="description" maxlength="255"><?php echo htmlspecialchars(set_value('description', $role->description)); ?></textarea>
</fieldset>
<fieldset>
<table class="datagrid">
<tr><th>Fragment</th><th>Name</th><th><?php echo lang('description');?></th></tr>
<?php foreach($m_arr as $mod){
	displayModule("", $mod, $selected);
}
?>
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