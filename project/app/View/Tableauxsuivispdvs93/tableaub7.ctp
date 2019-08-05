<?php
	require_once  dirname( __FILE__ ).DS.'search.ctp' ;
	$index = 0;
	$annee = Hash::get( $this->request->data, 'Search.annee' );
?>
<?php if( isset( $results ) ): ?>
	<br />
	<table class="tableaud2">
		<tbody>
			<tr class="total">
				<th><?php echo __d ('tableauxsuivispdvs93', 'Tableaub7.nb.personnes.differentes') ?> :</th>
				<td class="number"></td>
			</tr>
			<tr>
				<th> - <?php echo __d ('tableauxsuivispdvs93', 'Tableaub7.avec.ou.sans') ?></th>
				<td class="number"><?php echo $results['maintenues_sorties']; ?></td>
			</tr>
			<tr>
				<th> - <?php echo __d ('tableauxsuivispdvs93', 'Tableaub7.sans') ?></th>
				<td class="number"><?php echo $results['sorties']; ?></td>
			</tr>
			<tr>
				<th> - <?php echo __d ('tableauxsuivispdvs93', 'Tableaub7.avec') ?></th>
				<td class="number"><?php echo $results['maintenues']; ?></td>
			</tr>
		</tbody>
	</table>
	<br />
	<br />
	<?php include_once  dirname( __FILE__ ).DS.'footer.ctp' ;?>
<?php endif;?>