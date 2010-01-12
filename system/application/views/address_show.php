<?php
if (!isset($addresses) || $addresses->count() < 1):?>
	<div class="addressinfo">
		<div class="recordrow"><span class="notice">No address information</span></div>
	</div>
<?php else:
	foreach($addresses as $address):?>
	<div class="addressinfo">
		<div class="recordrow"><span class="label">Street:</span><?=$address->street?></div>
	</div><?php endforeach;?>
<?php endif; ?>