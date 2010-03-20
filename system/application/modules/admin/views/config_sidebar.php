<?php
   $modules = $data['modules'];
   $selected = @$data['selected'];
?>
<div class="secheader">Modules</div>
<?php foreach($modules as $m):?>
<div class="module<?php if ($m->fragment ==$selected) echo " selected";?>">
	<?php echo "<a href=\"".site_url('admin/config/settings/'.$m->text)."\">".$m->description."</a>";?>
</div>
<?php endforeach;?>
