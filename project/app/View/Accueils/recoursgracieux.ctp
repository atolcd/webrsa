<div class="col-2-accueil">
<h2>
	<?php
		$recoursgracieux = $results['recoursgracieux'];
		$titre = __d('accueils', 'Accueil.recourgracieux.titre');
		$titre = str_replace('__LIMITE__', $recoursgracieux['limite'], $titre);
		unset ($recoursgracieux['limite']);
		echo $titre;
	?>
</h2>
<table>
	<?php
		$count = count($recoursgracieux);
		if ($count > 0) {
	?>
	<thead>
		<tr>
			<th><?php echo __d('accueils', 'Accueil.recoursgracieux.dtarrivee'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.recoursgracieux.dtbutoir'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.recoursgracieux.dtreception'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.recoursgracieux.origine'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.recoursgracieux.dtaffectation'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.recoursgracieux.etatDepuis'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.actions'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
		//debug ( $recoursgracieux );
		for ($i = 0; $i < $count; $i++):
			?>
		<tr class="<?php echo $i%2 == 1 ? 'odd' : 'even'; ?>">
			<td>
				<?php
					$date = new DateTime ($recoursgracieux[$i]['Recourgracieux']['dtarrivee']);
					echo $date->format('d/m/Y');
				?>
			</td>
			<td>
				<?php
					$date = new DateTime ($recoursgracieux[$i]['Recourgracieux']['dtbutoir']);
					echo $date->format('d/m/Y');
				?>
			</td>
			<td>
				<?php
					$date = new DateTime ($recoursgracieux[$i]['Recourgracieux']['dtreception']);
					echo $date->format('d/m/Y');
				?>
			</td>
			<td><?php echo $recoursgracieux[$i]['Originerecoursgracieux']['name']; ?></td>
			<td>
				<?php
					$date = new DateTime ($recoursgracieux[$i]['Recourgracieux']['dtaffectation']);
					echo $date->format('d/m/Y');
				?>
			</td>
			<td><?php echo $recoursgracieux[$i]['Recourgracieux']['etatDepuis']; ?></td>
			<td>
				<?php
					echo $this->Html->link(
						__d('accueils', 'Accueil.action.voir'),
						array(
					        'controller' => 'Recoursgracieux',
					        'action' => 'view/'.$recoursgracieux[$i]['Recourgracieux']['id']
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