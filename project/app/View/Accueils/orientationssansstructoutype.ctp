<div class="col-2-accueil">
<h2>
	<?php
		$orientations = $results['orientationssansstructoutype'];
		$titre = __d('accueils', 'Accueil.orientationssansstructoutype.titre');
		echo $titre;
	?>
</h2>
<table>
	<?php
		$count = count($orientations);
		if ($count > 0) {
	?>
	<thead>
		<tr>
			<th><?php echo __d('accueils', 'Accueil.beneficiaire'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.date.demande'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.date.orientation'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.actions'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
			for ($i = 0; $i < $count; $i++): ?>
		<tr class="<?php echo $i%2 == 1 ? 'odd' : 'even'; ?>">
			<td><?php echo $orientations[$i]['Personne']['nom'].' '.$orientations[$i]['Personne']['prenom']; ?></td>
			<td>
				<?php
					$date = new DateTime ($orientations[$i]['Orientstruct']['date_propo']);
					echo $date->format('d/m/Y');
				?>
			</td>
			<td>
				<?php
					$date = new DateTime ($orientations[$i]['Orientstruct']['date_valid']);
					echo $date->format('d/m/Y');
				?>
			</td>
			<td>
				<?php
					echo $this->Html->link(
						__d('accueils', 'Accueil.action.modifier'),
						array(
					        'controller' => 'Orientsstructs',
					        'action' => 'edit/'.$orientations[$i]['Orientstruct']['id']
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
			<th><?php echo __d('accueils', 'Accueil.orientationssansstructoutype.aucun'); ?></th>
		</tr>
	</thead>
	<?php
		}
	?>
	</tbody>
</table>

</div>