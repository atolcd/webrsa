<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ; ?>
<?php if( !empty( $this->request->data ) ): ?>
	<h2>Tableau 6 - Nombre de personnes soumises aux droits et devoirs et orientées au 31 décembre de l'année ayant connu une réorientation d'un organisme du SPE vers un organisme hors SPE ou vice versa au cours de l'année</h2>
	<?php
		$annee = Hash::get( $this->request->data, 'Search.annee' );
		$indicateurs = array( 'age', 'sexe', 'sitfam', 'anciennete', 'nivetu' );
	?>

	<table class="first">
		<caption></caption>
		<thead>
			<tr class="main">
				<th></th>
				<th></th>
				<th colspan="2">… et dont la dernière réorientation au cours de l'année est une réorientation …</th>
			</tr>
			<tr class="main">
				<th></th>
				<th rowspan="2">Personnes soumises aux droits et devoirs orientées au 31/12 de l'année ayant connu une réorientation d'un organisme du SPE vers un organisme hors SPE ou vice versa au cours de l'année (4) (5) (7) (23)…</th>
				<th>… d'un organisme du SPE vers un organisme hors SPE (7) (23)</th>
				<th>… d'un organisme hors SPE vers un organisme du SPE (7) (23)</th>
			</tr>
		</thead>
	<?php foreach( $indicateurs as $index => $indicateur ):?>
	<?php
		$name = "{$indicateur}";

		$reorientation = (array)Hash::get( $results, "{$name}.reorientation" );
		$spe_vers_hors_spe = (array)Hash::get( $results, "{$name}.spe_vers_hors_spe" );
		$hors_spe_vers_spe = (array)Hash::get( $results, "{$name}.hors_spe_vers_spe" );
	?>
		<tbody>
			<tr class="total">
				<th colspan="4"><?php echo __d( 'statistiquesdrees', $name );?></th>
			</tr>
			<?php foreach( $tranches[$indicateur] as $tranche ):?>
			<tr>
				<th><?php echo __d( 'statistiquesdrees',  $tranche );?></th>
				<td class="number"><?php echo  isset( $reorientation[$tranche] ) ? $this->Locale->number( $reorientation[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $spe_vers_hors_spe[$tranche] ) ? $this->Locale->number( $spe_vers_hors_spe[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $hors_spe_vers_spe[$tranche] ) ? $this->Locale->number( $hors_spe_vers_spe[$tranche] ) : 0 ;?></td>
			</tr>
			<?php endforeach;?>

			<tr>
				<th class="total">Effectif total</th>
				<td class="total"><?php echo $this->Locale->number( array_sum( $reorientation ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $spe_vers_hors_spe ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $hors_spe_vers_spe ) );?></td>
			</tr>
		</tbody>

	<?php endforeach;?>
	</table>

	<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv_tableau6', 'visualisation' ) + Hash::flatten( $this->request->data, '__' ),
			true
		);
	?></li>
	</ul>

	<?php
		include_once  dirname( __FILE__ ).DS.'precision.ctp' ;
	?>
<?php endif; ?>