<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ; ?>
<?php if( !empty( $this->request->data ) ): ?>
	<h2>Tableau 3 - Durées inscrites dans les CER en cours de validité au 31/12 de l'année des personnes soumises aux droits et devoirs et orientées à cette même date vers un organisme autre que Pôle emploi</h2>
	<?php
		$annee = Hash::get( $this->request->data, 'Search.annee' );
		$indicateurs = array( 'age', 'sexe', 'sitfam', 'anciennete', 'nivetu' );
	?>

	<table class="first">
		<caption>Personnes soumises aux droits et devoirs et orientées vers un organisme autre que Pôle emploi au 31/12 de l'année ayant un CER en cours de validité à cette même date (4) (6) (15) ...</caption>
		<thead>
			<tr class="main">
				<th></th>
				<th> … d'une durée inscrite inférieure à 6 mois</th>
				<th> … d'une durée inscrite de 6 mois à moins de 1 an</th>
				<th> … d'une durée inscrite de 1 an et plus</th>
			</tr>
		</thead>
	<?php foreach( $indicateurs as $index => $indicateur ):?>
	<?php
		$name = "{$indicateur}";
		$cer_moins_6_mois = (array)Hash::get( $results, "{$name}.cer_moins_6_mois" );
		$cer_6_mois_un_an = (array)Hash::get( $results, "{$name}.cer_6_mois_un_an" );
		$cer_1_an_et_plus = (array)Hash::get( $results, "{$name}.cer_1_an_et_plus" );
	?>
		<tbody>
			<tr class="total">
				<th colspan="13"><?php echo __d( 'statistiquesdrees', $name );?></th>
			</tr>
			<?php foreach( $tranches[$indicateur] as $tranche ):?>
			<tr>
				<th><?php echo __d( 'statistiquesdrees',  $tranche );?></th>
				<td class="number"><?php echo  isset( $cer_moins_6_mois[$tranche] ) ? $this->Locale->number( $cer_moins_6_mois[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $cer_6_mois_un_an[$tranche] ) ? $this->Locale->number( $cer_6_mois_un_an[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $cer_1_an_et_plus[$tranche] ) ? $this->Locale->number( $cer_1_an_et_plus[$tranche] ) : 0 ;?></td>
			</tr>
			<?php endforeach;?>

			<tr>
				<th class="total">Effectif total</th>
				<td class="total"><?php echo $this->Locale->number( array_sum( $cer_moins_6_mois ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $cer_6_mois_un_an ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $cer_1_an_et_plus ) );?></td>
			</tr>
		</tbody>

	<?php endforeach;?>
	</table>

	<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv_tableau3', 'visualisation' ) + Hash::flatten( $this->request->data, '__' ),
			true
		);
	?></li>
	</ul>

<?php endif; ?>