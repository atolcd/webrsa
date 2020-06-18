<tr>
	<td>
		<h2><?php echo __m('Dossier.histomodif') ?></h2>
		<table>
			<thead>
				<tr>
					<th><?php echo __m('Dossier.date') ?></th>
					<th><?php echo __m('Dossier.prenomuser') ?></th>
					<th><?php echo __m('Dossier.nomuser') ?></th>
					<th><?php echo __m('Dossier.ancienetat') ?></th>
					<th><?php echo __m('Dossier.nouveletat') ?></th>
					<th><?php echo __m('Dossier.motif') ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach($details['Histomodifetatdossier'] as $key => $data) {
						$class = ($key %2 == 0) ? 'even' : 'odd';
				?>
				<tr class=<?php echo $class?>>
					<td><?php echo date( 'd/m/Y' , strtotime($data['created']))?></td>
					<td><?php echo $data['prenom']?></td>
					<td><?php echo $data['nom']?></td>
					<td><?php echo __d('dossier', 'ENUM::ETATDOSRSA::' . $data['etatdosrsa'])?></td>
					<td><?php echo __d('dossier', 'ENUM::ETATDOSRSA::' . $data['nouvetatdosrsa'])?></td>
					<td><?php echo $data['motif']?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</td>
</tr>