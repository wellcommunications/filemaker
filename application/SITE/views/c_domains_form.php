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
	
	<form id="frmDomain" action="<?= site_url($this->uri->uri_string()) ?>" method="POST">
		<!-- ================ -->
		<!-- = REGISTRATION = -->
		<!-- ================ -->
		<h2>Registratie gegevens</h2>
		<table class="details">
		
			<tr>
				<td width="240">Domeinnaam</td>
				<td>
					<?php 
						if($action_edit){
							echo $details->domain;
						}else{
					?>
						<input type="text" name="domain" class="meta" value="<?= set_value('domain', $details->domain) ?>" rel="Domeinnaam zonder extensie" /> <span class="req">*</span>
						<?php if(form_error('domain')): ?><img src="img/icons/exclamation.png" class="icon_error" title="<?= strip_tags(form_error('domain')) ?>" /><?php endif; ?><br />
						
					<?php
						}
					?>
				</td>
			</tr>
		
			<tr>
				<td>TLD</td>
				<td>
					<?php 
						if($action_edit){
							echo $details->extension . ' (' . $details->extension_desc . ')';
						}else{
							echo form_dropdown('extension_id', $extensions, (set_value('extension_id')) ? set_value('extension_id') : 0);
							echo ' <span class="req">*</span>';
							if(form_error('extension_id')){
								echo '<img src="img/icons/exclamation.png" class="icon_error" title="' . strip_tags(form_error('domain')) . '" />';
							} 
						}
					?>
				</td>
			</tr>
		
			<tr>
				<td>Reseller</td>
				<td><?= form_dropdown('reseller_id', $resellers, (!empty($details->reseller)) ? $details->reseller->id : 0)?></td>
			</tr>
		
			<tr>
				<td>Registrant</td>
				<td>
					<input type="text" class="meta" name="contact_firstname" value="<?= set_value('contact_firstname', (isset($details->contact->firstname)) ? $details->contact->firstname : '') ?>" rel="Voornaam" /> <span class="req">*</span>
					<?php if(form_error('contact_firstname')): ?><img src="img/icons/exclamation.png" class="icon_error" title="<?= strip_tags(form_error('contact_firstname')) ?>" /><?php endif; ?><br />
					
					<input type="text" class="meta" name="contact_lastname" value="<?= set_value('contact_lastname', (isset($details->contact->lastname)) ? $details->contact->lastname : '') ?>" rel="Familienaam" /> <span class="req">*</span>
					<?php if(form_error('contact_lastname')): ?><img src="img/icons/exclamation.png" class="icon_error" title="<?= strip_tags(form_error('contact_lastname')) ?>" /><?php endif; ?><br />
						
					<input type="text" class="meta" name="contact_company" value="<?= set_value('contact_company', (isset($details->contact->company)) ? $details->contact->company : '') ?>" rel="Firma" /> <span class="req">*</span>	
					<?php if(form_error('contact_company')): ?><img src="img/icons/exclamation.png" class="icon_error" title="<?= strip_tags(form_error('contact_company')) ?>" /><?php endif; ?><br />
						
					<input type="text" class="meta" name="contact_email" value="<?= set_value('contact_email', (isset($details->contact->email)) ? $details->contact->email : '') ?>" rel="E-mail" />
					<?php if(form_error('contact_email')): ?><img src="img/icons/exclamation.png" class="icon_error" title="<?= strip_tags(form_error('contact_email')) ?>" /><?php endif; ?><br />
				</td>
			</tr>
		
			<tr>
				<td>Prijs</td>
				<td>
					<?php
					
						if(set_value('price')){
							$price = set_value('price');
						}elseif($details->price==NULL){
							$price = "";
						}else{
							$price = number_format(set_value('price', $details->price), 2);
						}
					?>
					<input type="text" name="price" class="numeric euro <?= (form_error('price')) ? 'error' : '' ?>" value="<?= $price ?>" />
					<?php if(form_error('price')): ?><img src="img/icons/exclamation.png" class="icon_error" title="<?= strip_tags(form_error('price')) ?>" /><br /><?php endif; ?><br />
					
					<input type="checkbox" name="intern" id="chkIntern" value="1" class="check" <?= set_checkbox('intern', '1', $details->intern); ?> /><label for="chkIntern">Intern</label><br />
					<input type="checkbox" name="pakket" id="chkPakket" value="1" class="check" <?= set_checkbox('pakket', '1', $details->pakket); ?> /><label for="chkPakket">Pakket/Webdirect</label>
				</td>
			</tr>
		
			<tr>
				<td>Verlenging</td>
				<td>
					<?php unset($details->arr_months[0]) ?>
					<?= form_dropdown('month', $details->arr_months, (set_value('month')) ? set_value('month') : $details->month); ?> <span class="req">*</span>
				</td>
			</tr>
		
		
			<tr>
				<td>Geregistreerd op</td>
				<td><input type="text" name="registration_date" class="date" value="<?= set_value('registration_date', $details->registration_date) ?>" /></td>
			</tr>
		
		
			<tr class"error">
				<td>Verwijderen op</td>
				<td><input type="text" name="deletion_date" class="date" value="<?= set_value('deletion_date', $details->deletion_date) ?>" /></td>
			</tr>
		
		</table>
		
		<p class="req">* verplichte velden</p>
		<br /><br />
		
		
		<!-- =========== -->
		<!-- = REMARKS = -->
		<!-- =========== -->
		<h2>Opmerkingen</h2>
		<textarea name="remarks"><?= str_replace('', "\n", $details->remarks) ?></textarea>
		
		
		
		<!-- =========== -->
		<!-- = BUTTONS = -->
		<!-- =========== -->
		<br /><br />
		<div class="buttons">
			<input type="hidden" name="placeholder" id="myPlaceholder" value="full" />
			<a href="<?= site_url('domains/edit/' . $details->id) ?>" id="btnSubmit" rel="frmDomain" class="button">Opslaan</a>
			<?php if($this->session->userdata('crrUser')->allow_remove==1 && $action_edit): ?>
			<a href="<?= site_url('domains/delete/' . $details->id) ?>" class="button delete">Domeinnaam verwijderen</a>
			<?php endif;?>
			<a href="<?= site_url('domains/details/' . $details->id) ?>" class="button">Annuleren</a>
		</div>
		
		
		<?php if($action_edit):	?>
		
			<br /><br /><br />
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
									<a href="<?= site_url('domains/remove_addon/'.$add->conn_id.'/'.$details->id) ?>" class="delete">
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
								<a href="<?= site_url('domains/remove_login/' . $cred->id . '/' . $details->id) ?>" title="Verwijderen" class="delete"><img src="img/icons/delete.png" /></a>
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
		
		
		<?php endif;	?>
	</form>
	
<?php else:	?>
	
	<h2>Geen details gevonden voor deze domeinnaam.</h2>
	
<?php endif;?>