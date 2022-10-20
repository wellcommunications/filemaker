<?php if(!empty($details)): ?>
	
	<!-- ================ -->
	<!-- = REGISTRATION = -->
	<!-- ================ -->
	<h2>Registratie gegevens</h2>
	<table class="details">
		
		<tr>
			<td width="130">
				<?= (!empty($details->domains) && count($details->domains)>1) ? 'Domeinnamen' : 'Domeinnaam'; ?>
			</td>
			<td>
				<?php
					if(!empty($details->domains)){
						foreach($details->domains as $dom){
							if(!empty($dom->domain_id)) echo '<a href="' . site_url('domains/details/' . $dom->domain_id)  . '" title="Bekijk details">';
							echo (!empty($dom->domain_full)) ? $dom->domain_full : $dom->domain_name;
							if(!empty($dom->domain_id)) echo '</a>';
							echo "<br />";
						}
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>Registrant</td>
			<td><?= $details->contact_toString(FALSE) ?></td>
		</tr>
		
		<tr>
			<td>Verlenging</td>
			<td><?= $details->month_full ?></td>
		</tr>

		<?php if($details->creation_date):	?>
			<tr>
				<td>Geregistreerd op</td>
				<td><?= $details->creation_date ?></td>
			</tr>
		<?php endif; 	?>
		
		
		<?php if($details->deletion_date):	?>
			<tr class"error">
				<td>Verwijderen op</td>
				<td><?= $details->deletion_date ?> <img src="img/icons/exclamation.png" class="icon" /></td>
			</tr>
		<?php endif; 	?>
		
	</table>
	
	<br />
	<h2>Hosting pakket</h2>
	<table>
		<tr>
			<td>Pakket</td>
			<td width="160"><?= (isset($details->pakket->size)) ? $details->pakket->size : 'Geen hostingpakket' ?></td>
			<td>&euro; <?= number_format($details->pakket->price, 2, ',', '.') ?></td>
			<td class="info"><img src="img/icons/information.png" title="<?= (isset($details->pakket->size)) ? $details->pakket->specs : 'Deze klant heeft geen hostingpakket bij ons' ?>" /></td>
		</tr>

		<?php if(!empty($details->addons)): 
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
			<?php $i++;
				endforeach; ?>
		<?php endif; ?>
		
		
		<tr class="totaal">
			<td colspan="2">Totaal</td>
			<td colspan="2">&euro; <?= $details->get_price() ?></td>
		</tr>
	</table>
	
	<br />
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
		<a href="<?= site_url('hostings/edit/' . $details->id) ?>" class="button">Wijzig gegevens</a>
	<?php endif;?>
	<a href="<?= site_url('hostings/overview/') ?>" class="button">Naar overzicht hostings</a>
	
<?php else:	?>
	
	<h2>Geen details gevonden voor deze hosting.</h2>
	
<?php endif;?>