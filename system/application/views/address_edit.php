<?php
if (!isset($addresses) || $addresses->count() < 1):?>
	<div class="addressinfo">
		<div class="recordrow"><span class="notice">No address information</span></div>
	</div>
<?php else:
	$addressIdList = array();
	foreach($addresses as $address):
	$addressIdList[] = $address->id; ?>
	<table class="addressinfo">
		<tr class="recordrow"><td class="label">Street:</td><td><input type="text" value="<?=$address->street?>" name="street_<?php echo $address->id?>" /></td></tr>
	</table>
	<?php endforeach;?>
	<input type="hidden" id="address_ids" name="address_ids" value="<?php echo implode(",", $addressIdList); ?>" />
	<?php endif; ?>