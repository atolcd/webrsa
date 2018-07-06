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
			<th><?php echo __d('accueils', 'Accueil.date.debut.contrat'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.actions'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
			for ($i = 0; $i < $count; $i++): ?>
		<tr class="<?php echo $i%2 == 1 ? 'odd' : 'even'; ?>">
			<td><?php echo $cers[$i]['Personne']['nom'].' '.$cers[$i]['Personne']['prenom']; ?></td>
			<td>
				<?php
					$date = new DateTime ($cers[$i]['Contratinsertion']['created']);
					echo $date->format('d/m/Y');
				?>
			</td>
			<td>
				<?php
					echo $this->Html->link(
						__d('accueils', 'Accueil.action.voir'),
						array(
					        'controller' => 'Contratsinsertion',
					        'action' => 'view/'.$cers[$i]['Contratinsertion']['id']
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