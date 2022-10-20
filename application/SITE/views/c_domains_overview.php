<?php if(!empty($feedback)):?>
	<div class="feedback <?= $feedback->type ?>">
		<?php if(isset($feedback->title)):?>
			<h2><?= $feedback->title ?></h2>
		<?php endif; ?>
		<p><?= $feedback->message ?></p>
	</div>
<?php endif; ?>
	
	
<?php if(!empty($domainnames)): ?>
	
	<table class="skinned">
		<tr>
			<th>Domeinnaam <span>(totaal: <?= count($domainnames) ?>)</span></th>
			<th>Contact</th>
			<th>Hernieuwen</th>
			<th>Prijs</th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
		
		<?php foreach($domainnames as $domain):	?>
			<tr>
				<td><?= $domain->domain_full ?></td>
				<td>
					<?php 
						if(empty($domain->reseller)){
							echo $domain->contact_toString();
						}else{
							echo $domain->reseller_toString();
						}
					?>
				</td>
				<td><?= $domain->month_full ?></td>
				<td><?= $domain->get_price() ?></td>
				<td><?= ($domain->forward===TRUE) ? '<img src="img/icons/server_go.png" title="Forwarding Nameservers" alt="Forwarding nameservers" />' : '' ?></td>
				<td><a href="<?= site_url('domains/details/' . $domain->id) ?>" class="details" title="Details"><img src="img/icons/magnifier.png" alt="Details" /></a></td>
				<td>
					<?php if($this->session->userdata('crrUser')->allow_remove==1): ?>
					<a href="<?= site_url('domains/delete/' . $domain->id) ?>" class="delete" title="Verwijderen"><img src="img/icons/delete.png" alt="Verwijderen" /></a>
					<?php endif; 	?>
				</td>
			</tr>
			
		<?php endforeach; ?>
		
	</table>
	
<?php endif; ?>