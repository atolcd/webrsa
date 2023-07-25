

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
			<th width="15%"><?= __m('created')?></th>
			<th width="25%"><?= __m('fichier')?></th>
			<th width="20%"><?= __m('total')?></th>
			<th width="20%"><?= __m('motif.pas_demandeur')?></th>
			<th width="20%"><?= __m('motif.aucun_nir')?></th>
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
					<th><?= $r[0]['aucun_nir']?></th>
				<?php echo '</tr>';

				$class = $class == 'odd' ? 'even' : 'odd';
			}
		?>
	</tbody>

</table>
<?php
}
?>