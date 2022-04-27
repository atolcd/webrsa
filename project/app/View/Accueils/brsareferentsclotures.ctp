<div class="col-2-accueil">
<h2>
	<?php
		$arguments = $results['brsareferentsclotures']['arguments'];
		$total = $results['brsareferentsclotures']['nombre_total'];
		unset($results['brsareferentsclotures']['arguments']);
		unset($results['brsareferentsclotures']['nombre_total']);
		$brsareferentsclotures = $results['brsareferentsclotures'];
		$titre = __d('accueils', 'Accueil.brsareferentsclotures.titre');
		echo $titre;
	?>
</h2>
<table>
	<?php
		$count = count($brsareferentsclotures);
		if ($count > 0) {
	?>
	<h3><?= sprintf(__d('accueils', 'Blocreferent.nbBRSA'), $total)?></h3>
	<thead>
		<tr>
			<th><?php echo __d('accueils', 'Accueil.demandeur'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.date.demande'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.etatdosrsa'); ?></th>
			<th><?php echo __d('accueils', 'Accueil.actions'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
			for ($i = 0; $i < $count; $i++): ?>
		<tr class="<?php echo $i%2 == 1 ? 'odd' : 'even'; ?>">
			<td><?php echo $brsareferentsclotures[$i][0]['demandeur'] ?></td>
			<td>
				<?php
					$date = new DateTime ($brsareferentsclotures[$i][0]['dtdemrsa']);
					echo $date->format('d/m/Y');
				?>
			</td>
			<td><?php echo __d('dossier', 'ENUM::ETATDOSRSA::' . $brsareferentsclotures[$i][0]['etatdosrsa']) ?></td>
			<td class="action">
				<?php
					echo $this->Html->link(
						__d('accueils', 'Accueil.action.voir'),
						array(
					        'controller' => 'Dossiers',
					        'action' => 'view/'.$brsareferentsclotures[$i][0]['id']
					    ),
						['class' => 'view']
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
			<th><?php echo __d('accueils', 'Accueil.brsareferentsclotures.aucun'); ?></th>
		</tr>
	</thead>
	<?php
		}
	?>
	</tbody>
</table>
<?php
if ($count > 0) {
	echo '<br><b>' . $this->Html->link(
			__d('accueils', 'Accueil.lien.cohorte_modif_referents'),
			array(
				'controller' => 'referents',
				'action' => 'cohorte_modif/'.$arguments
			),
			array (
				'target' => '_blank',
			)
	) . '</b>';
}
?>
</div>