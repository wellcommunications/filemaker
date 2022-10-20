<form action="<?= site_url('domains/search') ?>" method="POST">
	<h2>Voeg zoekveld toe</h2>
	<select name="addFields" id="ddlAddFields">
		<option value='0'>--- Kies een veld ---</option>
		<?php 
			foreach($fields as $field){
				echo "<option value='$field->db_field'>$field->label</option>";
			}
		?>
	</select>
	<br />
	<br />
	<a href="#" class="button" id="btnAddField">Toevoegen</a>
	<br /><br /><br /><br /><br />
	
	<div id="frmSearch">
		<h2>Zoekvelden</h2>
		<table>
			<tr><td width="160"></td><td width="50"></td><td></td></tr>
		</table>
		
		<br /><br />
		<input type="hidden" name="arr_fields" id="arr_fields" value="" />
		<button type="submit" name="postback" value="postback">Zoeken</button>
		<br><br><br><br><br>
	</div>
	
	<?php if(isset($search) && !empty($search)):	?>
		
		<h2>Gezocht op:</h2>
		<ul class="searchList">
			<?php 
				foreach($search as $crit){
					echo "<li><strong>{$crit->label}</strong> ";
					
					if($crit->operator==' LIKE '){
						echo "bevat";
					}elseif($crit->operator!='FIND_IN_SET'){
						echo $crit->operator;
					}
					echo " ";
					
					if($fields[$crit->field]->type=="boolean"){
						echo ($crit->value==1) ? 'ja' : 'nee';
					}elseif($fields[$crit->field]->type=="select"){
						echo trim($fields[$crit->field]->values[trim($crit->value, '%')], '- ');
					}else{
						echo '"' . trim($crit->value, "%") . '"';
					}
				
					echo "</li>";
				}
			?>
		</ul>
		
	<?php endif;	?>
	
	<br /><br />
	
	<?php if(isset($domains)): ?>
		<?php if(!empty($domains)):?>
			<table class="skinned">
				<tr>
					<th>Domeinnaam <span>(totaal: <?= count($domains) ?>)</span></th>
					<th>Contact</th>
					<th>Hernieuwen</th>
					<th>Prijs</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>

				<?php foreach($domains as $domain):	?>
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
						<td><a href="<?= site_url('domains/details/' . $domain->id) ?>" target="_blank" class="details"  title="Details"><img src="img/icons/magnifier.png" alt="Details" /></a></td>
						<td>
							<?php if($this->session->userdata('crrUser')->allow_remove==1): ?>
								<a href="<?= site_url('domains/delete/' . $domain->id) ?>" class="delete" title="Verwijderen"><img src="img/icons/delete.png" alt="Verwijderen" /></a>
							<?php endif; ?>
						</td>
					</tr>

				<?php endforeach; ?>

			</table>
		<?php else: ?>
			
			<h2>Geen resultaten gevonden</h2>
			
		<?php endif; ?>
	<?php endif; ?>
</form>



<table id="dummyTable">
	<?php foreach($fields as $field){
		
		//	LABEL
		echo "<tr id='dummy_$field->db_field' rel='";
		if(isset($field->multiple))  echo 'multiple';
		echo "'><td>" . $field->label . "</td>";
		
		//	OPERATOR
		if(isset($field->operators) && !empty($field->operators)){
			echo "<td><select class='operator' name='operator___SET_FIELDNAME__'>";
			foreach($field->operators as $op){
				echo "<option value='$op'>$op</option>";
			}
			echo "</select></td>";
		}else{
			echo "<td></td>";
		}
		
		//	VALUE
		echo "<td>";
		switch($field->type){
			case 'boolean':
				echo "<select name='__SET_FIELDNAME__'><option value='1'>ja</option><option value='0'>nee</option></select>";
				break;
			case 'select':
				echo form_dropdown('__SET_FIELDNAME__', $field->values);
				break;
			case 'date':
				echo "<input type='text' name='__SET_FIELDNAME__' class='datepicker' />";
				break;
			case 'text':
			default:
				echo "<input type='text' name='__SET_FIELDNAME__' />";
				break;
		}
		echo "</td>";

		//echo "<td><a href='#' class='removeField' title='Verwijder zoekveld'><img src='img/icons/cross.png' alt='Verwijder zoekveld' /></a></td>";
		
		echo "</tr>";
		
	}?>

</table>