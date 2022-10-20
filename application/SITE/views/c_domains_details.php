<?php if(!empty($details)): ?>
	
	<!-- ================ -->
	<!-- = REGISTRATION = -->
	<!-- ================ -->
	<h2>Registratie gegevens</h2>
	<table class="details">
		
		<tr>
			<td width="130">Domeinnaam</td>
			<td><?= $details->domain ?></td>
		</tr>
		
		<tr>
			<td>TLD</td>
			<td><?= $details->extension . ' (' . $details->extension_desc . ')'?></td>
		</tr>
		
		<tr>
			<td>Registrant</td>
			<td><?= $details->contact_toString(FALSE) ?></td>
		</tr>
		
		<?php if(!empty($details->reseller)):	?>
		<tr>
			<td>Reseller</td>
			<td>
				<?= $details->reseller->firstname . " " . $details->reseller->lastname?><br />
				<?= $details->reseller->company ?>
				<?= (!empty($details->reseller->email)) ? "<br />" . safe_mailto($details->reseller->email) : '' ?>
			</td>
		</tr>
		<?php endif; 	?>
		
		<tr>
			<td>Prijs</td>
			<td>
				<?php 
					if($details->intern){
						echo "intern <br />";
					}elseif($details->pakket){
						echo "Pakket/Webdirect<br />";
					}
					
					if(isset($details->price)) echo "&euro; " . number_format($details->price, 2, ',', '.');
				?>
			</td>
		</tr>
		
		<tr>
			<td>Verlenging</td>
			<td><?= $details->month_full ?></td>
		</tr>
		
		<?php if($details->registration_date):	?>
			<tr>
				<td>Geregistreerd op</td>
				<td><?= $details->registration_date ?></td>
			</tr>
		<?php endif; 	?>
		
		
		<?php if($details->deletion_date):	?>
			<tr class"error">
				<td>Verwijderen op</td>
				<td><?= $details->deletion_date ?> <img src="img/icons/exclamation.png" class="icon" /></td>
			</tr>
		<?php endif; 	?>
		
		
		<?php if(!empty($hosting)): ?>
			<tr>
				<td>Hostingpakket</td>
				<td>
					<a href="<?= site_url('hostings/details/' . $hosting->id) ?>" title="Details hostingspakket">
						<?= $hosting->domain_full ?>
					</a>
				</td>
			</tr>
			
		<?php endif;?>
		
	</table>
	
	
	<!-- ========== -->
	<!-- = ADDONS = -->
	<!-- ========== -->
	<?php if(!empty($details->addons)): 	?>
	<br />
	<h2>Addons</h2>
	<table>
		<?php	
			$i = 0;
			foreach($details->addons as $add): ?>
			
				<tr class="addons">
			
					<?php if($i==0): ?>
						<td width="130" rowspan="<?= count($details->addons) ?>">Add-ons</td>
					<?php endif; ?>
				
					<td><?= $add->name; ?></td>
					<td><?php if(!empty($add->price)): ?>&euro; <?= number_format($add->price, 2, ',', '.') ?><?php endif; ?></td>
					<td class="info"><img src="img/icons/information.png" title="<?= $add->description ?>" /></td>
				</tr>
		<?php 
			$i++;
			endforeach; 
		?>
		
		
		<tr class="totaal">
			<td colspan="2">Totaal addons</td>
			<td colspan="2"><?= $details->get_price() ?></td>
		</tr>
	</table>
	<?php endif;	?>
	
	
	
	<!-- =========== -->
	<!-- = REMARKS = -->
	<!-- =========== -->
	<?php if(!empty($details->remarks)):	?>
		<br /><br />
		<h2>Opmerkingen</h2>
		<p class="details"><?= nl2br(str_replace('', "\n", $details->remarks)) ?></p>
	<?php endif; 	?>
	
	
	<!-- ========== -->
	<!-- = LOGINS = -->
	<!-- ========== -->
	<?php if(!empty($details->logins)): 	?>
		<br />
		<h2>Logins</h2><br />
		<table class="skinned">
			<tr>
				<th>Type</th>
				<th>Host</th>
				<th>Gebruikersnaam</th>
				<th>Paswoord</th>
				<th>Opmerkingen</th>
			</tr>
		
			<?php foreach($details->logins as $login):?>
			
				<tr title="<?= $login->login_desc ?>">
					<td><?= $login->login_type ?></td>
					<td><?= $login->host ?></td>
					<td><?= $login->user ?></td>
					<td><?= $login->pass ?></td>
					<td><?= $login->remarks ?></td>
				</li>
			
			<?php endforeach;?>
		</table>
		
	<?php endif;	?>
	
	
	<br /><br /><br />
	<!-- =========== -->
	<!-- = BUTTONS = -->
	<!-- =========== -->
	<?php if($this->session->userdata('crrUser')->allow_edit==1): ?>
		<a href="<?= site_url('domains/edit/' . $details->id) ?>" class="button">Wijzig gegevens</a>
	<?php endif;?>
	<a href="<?= site_url('domains/overview/') ?>" class="button">Naar overzicht domeinnamen</a>
	
<?php else:	?>
	
	<h2>Geen details gevonden voor deze domeinnaam.</h2>
	
<?php endif;?>