<?php	

if ($data->count() < 1 ): ?>
<div class="candidate">
No Candidate Information here.
</div>
<?php
 else: 
 	$arr = $data;


 foreach ($data as $candidate):
 if (!$candidate) continue;
 	?>
 <div class="candidateinfo">
<table>
<tr class="recordrow"><td class="label">Candidate ID</td><td><?=$candidate->id?> <a href="edit/<?=$candidate->id?>"><img class="editlink inlineimage" src="<?php echo base_url()?>/images/icons/edit16.png" /></a></td></tr>
<tr class="recordrow"><td class="label">Name</td><td><? echo $candidate->Contact->firstname ?></td></tr>
<tr class="recordrow"><td class="label">Surname</td><td><?=$candidate->Contact->surname?></td></tr>
<tr class="recordrow"><td class="label">Gender</td><td><?=$candidate->gender?></td></tr>
<tr class="recordrow"><td class="label">Nationality</td><td><?=$candidate->nationality?></td></tr>
<tr><td colspan="2">
  <div class="addresscontainer">
	<div class="sectionheader"><div class="sectiontitle">Address Information</div> <div class="sectionicons"><img class="headericon right" src="<?php echo base_url()?>/images/icons/close16.png"></div></div>
	<?php if ($candidate->Contact)
	$this->load->view("address_show", array("addresses"=>$candidate->Contact->Addresses));
 ?>

</div>
</td></tr>
</table>
</div>
<?php
endforeach;

endif;?>

