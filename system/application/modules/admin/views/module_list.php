<?php
@$avail = $data['available'];
///$avail = $available;
$db = @$data['db'];

//print_r($db->toArray());
$db_arr = array();
foreach(@$db as $m):
	$db_arr[$m->fragment] = $m->fragment;
endforeach;
$avail_arr = array();
//set allmodules in a flat array
$avail_arr[] = $avail;
/*foreach($avail as $module){
	print_r($module);
	$avail_arr[$module->fragment] = $module;
}*/

function displayModule($parentID, $mod, &$db_arr, $level=1){
	$indb = isset($db_arr[$mod->fragment]);
	//if ($mod =="" || !isset($mod->fragment)) return;
	//if($mod->id == $parentID) return FALSE;//DANGER! Will Robinson. parent == child? could cause infinite loop.
	if($level < 3) $indent =1; else $indent = $level - 1;
	echo "<tr><td>";
	for($cnt=0; $cnt < $indent; $cnt++){
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	echo "<font ".(!$indb? " style='color: #FF0000;'":"").">";
	echo $mod->fragment;
	echo "</font>";
	echo " [<a href='".site_url()."/".$mod->fragment."' target='_BLANK'>Go</a>]";
	$escaped = str_replace("/", "-", $mod->fragment);
	echo "</td><td>". form_button((array('type'=>'link', 'icon'=>'edit', 'href'=>site_url("/admin/usermodules/edit")."/{$escaped}")), lang('edit'));
	echo ($indb ? "<td><input type=\"checkbox\"  class=\"remove_checkbox\" value=\"{$escaped}\" name=\"remove[]\" />".
	form_button(array('type'=>'link', 'icon'=>'delete', 'href'=>site_url("/admin/usermodules/delete")."/".str_replace("/", "-", $mod->fragment)), lang('delete')):"<td></td>");
	echo ($indb ? "<td></td>":"<td><input type=\"checkbox\" class=\"add_checkbox\" value=\"{$escaped}\" name=\"add[]\" />".
	form_button(array('type'=>'link', 'icon'=>'add', 'href'=>site_url("/admin/usermodules/add")."/".str_replace("/", "-", $mod->fragment)), lang('add'))."</td>")."</tr>\n";
	foreach($mod->Children as $child){
		displayModule($mod->id, $child, $db_arr, $level+1);
	}
}


//print_r($avail->Children->toArray(true));

?>
<div class="error">
<?php echo validation_errors(); 
if ($data['errors']) foreach($data['errors'] as $error):
?><p><?php echo $error;?></p>
<?php endforeach;?>
</div>
<?php
if($data['messages']):
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
?>
<table class="datagrid">
<tr><th><?php echo lang('location');?></th><th><?php echo lang('edit');?></th><th><input type="checkbox" id="delete_checkbox_master" /></th>
<th><input type="checkbox" id="add_checkbox_master" /></th></tr>
<?php foreach($avail_arr as $mod){
	displayModule("", $avail, $db_arr);
}
?>
</table>

<div>
<?php
echo form_button(array('type'=>'submit', 'icon'=>'save'), lang('save'));
?>	</div>
</form>
<script type="text/javascript">
$(document).ready(function(){
	$('#delete_checkbox_master').change(function(){
//		alert('changed!');
		if ($('#delete_checkbox_master').attr('checked')){
			$('input.remove_checkbox').attr('checked', 'checked');
		} else {
			$('input.remove_checkbox').removeAttr('checked');			
		}
	});
	$('#add_checkbox_master').click(function(){
//		alert('clicked!');
		if ($('#add_checkbox_master').attr('checked')){
			$('input.add_checkbox').attr('checked', 'checked');
		} else {
			$('input.add_checkbox').removeAttr('checked');			
		}
	});
	
});
</script>
