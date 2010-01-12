<?php	

if ($data->count() < 1 ): ?>
<div class="candidate">
No Candidate Information here.
</div>
<?php
 else:
?><form name="candidate_edit" action="" method="post">

<?php  	$arr = $data;


 $candidateIdList = array();
 $quit = false;
 foreach ($data as $candidate):
 	if ($quit) break;

 	if (!isset($candidate->id)) {$candidate = $data; $quit = true;}
 	$candidateIdList[] = $candidate->id;
 	?>
 <div class="candidateinfo">
<table>
<tr class="recordrow"><td class="label">Candidate ID</td><td><?=$candidate->id?></td></tr>
<tr class="recordrow"><td class="label">Name</td><td><input type="text" value="<?php echo $candidate->Contact->firstname;?>" name="contact_firstname_<?=$candidate->id?>" /></td></tr>
<tr class="recordrow"><td class="label">Surname</td><td><input type="text" value="<?php echo $candidate->Contact->surname; ?>" name="contact_surname_<?=$candidate->id?>" /></td></tr>
<tr class="recordrow"><td class="label">Gender</td><td><select name="gender_<?=$candidate->id?>"><option value=""></option><option value="m">Male</option><option value="f">Female</option></select></td></tr>
<tr class="recordrow"><td class="label">Marital Status</td><td><select name="maritalstatus_<?=$candidate->id?>"><option value=""></option></select></td></tr>
<tr class="recordrow"><td class="label">Date of Birth</td><td><input type="text" class="date" value="<?php echo $candidate->dateofbirth;?>" name="dob_<?=$candidate->id?>"/></td></tr>
<tr class="recordrow"><td class="label">Nationality</td><td><input type="text" value="<?php echo $candidate->nationality;?>" name="nationality_<?=$candidate->id?>"/></td></tr>
<tr class="recordrow"><td class="label">Country of Residence</td><td><input type="text" value="<?php echo $candidate->residence;?>" name="residence_<?=$candidate->id?>"/></td></tr>
<tr class="recordrow"><td class="label">Ethnicity</td><td><select name="ethnicity_<?=$candidate->id?>"><option value=""></option></select></td></tr>
<tr><td colspan="2">
	  <div class="addresscontainer">
	<div class="sectionheader"><div class="sectiontitle">Address Information</div> <div class="sectionicons"><img class="headericon right" src="<?php echo base_url()?>/images/icons/close_16.png"></div></div>
	<?php if ($candidate->Contact)
	$this->load->view("address_edit", array("addresses"=>$candidate->Contact->Addresses));
 ?>

</div>
</td></tr>
</table>
</div>
<?php
endforeach;

endif;
?>
<input type="hidden" name="candidate_ids" value="<?php echo implode(",", $candidateIdList); ?>" />
</form>