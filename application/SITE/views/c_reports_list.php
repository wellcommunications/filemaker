<!-- ============ -->
<!-- = HOSTINGS = -->
<!-- ============ -->
<?php
	
	$action_delete = FALSE;
	if($this->uri->segment(2)=="delete"){
		$action_delete = TRUE;
	}

?>

<h2>Hostings</h2>
<?php if(!empty($hostings)):	?>
	<br />
	<table class="skinned">
		<tr>
			<th>Hosting <span>(totaal: <?= count($hostings) ?>)</span></th>
			<th>Contact</th>
			<th>Pakket</th>
			<?php if($action_delete): ?>
			<th>Verwijderen</th>
			<?php else:	?>
			<th>Hernieuwen</th>
			<?php endif;?>
			<th></th>
			<th></th>
			<th></th>
		</tr>
		
		<?php foreach($hostings as $hosting):	?>
			<tr class="<?= ($action_delete && $hosting->deletion_countdown()<=3) ? 'warn' : '' ?>">
				<td class="<?= ($action_delete && $hosting->deletion_countdown()<0) ? 'late' : '' ?>">
					<?php if($action_delete && $hosting->deletion_countdown()<0):	?>
						<img src="img/icons/exclamation.png" title="Deze domeinnaam moest al verwijderd zijn!" />
					<?php endif; ?>
					<?= $hosting->domain_full ?>
				</td>
				<td>
					<?= $hosting->contact_toString() ?>
				</td>
				<td><?= $hosting->pakket->size ?></td>
				<td>
					<?php 
						if($action_delete){
							echo $hosting->deletion_date;
							echo ($hosting->deletion_countdown()<0) ? ' <span class="late">(' : ' <span>(';
							echo $hosting->deletion_countdown();
							echo ($hosting->deletion_countdown()==0) ? ' dag)</span>' : ' dagen)</span>';
						}else{
							echo $hosting->month_full;
						}
					?>
				</td>
				<td><?= ($hosting->hasMySQL) ? '<img src="img/icons/database.png" alt="Database" title="MySQL database" />': '' ?></td>
				<td><a href="<?= site_url('hostings/details/' . $hosting->id) ?>" class="details" title="Details"><img src="img/icons/magnifier.png" alt="Details" /></a></td>
				<td>
					<?php if($this->session->userdata('crrUser')->allow_remove==1): ?>
						<a href="<?= site_url('hostings/delete/' . $hosting->id) ?>" class="delete" title="Verwijderen"><img src="img/icons/delete.png" alt="Verwijderen" /></a>
					<?php endif; 	?>
				</td>
			</tr>
			
		<?php endforeach; ?>
		
	</table>
	
<?php else:	?>
	
	<?php if($action_delete):	?>
		
		<p>Geen hostings te verwijderen binnen de <?= DELETION_DATE_DAYS_AHEAD ?> dagen.</p>

		
	<?php else:		?>
		
		<p>Geen hostings te verlengen deze maand</p>
		
	<?php endif;	?>
	
<?php endif; ?>

<br /><br /><br />

<!-- =============== -->
<!-- = DOMAINNAMES = -->
<!-- =============== -->
<h2>Domeinnamen</h2>

<?php if(!empty($domains)):	?>
	
	<br />
	<table class="skinned">
		<tr>
			<th>Domeinnaam <span>(totaal: <?= count($domains) ?>)</span></th>
			<th>Contact</th>
			<?php if($action_delete): ?>
			<th>Verwijderen</th>
			<?php else:	?>
			<th>Hernieuwen</th>
			<?php endif;?>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
		
		<?php foreach($domains as $domain):	?>
			<tr class="<?= ($action_delete && $domain->deletion_countdown()<=3) ? 'warn' : '' ?>">
				<td class="<?= ($action_delete && $domain->deletion_countdown()<0) ? 'late' : '' ?>">
					<?php if($action_delete && $domain->deletion_countdown()<0):	?>
						<img src="img/icons/exclamation.png" title="Deze domeinnaam moest al verwijderd zijn!" />
					<?php endif; ?>
					<?= $domain->domain_full ?>
				</td>
				<td>
					<?php 
						if(empty($domain->reseller)){
							echo $domain->contact_toString();
						}else{
							echo $domain->reseller_toString();
						}
					?>
				</td>
				<td>
					<?php 
						if($action_delete){
							echo $domain->deletion_date;
							echo ($domain->deletion_countdown()<0) ? ' <span class="late">(' : ' <span>(';
							echo $domain->deletion_countdown();
							echo ($domain->deletion_countdown()==0) ? ' dag)</span>' : ' dagen)</span>';
						}else{
							echo $domain->month_full;
						}
					?>
				</td>
				<td><?= $domain->get_price() ?></td>
				<td><?= ($domain->forward===TRUE) ? '<img src="img/icons/server_go.png" title="Forwarding Nameservers" alt="Forwarding nameservers" />' : '' ?></td>
				<td><a href="<?= site_url('domains/details/' . $domain->id) ?>" title="Details"><img src="img/icons/magnifier.png" alt="Details" /></a></td>
				<td>
					<?php if($this->session->userdata('crrUser')->allow_remove==1): ?>
					<a href="<?= site_url('domains/delete/' . $domain->id) ?>" title="Verwijderen"><img src="img/icons/delete.png" alt="Verwijderen" /></a>
					<?php endif; 	?>
				</td>
			</tr>
			
		<?php endforeach; ?>
		
	</table>
	
<?php else:		?>
	
	<?php if($action_delete):	?>
		
		<p>Geen domeinnamen te verwijderen binnen de <?= DELETION_DATE_DAYS_AHEAD ?> dagen.</p>
	
	<?php else:		?>
		
		<p>Geen hostings te verlengen deze maand</p>
		
	<?php endif;	?>
<?php endif;	?>