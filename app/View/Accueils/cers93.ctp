<div class="col-2-accueil">
<h2>
	<?php
		$cers = $results['cers'];
		$titre = __d('accueils', 'Accueil.cer.titre');
		$titre = str_replace('__DU__', $cers['du'], $titre);
		$titre = str_replace('__AU__', $cers['au'], $titre);
		unset ($cers['du']);
		unset ($cers['au']);
		echo $titre;
	?>
</h2>
<table>
	<?php
		$count = count($cers);
		if ($count > 0) {
	?>
	<thead>
		<tr>
			<th><?php echo __d('accueils', 'Accueil.demandeur'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.date.creation'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.description'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.actions'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
			for ($i = 0; $i < $count; $i++): ?>
		<tr class="<?php echo $i%2 == 1 ? 'odd' : 'even'; ?>">
			<td><?php echo $cers[$i]['Cer93']['nom'].' '.$cers[$i]['Cer93']['prenom']; ?></td>
			<td>
				<?php
					$date = new DateTime ($cers[$i]['Cer93']['created']);
					echo $date->format('d/m/Y');
				?>
			</td>
			<td><?php echo $cers[$i]['Cer93']['prevu']; ?></td>
			<td>
				<?php
					echo $this->Html->link(
						__d('accueils', 'Accueil.action.voir'),
						array(
					        'controller' => 'Cers93',
					        'action' => 'view/'.$cers[$i]['Cer93']['contratinsertion_id']
					    )
					);
				?>
			</td>
		</tr>
	<?php
			endfor;
		}
		else {
	?>
	<thead>
		<tr>
			<th><?php echo __d('accueils', 'Accueil.cer.aucun'); ?></th>
		</tr>
	</thead>
	<?php
		}
	?>
	</tbody>
</table>
</div>