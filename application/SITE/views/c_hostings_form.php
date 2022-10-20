<?php 
	
	$action_edit = ($this->uri->segment(2)=="edit");
	
	if(!empty($details)): ?>
	
	<?php if(!empty($feedback)):?>
		<div class="feedback <?= $feedback->type ?>">
			<?php if(isset($feedback->title)):?>
				<h2><?= $feedback->title ?></h2>
			<?php endif; ?>
			<p><?= $feedback->message ?></p>
		</div>
	<?php endif; ?>
	
	<form id="frmHosting" action="<?= site_url($this->uri->uri_string()) ?>" method="POST">
		<!-- ================ -->
		<!-- = REGISTRATION = -->
		<!-- ================ -->
		<h2>Registratiegegevens</h2>
		<table class="details">
			
			<?php if(!$action_edit):	?>
			<tr>
				<td>Domeinnaam</td>
				<td>
					<input type="text" class="meta" name="domainname" value="<?= set_value('domainname') ?>" rel="Domeinnaam (zonder extensie)" /> <span class="req">*</span>
					<?php if(form_error('domainname')): ?><img src="img/icons/exclamation.png" class="icon_error" title="<?= strip_tags(form_error('domainname')) ?>" /><?php endif; ?><br />
				</td>
			</tr>
			
			<tr>
				<td>TLD</td>
				<td>
					<?= form_dropdown('extension_id', $extensions, (set_value('extension_id')) ? set_value('extension_id') : 0); ?><span class="req">*</span>
					<?php if(form_error('extension_id')): ?><img src="img/icons/exclamation.png" class="icon_error" title="<?= strip_tags(form_error('extension_id')) ?>" /><?php endif; ?><br />
				</td>
			</tr>
			<?php endif; ?>
			
			<tr>
				<td width="240">Klantengegevens</td>
				<td>
					<input type="text" class="meta" name="contact_firstname" value="<?= set_value('contact_firstname', (isset($details->contact->firstname)) ? $details->contact->firstname : '') ?>" rel="Voornaam" /> <span class="req">*</span>
					<?php if(form_error('contact_firstname')): ?><img src="img/icons/exclamation.png" class="icon_error" title="<?= strip_tags(form_error('contact_firstname')) ?>" /><?php endif; ?><br />
					
					<input type="text" class="meta" name="contact_lastname" value="<?= set_value('contact_lastname', (isset($details->contact->lastname)) ? $details->contact->lastname : '') ?>" rel="Familienaam" /> <span class="req">*</span>
					<?php if(form_error('contact_lastname')): ?><img src="img/icons/exclamation.png" class="icon_error" title="<?= strip_tags(form_error('contact_lastname')) ?>" /><?php endif; ?><br />
						
					<input type="text" class="meta" name="contact_company" value="<?= set_value('contact_company', (isset($details->contact->company)) ? $details->contact->company : '') ?>" rel="Firma" /> <span class="req">*</span>
					<?php if(form_error('contact_company')): ?><img src="img/icons/exclamation.png" class="icon_error" title="<?= strip_tags(form_error('contact_company')) ?>" /><?php endif; ?><br />
						
					<input type="text" class="meta" name="contact_email" value="<?= set_value('contact_email', (isset($details->contact->email)) ? $details->contact->email : '') ?>" rel="E-mail" />&nbsp;&nbsp;&nbsp;&nbsp;
					<?php if(form_error('contact_email')): ?><img src="img/icons/exclamation.png" class="icon_error" title="<?= strip_tags(form_error('contact_email')) ?>" /><?php endif; ?><br />
					
				</td>
			</tr>
		
			
			<tr>
				<td>Maand van verlenging</td>
				<td>
					<?php unset($details->arr_months[0]) ?>
					<?= form_dropdown('month', $details->arr_months, (set_value('month')) ? set_value('month') : $details->month); ?> <span class="req">*</span>
				</td>
			</tr>
		
		
			<tr>
				<td>Aangemaakt op</td>
				<td><input type="text" name="creation_date" class="date" value="<?= set_value('creation_date', $details->creation_date) ?>" /></td>
			</tr>
		
		
			<tr class"error">
				<td>Verwijderen op</td>
				<td><input type="text" name="deletion_date" class="date" value="<?= set_value('deletion_date', $details->deletion_date) ?>" /></td>
			</tr>
			
			
			<tr>
				<td>Pakket</td>
				<td>
					<?php 
						unset($details->arr_months[0]);
						if(!isset($details->pakket->id)) $details->pakket->id = 0;
					?>
					<?= form_dropdown('hosting_size', $hosting_sizes, (set_value('hosting_size')) ? set_value('hosting_size') : $details->pakket->id); ?> <span class="req">*</span>
				</td>
			</tr>
		</table>
		
		
		
		<!-- =========== -->
		<!-- = REMARKS = -->
		<!-- =========== -->
		<h2>Opmerkingen</h2>
		<textarea name="remarks"><?= str_replace('', "\n", $details->remarks) ?></textarea>
		
		
		<p class="req">* verplichte velden</p>

		
		<!-- =========== -->
		<!-- = BUTTONS = -->
		<!-- =========== -->
		<div class="buttons">
			<input type="hidden" name="placeholder" id="myPlaceholder" value="full" />
			<a href="<?= site_url('hostings/edit/' . $details->id) ?>" id="btnSubmit" rel="frmHosting" class="button">Opslaan</a>
			<?php if($this->session->userdata('crrUser')->allow_remove==1 && $action_edit): ?>
			<a href="<?= site_url('hostings/delete/' . $details->id) ?>" class="button delete">Hosting verwijderen</a>
			<?php endif;?>
			<a href="<?= site_url('hostings/details/' . $details->id) ?>" class="button">Annuleren</a>
		</div>
		
		
		<?php if($action_edit):	?>
		<br /><br /><br /><br />
		
		<!-- =============== -->
		<!-- = DOMAINNAMES = -->
		<!-- =============== -->
		<h2>Domainnamen</h2>
		<table class="details">
					
			<?php 	
				if(!empty($details)){
					$i = 0;
					foreach($details->domains as $dom){ 
			?>

						<tr class="addons">

							<?php if($i==0): ?>
					            <td rowspan="<?= count($details->domains) ?>">Gekoppelde <?= (!empty($details->domains) && count($details->domains)>1) ? 'domainnamen' : 'domainnaam'; ?></td>
							<?php endif; ?>

							<td><?= (!empty($dom->domain_full)) ? $dom->domain_full : $dom->domain_name ?></td>
							<td>
								<?php if(count($details->domains)>1): ?>
									<a href="<?= site_url('hostings/remove_domain/'.$dom->id . '/' . $details->id) ?>" class="delete" title="Verwijder domeinnaam"><img src="img/icons/link_delete.png" /></a></td>
								<?php endif; ?>
						</tr>
				
			<?php 
						$i++;
					}
				}
			?>
			
			<tr>
				<td width="200">Extra domeinnaam toevoegen</td>
				<td>
					<input type="text" class="meta clear" name="domainname" value="<?= set_value('domainname') ?>" rel="Domeinnaam" style="width:170px;" /> .
					<?= form_dropdown('extension_id', $extensions, (set_value('extension_id')) ? set_value('extension_id') : 0, 'style="width:50px;"'); ?>
				</td>
				<td>
					<button class="mini clear" name="postback" value="domain"><img src="img/icons/add.png" alt="Toevoegen" /></button>
				</td>
			</tr>
		</table>	
		<br />
		
		
		
		<br /><br />
		<h2>Add-ons</h2>
		<table>
			<?php if(!empty($details->addons)): 
					$i = 0;
					foreach($details->addons as $add): ?>

						<tr class="addons">

							<?php if($i==0): ?>
								<td rowspan="<?= count($details->addons) ?>" width="200">Toegevoegde add-ons</td>
							<?php endif; ?>

							<td><?= $add->name; ?></td>
							<td>
								<a href="<?= site_url('hostings/remove_addon/'.$add->conn_id.'/'.$details->id) ?>" class="delete">
									<img src="img/icons/brick_delete.png" />
								</a>
							</td>
						</tr>
				<?php $i++;
					endforeach; ?>
			<?php endif; ?>
			
			<tr>
				<td width="200">Extra add-on toevoegen</td>
				<td><?= form_dropdown('add_addon', $addons); ?></td>
				<td><button class="mini" name="postback" value="addon"><img src="img/icons/add.png" alt="Toevoegen" /></button></td>
			</tr>
			
		</table>
	
	
		<br /><br /><br />
		<!-- ========== -->
		<!-- = LOGINS = -->
		<!-- ========== -->
		<h2>Logins</h2><br />
		<table class="skinned">
			<tr>
				<th>Type</th>
				<th>Host</th>
				<th>Gebruiker</th>
				<th>Paswoord</th>
				<th>Opmerking</th>
				<th></th>
			</tr>
			
			<tr class="logins" id="editDummy" rel="">
				<td><?= form_dropdown('update_login_type', $login_types, '', 'class="update_login_type"'); ?></td>
				<td><input type="text" class="update_login_host" name="update_login_host" value="<?= set_value('login_host') ?>" rel="Host" /></td>
				<td><input type="text" class="update_login_user" name="update_login_user" value="<?= set_value('login_user') ?>" rel="Gebruikersnaam" /></td>
				<td><input type="text" class="update_login_pass" name="update_login_pass" value="<?= set_value('login_pass') ?>" rel="Paswoord" /></td>
				<td><input type="text" class="update_login_remarks" name="update_login_remarks" value="<?= set_value('login_remarks') ?>" rel="Opmerking" /></td>
				<td>
					&nbsp;&nbsp;<button class="mini clear" name="postback" value="update_login"><img src="img/icons/tick.png" alt="Toevoegen" /></button>
					&nbsp;&nbsp;<a href="#" class="cancel"><img src="img/icons/cross.png" alt="Annuleren" /></a>
				</td>
			</tr>
			
			<?php if(!empty($details->logins)): 	?>
				<?php foreach($details->logins as $cred):?>
					<tr id="login_<?= $cred->id ?>" rel="<?= $cred->id ?>">
						<td class="type" rel="<?= $cred->type_id ?>"><?= $cred->login_type ?></td>
						<td class="host"><?= $cred->host ?></td>
						<td class="user"><?= $cred->user ?></td>
						<td class="pass"><?= $cred->pass ?></td>
						<td class="remarks"><?= $cred->remarks ?></td>
						<td width="50">
							<a href="<?= current_url() ?>" title="Bewerken" class="edit"><img src="img/icons/pencil.png" /></a>&nbsp;&nbsp;&nbsp;
							<a href="<?= site_url('hostings/remove_login/' . $cred->id . '/' . $details->id) ?>" title="Verwijderen" class="delete"><img src="img/icons/delete.png" /></a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif;	?>
			
			

			<tr class="logins">
				<td><?= form_dropdown('login_type', $login_types); ?></td>
				<td><input type="text" name="login_host" value="<?= set_value('login_host') ?>" rel="Host" /></td>
				<td><input type="text" name="login_user" value="<?= set_value('login_user') ?>" rel="Gebruikersnaam" /></td>
				<td><input type="text" name="login_pass" value="<?= set_value('login_pass') ?>" rel="Paswoord" /></td>
				<td><input type="text" name="login_remarks" value="<?= set_value('login_remarks') ?>" rel="Opmerking" /></td>
				<td><button type="submit" class="mini clear" name="postback" value="login"><img src="img/icons/add.png" alt="Toevoegen" /></button></td>
			</tr>
		</table>
		
		<input type="hidden" id="update_login_id" class="update_login_id" name="update_login_id" value="" />
		
		<?php endif; ?>
	</form>
	
	<?php if($action_edit): ?>
	<br /><br /><br />
	<a href="<?= site_url('hostings/details/' . $details->id) ?>" class="button">Terug naar details</a>
	<?php endif; ?>
<?php else:	?>
	
	<h2>Geen details gevonden voor deze domeinnaam.</h2>
	
<?php endif;?>