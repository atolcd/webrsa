<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ; ?>
<?php if( !empty( $this->request->data ) ): ?>
	<h2>Tableau 1 - Nombre de personnes soumises aux droits et devoirs au 31/12 de l'année, selon l'orientation à cette même date</h2>
	<?php
		$annee = Hash::get( $this->request->data, 'Search.annee' );
		$indicateurs = array( 'age', 'sexe', 'sitfam', 'anciennete', 'nivetu' );
	?>

	<table class="first">
		<caption>Orientation des personnes dans le champ des Droits et Devoirs au 31 décembre de l'année <?php echo $annee;?>, au sens du type de parcours</caption>
		<thead>
			<tr class="main">
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th colspan="5">… dont personnes orientées vers un organisme du SPE autre que Pôle emploi (6) (7)</th>
				<th colspan="5">… dont personnes orientées vers un organisme hors SPE6-7</th>
			</tr>
			<tr class="main">
				<th></th>
				<th rowspan="2">Personnes soumises aux droits et devoirs au 31/12 de l'année (4)</th>
				<th rowspan="2">Personnes soumises aux droits et devoirs au 31/12 de l'année et non orientées à cette même date (5)</th>
				<th rowspan="2">Personnes soumises aux droits et devoirs au 31/12 de l'année et orientées à cette même date (5)</th>
				<th rowspan="2">Personnes soumises aux droits et devoirs au 31/12 de l'année et orientées vers Pôle emploi à cette même date (6)</th>
				<th rowspan="2">Personnes soumises aux droits et devoirs au 31/12 de l'année et orientées vers un organisme autre que Pôle emploi à cette même date (6)…</th>
				<th>Mission locale</th>
				<th>Maison de l'emploi (MDE), Maison de l’emploi et de la formation (MDEF), Plan local pluriannuel pour l'insertion et l'emploi (PLIE), Cap Emploi</th>
				<th>Structure d'appui à la création et au développement d'entreprise (8)</th>
				<th>Structure d'insertion par l'activité économique (IAE) (9)</th>
				<th>Autre organisme de placement professionnel ou de formation professionnelle (10)</th>
				<th>Service du Conseil départemental/territorial (11)</th>
				<th>Caisse d'allocations familiales (Caf) (12)</th>
				<th>Mutualité sociale agricole (Msa)</th>
				<th>Centre communal/intercommunal d'action sociale (CCAS/CIAS) (13)</th>
				<th>Autre organisme</th>
			</tr>
		</thead>
	<?php foreach( $indicateurs as $index => $indicateur ):?>
	<?php
		$name = "{$indicateur}";

		$droits_et_devoirs = (array)Hash::get( $results, "{$name}.droits_et_devoirs" );
		$non_orientes = (array)Hash::get( $results, "{$name}.non_orientes" );
		$orientes = (array)Hash::get( $results, "{$name}.orientes" );
		$orientes_pole_emploi = (array)Hash::get( $results, "{$name}.orientes_pole_emploi" );
		$orientes_autre_que_pole_emploi = (array)Hash::get( $results, "{$name}.orientes_autre_que_pole_emploi" );
		$spe_mission_locale = (array)Hash::get( $results, "{$name}.spe_mission_locale" );
		$spe_mde_mdef_plie = (array)Hash::get( $results, "{$name}.spe_mde_mdef_plie" );
		$spe_creation_entreprise = (array)Hash::get( $results, "{$name}.spe_creation_entreprise" );
		$spe_iae = (array)Hash::get( $results, "{$name}.spe_iae" );
		$spe_autre_placement_pro = (array)Hash::get( $results, "{$name}.spe_autre_placement_pro" );
		$hors_spe_ssd = (array)Hash::get( $results, "{$name}.hors_spe_ssd" );
		$hors_spe_caf = (array)Hash::get( $results, "{$name}.hors_spe_caf" );
		$hors_spe_msa = (array)Hash::get( $results, "{$name}.hors_spe_msa" );
		$hors_spe_ccas_cias = (array)Hash::get( $results, "{$name}.hors_spe_ccas_cias" );
		$hors_spe_autre_organisme = (array)Hash::get( $results, "{$name}.hors_spe_autre_organisme" );
	?>
		<tbody>
			<tr class="total">
				<th colspan="16"><?php echo __d( 'statistiquesdrees', $name );?></th>
			</tr>
			<?php foreach( $tranches[$indicateur] as $tranche ):?>
			<tr>
				<th><?php echo __d( 'statistiquesdrees',  $tranche );?></th>
				<td class="number"><?php echo  isset( $droits_et_devoirs[$tranche] ) ? $this->Locale->number( $droits_et_devoirs[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $non_orientes[$tranche] ) ? $this->Locale->number( $non_orientes[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $orientes[$tranche] ) ? $this->Locale->number( $orientes[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $orientes_pole_emploi[$tranche] ) ? $this->Locale->number( $orientes_pole_emploi[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $orientes_autre_que_pole_emploi[$tranche] ) ? $this->Locale->number( $orientes_autre_que_pole_emploi[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $spe_mission_locale[$tranche] ) ? $this->Locale->number( $spe_mission_locale[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $spe_mde_mdef_plie[$tranche] ) ? $this->Locale->number( $spe_mde_mdef_plie[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $spe_creation_entreprise[$tranche] ) ? $this->Locale->number( $spe_creation_entreprise[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $spe_iae[$tranche] ) ? $this->Locale->number( $spe_iae[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $spe_autre_placement_pro[$tranche] ) ? $this->Locale->number( $spe_autre_placement_pro[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $hors_spe_ssd[$tranche] ) ? $this->Locale->number( $hors_spe_ssd[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $hors_spe_caf[$tranche] ) ? $this->Locale->number( $hors_spe_caf[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $hors_spe_msa[$tranche] ) ? $this->Locale->number( $hors_spe_msa[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $hors_spe_ccas_cias[$tranche] ) ? $this->Locale->number( $hors_spe_ccas_cias[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $hors_spe_autre_organisme[$tranche] ) ? $this->Locale->number( $hors_spe_autre_organisme[$tranche] ) : 0 ;?></td>
			</tr>
			<?php endforeach;?>

			<tr>
				<th class="total">Effectif total</th>
				<td class="total"><?php echo $this->Locale->number( array_sum( $droits_et_devoirs ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $non_orientes ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $orientes ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $orientes_pole_emploi ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $orientes_autre_que_pole_emploi ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $spe_mission_locale ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $spe_mde_mdef_plie ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $spe_creation_entreprise ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $spe_iae ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $spe_autre_placement_pro ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $hors_spe_ssd ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $hors_spe_caf ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $hors_spe_msa ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $hors_spe_ccas_cias ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $hors_spe_autre_organisme ) );?></td>
			</tr>
		</tbody>

	<?php endforeach;?>
	</table>

	<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv_tableau1', 'visualisation' ) + Hash::flatten( $this->request->data, '__' ),
			true
		);
	?></li>
	</ul>

	<?php
		include_once  dirname( __FILE__ ).DS.'precision.ctp' ;
	?>
<?php endif; ?>