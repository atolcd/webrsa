<div class="col-2-accueil">
<h2>
	<?php
		$cers = $results['dernierscersperimes'];
		echo __d('accueils', 'Accueil.dernierscersperimes.titre');
	?>
</h2>
<table>
	<?php
		$count = count($cers);
		if ($count > 0) {
	?>
	<thead>
		<tr>
			<th><?php echo __d('accueils', 'Accueil.referent'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.demandeur'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.date.fin.contrat'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.actions'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
			for ($i = 0; $i < $count; $i++):
	?>
		<tr class="<?php echo $i%2 == 1 ? 'odd' : 'even'; ?>">
			<td><?php echo $cers[$i]['Referent']['qual'].' '.$cers[$i]['Referent']['nom'].' '.$cers[$i]['Referent']['prenom']; ?></td>
			<td><?php echo $cers[$i]['Personne']['nom'].' '.$cers[$i]['Personne']['prenom']; ?></td>
			<td>
				<?php
					$date = new DateTime ($cers[$i]['Contratinsertion']['df_ci']);
					echo $date->format('d/m/Y');
				?>
			</td>
			<td>
				<?php
					echo $this->Html->link(
						__d('accueils', 'Accueil.action.voir'),
						array(
					        'controller' => 'contratsinsertion',
					        'action' => 'index/'.$cers[$i]['Personne']['id']
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
			<th><?php echo __d('accueils', 'Accueil.dernierscersperimes.aucun'); ?></th>
		</tr>
	</thead>
	<?php
		}
	?>
	</tbody>
</table>
</div>