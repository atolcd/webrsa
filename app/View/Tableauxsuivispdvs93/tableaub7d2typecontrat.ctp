<?php
	require_once  dirname( __FILE__ ).DS.'search.ctp' ;
	$index = 0;
	$annee = Hash::get( $this->request->data, 'Search.annee' );
?>
<?php if( isset( $results ) ): ?>
	<h2><?php echo __d ('tableauxsuivispdvs93', 'Tableaub7.par.type.contrat') ?></h2>
	<br />
	<table class="tableaud1">
		<tbody>
			<tr class="total">
				<th></th>
				<th colspan="4">B7</th>
				<th colspan="4">D2</th>
			</tr>
			<tr class="total">
				<th></th>
				<th colspan="2">Tps complet</th>
				<th colspan="2">Tps partiel</th>
				<th colspan="2">Tps complet</th>
				<th colspan="2">Tps partiel</th>
			</tr>
			<tr class="total">
				<th></th>
				<th>Nb</th>
				<th>%</th>
				<th>Nb</th>
				<th>%</th>
				<th>Nb</th>
				<th>%</th>
				<th>Nb</th>
				<th>%</th>
			</tr>
			<?php foreach( $results['typeemploi'] as $id => $intitule ):?>
			<tr>
				<th><?php echo $intitule; ?></th>
				<td class="number"><?php echo $results['tableauB7']['complet'][$id]; ?></td>
				<td class="number"><?php echo round (100 * $results['tableauB7']['complet'][$id] / $results['total'], 2); ?></td>
				<td class="number"><?php echo $results['tableauB7']['partiel'][$id]; ?></td>
				<td class="number"><?php echo round (100 * $results['tableauB7']['partiel'][$id] / $results['total'], 2); ?></td>
				<td class="number"><?php echo $results['tableauD2']['complet'][$id]; ?></td>
				<td class="number"><?php echo round (100 * $results['tableauD2']['complet'][$id] / $results['total'], 2); ?></td>
				<td class="number"><?php echo $results['tableauD2']['partiel'][$id]; ?></td>
				<td class="number"><?php echo round (100 * $results['tableauD2']['partiel'][$id] / $results['total'], 2); ?></td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	<br />
	<br />
	<?php include_once  dirname( __FILE__ ).DS.'footer.ctp' ;?>
<?php endif;?>