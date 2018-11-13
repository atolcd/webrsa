<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ; ?>
<?php if( !empty( $this->request->data ) ): ?>
	<h2>1 - Orientation des personnes dans le champ des Droits et Devoirs au 31 décembre de l'année, au sens du type de parcours (voir notice)</h2>
	<?php
		$annee = Hash::get( $this->request->data, 'Search.annee' );
		$indicateurs = array( 'age', 'sitfam', 'nivetu', 'anciennete' );
	?>
	<?php foreach( $indicateurs as $index => $indicateur ):?>
	<?php
		$name = "Indicateur{$indicateur}";
		$sdd = (array)Hash::get( $results, "{$name}.sdd" );
		$orient_pro = (array)Hash::get( $results, "{$name}.orient_pro" );
		$orient_sociopro = (array)Hash::get( $results, "{$name}.orient_sociopro" );
		$orient_sociale = (array)Hash::get( $results, "{$name}.orient_sociale" );
		$attente_orient = (array)Hash::get( $results, "{$name}.attente_orient" );

		if( $index == 0 ) {
			$class = 'first';
		}
		else if( $index + 1 == count( $indicateurs ) ) {
			$class = 'last';
		}
		else {
			$class = 'middle';
		}
	?>
	<table class="<?php echo $class;?>">
		<caption>Orientation des personnes dans le champ des Droits et Devoirs au 31 décembre de l'année <?php echo $annee;?>, au sens du type de parcours</caption>
		<thead>
			<tr class="main">
				<th rowspan="2">Catégorie</th>
				<th rowspan="2">Personnes dans le champ des Droits et Devoirs au 31 décembre (1)</th>
				<th colspan="4">dont:</th>
			</tr>
			<tr class="main">
				<th>Personnes dans le champ des Droits et Devoirs et orientées en parcours professionnel au 31 décembre (2)</th>
				<th>Personnes dans le champ des Droits et Devoirs et orientées en parcours socioprofessionnel au 31 décembre (2)</th>
				<th>Personnes dans le champ des Droits et Devoirs et orientées en parcours social au 31 décembre (2)</th>
				<th>Personnes dans le champ des Droits et Devoirs non-orientées au 31 décembre</th>
			</tr>
			<tr class="category">
				<th colspan="6"><?php echo __d( 'statistiquesministerielles', $name );?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Effectif total</th>
				<td><?php echo $this->Locale->number( array_sum( $sdd ) );?></td>
				<td><?php echo $this->Locale->number( array_sum( $orient_pro ) );?></td>
				<td><?php echo $this->Locale->number( array_sum( $orient_sociopro ) );?></td>
				<td><?php echo $this->Locale->number( array_sum( $orient_sociale ) );?></td>
				<td><?php echo $this->Locale->number( array_sum( $attente_orient ) );?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach( $tranches[$indicateur] as $tranche ):?>
			<tr>
				<th><?php echo __d( 'statistiquesministerielles',  $tranche );?></th>
				<td class="number"><?php echo  isset( $sdd[$tranche] ) ? $this->Locale->number( $sdd[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $orient_pro[$tranche] ) ? $this->Locale->number( $orient_pro[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $orient_sociopro[$tranche] ) ? $this->Locale->number( $orient_sociopro[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $orient_sociale[$tranche] ) ? $this->Locale->number( $orient_sociale[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $attente_orient[$tranche] ) ? $this->Locale->number( $attente_orient[$tranche] ) : 0 ;?></td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	<?php endforeach;?>

	<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv_orientations', 'visualisation' ) + Hash::flatten( $this->request->data, '__' ),
			true
		);
	?></li>
	</ul>

	<p>(1) Les <strong>personnes</strong> sont définies comme les adultes du foyer, c'est-à-dire les allocataires et conjoints appartenant à un foyer ayant un droit ouvert au RSA. Selon la
		loi, une personne relève du périmètre des <strong>droits et devoirs</strong> (L262-28) lorsqu'elle appartient à un foyer ayant un droit ouvert au RSA socle et si elle est sans emploi
		ou a un revenu d'activité professionnelle inférieur à 500 euros par mois. La définition des droits et devoirs à retenir est celle des organismes payeurs.</p>
	<p>(2) L'<strong>orientation</strong> peut être professionnelle, sociale ou, pour certains conseils départementaux, socioprofessionnelle. La définition des parcours professionnel,
		socioprofessionnel et social est laissée à la libre-appréciation du conseil départemental, en fonction des spécificités locales.
		Selon la loi, l'orientation concerne une personne, et non un foyer.<br/>
		Les <strong>personnes</strong> sont définies comme les adultes du foyer, c'est-à-dire les allocataires et conjoints appartenant à un foyer ayant un droit ouvert au RSA. Selon la loi,
		une personne relève du périmètre des <strong>droits et devoirs</strong> (L262-28) lorsqu'elle appartient à un foyer ayant un droit ouvert au RSA socle et si elle est sans emploi ou a
		un revenu d'activité professionnelle inférieur à 500 euros par mois. La définition des droits et devoirs à retenir est celle des organismes payeurs.</p>
	<p>(3) L'ancienneté dans le dispositif est mesurée par rapport à la dernière date d'entrée dans le dispositif, y compris anciens minima (RMI, API). Le passage
		automatique du RMI/API au RSA au moment de l'entrée en vigueur du RSA n'est pas considéré comme une entrée.</p>
<?php endif; ?>