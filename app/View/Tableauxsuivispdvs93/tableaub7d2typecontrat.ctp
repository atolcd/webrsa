<?php
	require_once  dirname( __FILE__ ).DS.'search.ctp' ;
	$index = 0;
	$annee = Hash::get( $this->request->data, 'Search.annee' );
?>
<?php if( isset( $results ) ): ?>
	<h2><?php echo __d ('tableauxsuivispdvs93', 'Tableaub7.par.type.contrat') ?></h2>
	<br />
	<table class="tableaud2">
		<tbody>
			<tr class="total">
				<th></th>
				<th colspan="6" style="text-align: center;">Maintiens dans l’accompagnement</th>
				<th colspan="6" style="text-align: center;">Sortie de l’accompagnement</th>
			</tr>
			<tr class="total">
				<th></th>
				<th colspan="2">Tps complet</th>
				<th colspan="2">Tps partiel</th>
				<th colspan="2">Non communiqué</th>
				<th colspan="2">Tps complet</th>
				<th colspan="2">Tps partiel</th>
				<th colspan="2">Non communiqué</th>
			</tr>
			<tr class="total">
				<th></th>
				<th>Nb d'accès</th>
				<th>%</th>
				<th>Nb d'accès</th>
				<th>%</th>
				<th>Nb d'accès</th>
				<th>%</th>
				<th>Nb d'accès</th>
				<th>%</th>
				<th>Nb d'accès</th>
				<th>%</th>
				<th>Nb d'accès</th>
				<th>%</th>
			</tr>
			<?php foreach( $results['typeemploi'] as $numero => $typeemploi ):?>
			<?php
				$numero = $typeemploi['Typeemploi']['codetypeemploi'];
			?>
			<tr>
				<th><?php echo $typeemploi['Typeemploi']['name']; ?></th>
				<td class="number"><?php echo $results['tableauB7']['complet'][$numero]; ?></td>
				<td class="number"><?php echo $results['total']['B7']['complet'] == 0 ? 0 : round (100 * $results['tableauB7']['complet'][$numero] / $results['total']['B7']['complet'], 2)."%"; ?></td>
				<td class="number"><?php echo $results['tableauB7']['partiel'][$numero]; ?></td>
				<td class="number"><?php echo $results['total']['B7']['partiel'] == 0 ? 0 : round (100 * $results['tableauB7']['partiel'][$numero] / $results['total']['B7']['partiel'], 2)."%"; ?></td>
				<td class="number"><?php echo $results['tableauB7']['non_com'][$numero]; ?></td>
				<td class="number"><?php echo $results['total']['B7']['non_com'] == 0 ? 0 : round (100 * $results['tableauB7']['non_com'][$numero] / $results['total']['B7']['non_com'], 2)."%"; ?></td>
				<td class="number"><?php echo @$results['tableauD2']['complet'][$numero]; ?></td>
				<td class="number"><?php echo @$results['total']['D2']['complet'] == 0 ? 0 : round (100 * @$results['tableauD2']['complet'][$numero] / @$results['total']['D2']['complet'], 2)."%"; ?></td>
				<td class="number"><?php echo @$results['tableauD2']['partiel'][$numero]; ?></td>
				<td class="number"><?php echo @$results['total']['D2']['partiel'] == 0 ? 0 : round (100 * @$results['tableauD2']['partiel'][$numero] / @$results['total']['D2']['partiel'], 2)."%"; ?></td>
				<td class="number"><?php echo @$results['tableauD2']['non_com'][$numero]; ?></td>
				<td class="number"><?php echo @$results['total']['D2']['non_com'] == 0 ? 0 : round (100 * @$results['tableauD2']['non_com'][$numero] / @$results['total']['D2']['non_com'], 2)."%"; ?></td>
			</tr>
			<?php endforeach;?>
			<tr class="total">
				<th>TOTAL</th>
				<td class="number"><?php echo $results['total']['B7']['complet']; ?></td>
				<td class="number"><?php echo $results['total']['B7']['complet'] == 0 ? 0 : "100%"; ?></td>
				<td class="number"><?php echo $results['total']['B7']['partiel']; ?></td>
				<td class="number"><?php echo $results['total']['B7']['partiel'] == 0 ? 0 : "100%"; ?></td>
				<td class="number"><?php echo $results['total']['B7']['non_com']; ?></td>
				<td class="number"><?php echo $results['total']['B7']['non_com'] == 0 ? 0 : "100%"; ?></td>
				<td class="number"><?php echo $results['total']['D2']['complet']; ?></td>
				<td class="number"><?php echo $results['total']['D2']['complet'] == 0 ? 0 : "100%"; ?></td>
				<td class="number"><?php echo $results['total']['D2']['partiel']; ?></td>
				<td class="number"><?php echo $results['total']['D2']['partiel'] == 0 ? 0 : "100%"; ?></td>
				<td class="number"><?php echo $results['total']['D2']['non_com']; ?></td>
				<td class="number"><?php echo $results['total']['D2']['non_com'] == 0 ? 0 : "100%"; ?></td>
			</tr>
		</tbody>
	</table>
	<br />
	<br />
	<?php include_once  dirname( __FILE__ ).DS.'footer.ctp' ;?>
<?php endif;?>