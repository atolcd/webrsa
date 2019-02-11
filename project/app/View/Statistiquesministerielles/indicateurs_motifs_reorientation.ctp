<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ; ?>

<?php if( !empty( $this->request->data ) ): ?>
	<?php $annee = Hash::get( $this->request->data, 'Search.annee' ); ?>

	<h2>4a - Motifs des réorientations d'un organisme appartenant ou participant au SPE vers un organisme hors SPE au cours de l'année</h2>
	<table>
		<caption>Motifs des réorientations d'un organisme appartenant ou participant au SPE vers un organisme hors SPE au cours de l'année <?php echo $annee;?></caption>
		<tbody>
			<tr class="total">
				<th>Nombre de personnes réorientées d'un organisme appartenant ou participant au SPE vers un organisme hors SPE au cours de l'année (1)</th>
				<td class="number"><?php echo $this->Locale->number( (int)Hash::get( $results, 'Indicateursocial.total' ) );?></td>
			</tr>
			<tr>
				<th> - orientation initiale inadaptée</th>
				<td class="number"><?php
					$value = Hash::get( $results, "Indicateursocial.orientation_initiale_inadaptee" );
					if( is_null( $value ) ) {
						echo 'ND';
					}
					else {
						echo $this->Locale->number( $value );
					}
				?></td>
			</tr>
			<tr>
				<th> - changement de situation de la personne (difficultés nouvelles de logement, santé, garde d'enfants, famille, ...)</th>
				<td class="number"><?php
					$value = Hash::get( $results, "Indicateursocial.changement_situation_allocataire" );
					if( is_null( $value ) ) {
						echo 'ND';
					}
					else {
						echo $this->Locale->number( $value );
					}
				?></td>
			</tr>
			<tr>
				<th> - autres</th>
				<td class="number"><?php echo $this->Locale->number( (int)Hash::get( $results, 'Indicateursocial.autre' ) );?></td>
			</tr>
		</tbody>
	</table>
	<p>(1) SPE : service public de l'emploi.<br/>
		<strong>Organismes appartenant ou participant au SPE</strong> : Pôle emploi, autres organismes publics de placement
		professionnel (PLIE, AFPA, maison de l'emploi, mission locale, etc.), organismes d'appui à la création et au
		développement d'entreprise, entreprises de travail temporaire, agences privées de placement, insertion par l'activité
		économique (IAE), autres organismes publics ou privés de placement professionnel.<br/>
		<strong>Organismes hors SPE</strong> : Conseil départemental, Caf, Msa, CCAS/CIAS, associations d'insertion, autres organismes
		d'insertion, Agence départementale d'insertion dans les DOM.</p>
	<p>Si une personne a été réorientée <em>plusieurs fois</em> au cours de l'année, indiquer uniquement le motif de sa dernière
		réorientation.</p>

	<h2>4b - Recours à l'article L262-31 au cours de l'année</h2>
	<table>
		<caption>4b - Recours à l'article L262-31 au cours de l'année <?php echo $annee;?></caption>
		<tbody>
			<tr class="total">
				<th>Nombre de personnes dont le dossier a été examiné par l'équipe pluridisciplinaire dans le cadre de l'article L262-31 (à l'issue du délai de 6 à 12 mois sans réorientation vers le SPE) au cours de l'année (1)</th>
				<td class="number"><?php echo $this->Locale->number( (int)Hash::get( $results, 'Indicateurep.total' ) );?></td>
			</tr>
			<tr>
				<th>dont maintien de l'orientation dans un organisme hors SPE (2)</th>
				<td class="number"><?php echo $this->Locale->number( (int)Hash::get( $results, 'Indicateurep.maintien' ) );?></td>
			</tr>
			<tr>
				<th>dont réorientation vers un organisme appartenant ou participant au SPE (2)</th>
				<td class="number"><?php echo $this->Locale->number( (int)Hash::get( $results, 'Indicateurep.reorientation' ) );?></td>
			</tr>
		</tbody>
	</table>
	<p>(1) Selon la loi, si une personne a été orientée vers un organisme compétent en matière d'insertion sociale, sa
		situation est réexaminée au bout de 6 mois (jusqu'à 12 mois dans certains cas), par une équipe pluridisciplinaire
		constituée par le conseil départemental, afin de vérifier si la personne peut s'engager dans un parcours vers l'emploi. Suite
		à cet examen, on compte les personnes maintenues dans un organisme hors SPE et les personnes réorientées vers
		un organisme appartenant ou participant au SPE.<br/>
		Les <strong>personnes</strong> sont définies comme les adultes du foyer, c'est-à-dire les allocataires et conjoints appartenant à un
		foyer ayant un droit ouvert au RSA.</p>
	<p>(2) SPE : service public de l'emploi.<br/>
		<strong>Organismes appartenant ou participant au SPE</strong> : Pôle emploi, autres organismes publics de placement
		professionnel (PLIE, AFPA, maison de l'emploi, mission locale, etc.), organismes d'appui à la création et au
		développement d'entreprise, entreprises de travail temporaire, agences privées de placement, insertion par l'activité
		économique (IAE), autres organismes publics ou privés de placement professionnel.<br/>
		<strong>Organismes hors SPE</strong> : Conseil départemental, Caf, Msa, CCAS/CIAS, associations d'insertion, autres organismes
		d'insertion, Agence départementale d'insertion dans les DOM.</p>
	<p>Si le dossier d'une même personne a été réexaminé <em>plusieurs fois</em> au cours de l'année, ne le compter qu'une fois et
		indiquer uniquement la dernière décision.</p>

	<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv_motifs_reorientation', 'visualisation' ) + Hash::flatten( $this->request->data, '__' ),
			true
		);
	?></li>
	</ul>

<?php endif; ?>