<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ; ?>
<?php if( !empty( $this->request->data ) ): ?>
	<h2>4 - Nombre et profil des personnes réorientées au cours de l'année, au sens de la loi (voir notice)</h2>
	<?php
		$annee = Hash::get( $this->request->data, 'Search.annee' );
		$indicateurs = array( 'age', 'sitfam', 'nivetu', 'anciennete' );
	?>
	<?php foreach( $indicateurs as $index => $indicateur ):?>
	<?php
		$name = "Indicateur{$indicateur}";
		$reorientes = (array)Hash::get( $results, "{$name}.reorientes" );
		$organismes_hors_spe = (array)Hash::get( $results, "{$name}.organismes_hors_spe" );
		$organismes_spe = (array)Hash::get( $results, "{$name}.organismes_spe" );

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
		<caption>Nombre et profil des personnes réorientées au cours de l'année <?php echo $annee;?>, au sens de la loi</caption>
		<thead>
			<tr class="main">
				<th rowspan="2">Catégorie</th>
				<th rowspan="2">Personnes réorientées au cours de l'année (1)</th>
				<th colspan="3">dont:</th>
			</tr>
			<tr class="main">
				<th>Organismes appartenant ou participant au SPE vers organismes hors SPE (2)</th>
				<th>Organismes hors SPE vers organismes appartenant ou participant au SPE (2)</th>
			</tr>
			<tr class="category">
				<th colspan="4"><?php echo __d( 'statistiquesministerielles', $name );?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Effectif total</th>
				<td><?php echo $this->Locale->number( array_sum( $reorientes ) );?></td>
				<td><?php echo $this->Locale->number( array_sum( $organismes_hors_spe ) );?></td>
				<td><?php echo $this->Locale->number( array_sum( $organismes_spe ) );?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach( $tranches[$indicateur] as $tranche ):?>
			<tr>
				<th><?php echo __d( 'statistiquesministerielles',  $tranche );?></th>
				<td class="number"><?php echo  isset( $reorientes[$tranche] ) ? $this->Locale->number( $reorientes[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $organismes_hors_spe[$tranche] ) ? $this->Locale->number( $organismes_hors_spe[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $organismes_spe[$tranche] ) ? $this->Locale->number( $organismes_spe[$tranche] ) : 0 ;?></td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	<?php endforeach;?>

	<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv_reorientations', 'visualisation' ) + Hash::flatten( $this->request->data, '__' ),
			true
		);
	?></li>
	</ul>

	<p>(1) On entend par <strong>réorientation</strong>, le passage d'un organisme participant au service public de l'emploi (SPE)
		vers un organisme hors SPE, ou réciproquement. Les autres changements d'organisme au sein du SPE ou
		entre organismes hors SPE ne sont pas comptabilisés comme des réorientations dans ce tableau. Le SPE est
		compris au sens large.<br/>
		<strong>Organismes appartenant ou participant au SPE</strong> : Pôle emploi, autres organismes publics de placement
		professionnel (PLIE, AFPA, maison de l'emploi, mission locale, etc.), organismes d'appui à la création et au
		développement d'entreprise, entreprises de travail temporaire, agences privées de placement, insertion par
		l'activité économique (IAE), autres organismes publics ou privés de placement professionnel.<br/>
		<strong>Organismes hors SPE</strong> : Conseil départemental, Caf, Msa, CCAS/CIAS, associations d'insertion, autres organismes
		d'insertion, Agence départementale d'insertion dans les DOM.</p>
	<p>Les <strong>personnes</strong> sont définies comme les adultes du foyer, c'est-à-dire les allocataires et conjoints appartenant
		à un foyer ayant un droit ouvert au RSA. Selon la loi, la réorientation concerne une personne, et non un foyer.</p>
	<p>(2) SPE : service public de l'emploi.<br/>
		<strong>Organismes appartenant ou participant au SPE</strong> : Pôle emploi, autres organismes publics de placement
		professionnel (PLIE, AFPA, maison de l'emploi, mission locale, etc.), organismes d'appui à la création et au
		développement d'entreprise, entreprises de travail temporaire, agences privées de placement, insertion par
		l'activité économique (IAE), autres organismes publics ou privés de placement professionnel.<br/>
		<strong>Organismes hors SPE</strong> : Conseil départemental, Caf, Msa, CCAS/CIAS, associations d'insertion, autres organismes
		d'insertion, Agence départementale d'insertion dans les DOM.</p>
	<p>(3) L'ancienneté dans le dispositif est mesurée par rapport à la dernière date d'entrée dans le dispositif, y
		compris anciens minima (RMI, API). Le passage automatique du RMI/API au RSA au moment de l'entrée en
		vigueur du RSA n'est pas considéré comme une entrée.</p>
	<p>Par ailleurs, si une personne a été réorientée plusieurs fois au cours de l'année, ne la compter qu'une fois et
		indiquer uniquement sa dernière réorientation.</p>
<?php endif; ?>