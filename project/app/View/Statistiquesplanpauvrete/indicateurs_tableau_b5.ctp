<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ; ?>
<?php if( !empty( $this->request->data ) ): ?>
	<h2>Tableau 5 - Délais pour l'orientation et la contractualisation pour les personnes entrées dans le RSA au cours de l'année et soumises aux droits et devoirs au 31/12 de l'année</h2>
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
				<th colspan="3">… dont …</th>
				<th></th>
				<th></th>
			</tr>
			<tr class="main">
				<th></th>
				<th rowspan="2">Personnes entrées dans le RSA au cours de l'année et soumises aux droits et devoirs au 31/12 de l'année (17)…</th>
				<th>personnes primo-orientées au 31/12 de l'année (18)</th>
				<th>personnes primo-orientées vers un organisme autre que Pôle emploi au 31/12 de l'année (19)</th>
				<th>personnes primo-orientées vers un organisme autre que Pôle emploi et ayant un primo-CER valide au 31/12 de l'année (20)</th>
				<th rowspan="2">Délai moyen entre la date d'entrée dans le RSA et la date de primo-orientation (en jours) (21)</th>
				<th rowspan="2">Délai moyen entre la date de primo-orientation vers un organisme autre que Pôle emploi et la date de signature du primo-CER associé à cette primo-orientation (en jours) (22)</th>
			</tr>
		</thead>
	<?php foreach( $indicateurs as $index => $indicateur ):?>
	<?php
		$name = "{$indicateur}";

		$droits_et_devoirs = (array)Hash::get( $results, "{$name}.droits_et_devoirs" );
		$primo_orientes = (array)Hash::get( $results, "{$name}.primo_orientes" );
		$primo_orientes_hors_pe = (array)Hash::get( $results, "{$name}.primo_orientes_hors_pe" );
		$primo_orientes_hors_pe_primo_cer = (array)Hash::get( $results, "{$name}.primo_orientes_hors_pe_primo_cer" );
		$delai_moyen_primo_orientes = (array)Hash::get( $results, "{$name}.delai_moyen_primo_orientes" ); 
		$delai_moyen_hors_pe_primo_orientes_primo_cer = (array)Hash::get( $results, "{$name}.delai_moyen_hors_pe_primo_orientes_primo_cer" );
	?>
		<tbody>
			<tr class="total">
				<th colspan="16"><?php echo __d( 'statistiquesdrees', $name );?></th>
			</tr>
			<?php foreach( $tranches[$indicateur] as $tranche ):?>
				<?php
					$temp_primo_orientes = isset( $primo_orientes[$tranche] ) ? $primo_orientes[$tranche] : 0 ;
					$temp_primo_orientes_hors_pe_primo_cer = isset( $primo_orientes_hors_pe_primo_cer[$tranche] ) ? $primo_orientes_hors_pe_primo_cer[$tranche] : 0;
					$temp_delai_moyen_primo_orientes = isset( $delai_moyen_primo_orientes[$tranche] ) ? $delai_moyen_primo_orientes[$tranche] : 0;
					$temp_delai_moyen_hors_pe_primo_orientes_primo_cer = isset( $delai_moyen_hors_pe_primo_orientes_primo_cer[$tranche] ) ? $delai_moyen_hors_pe_primo_orientes_primo_cer[$tranche] : 0;

					$moyenne_1 = 0;
					if ($temp_primo_orientes > 0) {
						$moyenne_1 = round ($temp_delai_moyen_primo_orientes / $temp_primo_orientes, 0);
					}
					$moyenne_2 = 0;
					if ($temp_primo_orientes_hors_pe_primo_cer > 0) {
						$moyenne_2 = round ($temp_delai_moyen_hors_pe_primo_orientes_primo_cer / $temp_primo_orientes_hors_pe_primo_cer, 0);
					}
				?>
			<tr>
				<th><?php echo __d( 'statistiquesdrees',  $tranche );?></th>
				<td class="number"><?php echo isset( $droits_et_devoirs[$tranche] ) ? $this->Locale->number( $droits_et_devoirs[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo $this->Locale->number( $temp_primo_orientes ); ?></td>
				<td class="number"><?php echo isset( $primo_orientes_hors_pe[$tranche] ) ? $this->Locale->number( $primo_orientes_hors_pe[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo $this->Locale->number( $temp_primo_orientes_hors_pe_primo_cer );?></td>
				<td class="number"><?php echo $this->Locale->number( $moyenne_1 ); ?></td>
				<td class="number"><?php echo $this->Locale->number( $moyenne_2 ); ?></td>
			</tr>
			<?php endforeach;?>

			<tr>
				<th class="total">Effectif total</th>
				<td class="total"><?php echo $this->Locale->number( array_sum( $droits_et_devoirs ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $primo_orientes ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $primo_orientes_hors_pe ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $primo_orientes_hors_pe_primo_cer ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $primo_orientes ) == 0 ? 0 : round (array_sum( $delai_moyen_primo_orientes ) / array_sum( $primo_orientes ), 0) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $primo_orientes_hors_pe_primo_cer ) == 0 ? 0 : round (array_sum( $delai_moyen_hors_pe_primo_orientes_primo_cer ) / array_sum( $primo_orientes_hors_pe_primo_cer ), 0) );?></td>
			</tr>
		</tbody>

	<?php endforeach;?>
	</table>

	<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv_tableau5', 'visualisation' ) + Hash::flatten( $this->request->data, '__' ),
			true
		);
	?></li>
	</ul>

<?php endif; ?>
