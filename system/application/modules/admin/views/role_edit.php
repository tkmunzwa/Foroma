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
echo form_open($controller); 

if (isset($role) && is_object($role)) :
	echo form_hidden(array("role_id"=>$role->id, "action"=>"save"));
endif;
?>

<h5>rolename</h5>
<input type="text" name="name" value="<?php echo set_value('name', $role->name); ?>" size="50" />

<h5>Description</h5>
<textarea name="description" maxlength="255"><?php echo htmlspecialchars(set_value('description', $role->description)); ?></textarea>
<table class="datagrid">
<tr><th>Fragment</th><th>Name</th><th>Description</th></tr>
<?php foreach($m_arr as $mod){
	displayModule("", $mod, $selected);
}
?>
</table>

<div><?php
echo form_button(array('icon'=>'save'),lang("save"));
echo form_button(array('icon'=>'cancel', 'onclick'=>"history.go(-1);", 'type'=>'button'),lang("cancel"));
?>
</div>
</form>