

<?php
	echo $this->Default3->titleForLayout();

	echo $this->element('rapports_talend_menu', ['contact' => true]);

if( empty( $Rapportstalendsmodescontacts ) ) {
	echo '<p class="notice">'. __m('rapports.aucun') . '</p>';
}else{
?>
<table>
	<thead>
		<tr>
			<th><?= __m('created')?></th>
			<th><?= __m('fichier')?></th>
			<th><?= __m('total')?></th>
			<th><?= __m('motif.pas_demandeur')?></th>
			<th><?= __m('motif.ancien_dossier')?></th>
			<th><?= __m('motif.aucun_nir')?></th>
			<th><?= __m('motif.aucun_matricule')?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			$class = 'odd';
			foreach ($Rapportstalendsmodescontacts as $r){
				echo '<tr class='.$class.'>'; ?>
					<td><?= $r[0]['created']?></td>
					<th><?= $r[0]['fichier']?></th>
					<th><?= $r[0]['nombre_total_rejets']?></th>
					<th><?= $r[0]['pas_demandeur']?></th>
					<th><?= $r[0]['ancien_dossier']?></th>
					<th><?= $r[0]['aucun_nir']?></th>
					<th><?= $r[0]['aucun_matricule']?></th>
				<?php echo '</tr>';

				$class = $class == 'odd' ? 'even' : 'odd';
			}
		?>
	</tbody>

</table>
<?php
}
?>