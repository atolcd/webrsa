<?php
	require_once  dirname( __FILE__ ).DS.'search.ctp' ;
	$index = 0;
	$annee = Hash::get( $this->request->data, 'Search.annee' );
?>
<?php if( isset( $results ) ): ?>
	<h2><?php echo __d ('tableauxsuivispdvs93', 'Tableaub7.par.famille.romev3') ?></h2>
	<br />
	<table class="tableaud1">
		<tbody>
			<tr class="total">
				<th></th>
				<th colspan="2">B7</th>
				<th colspan="2">D2</th>
				<th colspan="2">TOTAL</th>
			</tr>
			<tr class="total">
				<th></th>
				<th>Nb</th>
				<th>%</th>
				<th>Nb</th>
				<th>%</th>
				<th>Nb</th>
				<th>%</th>
			</tr>
			<?php foreach( $results['familleRomev3'] as $id => $intitule ):?>
			<tr>
				<th><?php echo $intitule; ?></th>
				<td class="number"><?php echo $results['tableauRomev3']['B7'][$id]; ?></td>
				<td class="number"><?php echo $results['totalFamilleB7'] == 0 ? 0 : round (100 * $results['tableauRomev3']['B7'][$id] / $results['totalFamilleB7'], 2); ?></td>
				<td class="number"><?php echo $results['tableauRomev3']['D2'][$id]; ?></td>
				<td class="number"><?php echo $results['totalFamilleD2'] == 0 ? 0 : round (100 * $results['tableauRomev3']['D2'][$id] / $results['totalFamilleD2'], 2); ?></td>
				<td class="number"><?php echo $results['tableauRomev3']['TOTAL'][$id]; ?></td>
				<td class="number"><?php echo $results['totalFamilleTotal'] == 0 ? 0 : round (100 * $results['tableauRomev3']['TOTAL'][$id] / $results['totalFamilleTotal'], 2); ?></td>
			</tr>
			<?php endforeach;?>
			<tr class="total">
				<th>TOTAL</th>
				<td class="number"><?php echo $results['totalFamilleB7']; ?></td>
				<td class="number"><?php echo $results['totalFamilleB7'] == 0 ? 0 : 100; ?></td>
				<td class="number"><?php echo $results['totalFamilleD2']; ?></td>
				<td class="number"><?php echo $results['totalFamilleD2'] == 0 ? 0 : 100; ?></td>
				<td class="number"><?php echo $results['totalFamilleTotal']; ?></td>
				<td class="number"><?php echo $results['totalFamilleTotal'] == 0 ? 0 : 100; ?></td>
			</tr>
		</tbody>
	</table>
	<br />
	<br />
	<?php include_once  dirname( __FILE__ ).DS.'footer.ctp' ;?>
<?php endif;?>