<?php
	$this->load->library('fo_user');
	$user = $this->fo_user->getUser();
	$items = array();
	$sorted = array();
	if ($user){
		foreach($user->Groups as $group) {
			foreach($group->Permissions as $perm){
				foreach($perm->Modules as $mod){
					if ($mod->parent_id != "") continue;//echo $mod->parent_id."<br/>";
					if ($mod->onmenu) {
						$items[$mod->id] = $mod;
						$sorted[$mod->id] = $mod->menuposition;
						asort($sorted); //sort by menu position
					}
				}
			}
		}
	}
?>
<ul id="menu">
<?php
	//var_dump($res);
	foreach($sorted as $k=>$v):
		$item = $items[$k];
?>
<li class="mega">
<a href="<?php echo site_url($item->fragment);?>"><span id="icon"><img src="<?php echo base_url()."images/icons/".$item->icon;?>" /></span>
<span><?php echo $item->text;?></span></a>
<?php if (count ($item->Children) > 0): ?>
<!-- insert drop down graphic here/hint that hover shows more -->
<div>
	<?php foreach($item->Children as $child): 
	if (!$child->onmenu) continue; ?>
	<span class="submenu"><a href="<?php echo site_url($child->fragment);?>"><span title="<?php echo $child->hovertext;?>"><?php echo $child->text;?></a></span></a>
	<span class="description"><?php echo $child->description;?></span>
	</span>
	<? endforeach; ?>
</div>
<?php endif; ?>
</li><?php endforeach; ?>
</ul>