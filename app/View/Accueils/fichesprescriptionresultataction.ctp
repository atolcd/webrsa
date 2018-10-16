<div class="col-2-accueil">
<h2>
<?php
$fiches = $results['fichesprescriptionresultataction'];
$titre = __d('accueils', 'Accueil.fiche.titreBis');
$titre = str_replace('__LIMITE__', $fiches['limite'], $titre);
unset ($fiches['limite']);
echo $titre;
?>
</h2>
<table>
	<?php
	$count = count($fiches);
	if ($count > 0) {
	?>
	<thead>
	<tr>
	<th><?php echo __d('accueils', 'Accueil.demandeur'); ?></th>
	<th><?php echo __d('accueils', 'Accueil.date.signature'); ?></th>
	<th><?php echo __d('accueils', 'Accueil.description'); ?></th>
	<th><?php echo __d('accueils', 'Accueil.statut'); ?></th>
	<th><?php echo __d('accueils', 'Accueil.actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	for ($i = 0; $i < $count; $i++): ?>
		<tr class="<?php echo $i%2 == 1 ? 'odd' : 'even'; ?>">
		<td><?php echo $fiches[$i]['Personne']['nom'].' '.$fiches[$i]['Personne']['prenom']; ?></td>
		<td>
		<?php
		$date = new DateTime ($fiches[$i]['Ficheprescription93']['created']);
		echo $date->format('d/m/Y');
		?>
		</td>
		<td><?php echo $fiches[$i]['Ficheprescription93']['objet']; ?></td>
		<td><?php echo __d ('ficheprescription93', 'ENUM::STATUT::'.$fiches[$i]['Ficheprescription93']['statut']); ?></td>
		<td>
		<?php
		echo $this->Html->link(
		    __d('accueils', 'Accueil.action.voir'),
		array(
		    'controller' => 'Fichesprescriptions93',
		'action' => 'edit/'.$fiches[$i]['Ficheprescription93']['id'],
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
		<th><?php echo __d('accueils', 'Accueil.fiche.aucun'); ?></th>
		</tr>
		</thead>
	<?php
	}
	?>
	</tbody>
	</table>
</div>