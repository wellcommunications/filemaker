<?php if(!empty($feedback)):?>
	<div class="feedback <?= $feedback->type ?>">
		<?php if(isset($feedback->title)):?>
			<h2><?= $feedback->title ?></h2>
		<?php endif; ?>
		<p><?= $feedback->message ?></p>
	</div>
<?php endif; ?>
	
	
<?php if(!empty($hostings)): ?>
	
	
	<table class="skinned">
		<tr>
			<th>Hosting <span>(totaal: <?= count($hostings) ?>)</span></th>
			<th>Contact</th>
			<th>Pakket</th>
			<th>Hernieuwen</th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
		
		<?php foreach($hostings as $hosting):	?>
			<tr>
				<td><?= $hosting->domain_full ?></td>
				<td>
					<?= $hosting->contact_toString() ?>
				</td>
				<td><?= $hosting->pakket->size ?></td>
				<td><?= $hosting->month_full ?></td>
				<td><?= ($hosting->hasMySQL) ? '<img src="img/icons/database.png" alt="Database" title="MySQL database" />': '' ?></td>
				<td><a href="<?= site_url('hostings/details/' . $hosting->id) ?>"  class="details" title="Details"><img src="img/icons/magnifier.png" alt="Details" /></a></td>
				<td>
					<?php if($this->session->userdata('crrUser')->allow_remove==1): ?>
						<a href="<?= site_url('hostings/delete/' . $hosting->id) ?>" class="delete" title="Verwijderen"><img src="img/icons/delete.png" alt="Verwijderen" /></a>
					<?php endif; 	?>
				</td>
			</tr>
			
		<?php endforeach; ?>
		
	</table>
	
<?php endif; ?>