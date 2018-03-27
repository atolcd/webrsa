<div class="col-2-accueil">
	<h2>
		<?php
			$items = $results['rendezvous'];
			$titre = __d('accueils', 'Accueil.rendezvous.titre');
			$titre = str_replace('__LIMITE__', $items['limite'], $titre);
			unset ($items['limite']);
			echo $titre;
		?>
	</h2>
	<table>
		<?php
			$count = count($items);
			if ($count > 0) {
		?>
		<thead>
			<tr>
				<th><?php echo __d('accueils', 'Accueil.demandeur'); ?></th>
				<th><?php echo __d('accueils', 'Accueil.date.rendezvous'); ?></th>
				<th><?php echo __d('accueils', 'Accueil.type'); ?></th>
				<th><?php echo __d('accueils', 'Accueil.actions'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
				for ($i = 0; $i < $count; $i++): ?>
			<tr class="<?php echo $i%2 == 1 ? 'odd' : 'even'; ?>">
				<td><?php echo $items[$i]['Personne']['nom_complet_prenoms']; ?></td>
				<td>
					<?php
						$date = new DateTime ($items[$i]['Rendezvous']['daterdv']);
						echo $date->format('d/m/Y').' '.$items[$i]['Rendezvous']['heurerdv'];
					?>
				</td>
				<td><?php echo $items[$i]['Typerdv']['libelle']; ?></td>
			<td>
				<?php
					echo $this->Html->link(
						__d('accueils', 'Accueil.action.voir'),
						array(
					        'controller' => 'Rendezvous',
					        'action' => 'view',
					        $items[$i]['Rendezvous']['id']
					    ),
					    array (
							'target' => '_blank',
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
				<th><?php echo __d('accueils', 'Accueil.rendezvous.aucun'); ?></th>
			</tr>
		</thead>
		<?php
			}
		?>
		</tbody>
	</table>
</div>