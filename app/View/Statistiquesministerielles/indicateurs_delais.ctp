<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ; ?>
<?php if( !empty( $this->request->data ) ): ?>
	<h2>3 - Délais entre les différentes étapes de l'orientation au cours de l'année</h2>
	<?php
		$annee = Hash::get( $this->request->data, 'Search.annee' );
	?>
	<table>
		<caption>Délais entre les différentes étapes de l'orientation au cours de l'année <?php echo $annee;?></caption>
		<tbody>
			<tr>
				<th><strong>a. Délai moyen entre la date d'ouverture de droit, telle qu'enregistrée par les organismes chargés du service de l'allocation, et la décision d'orientation validée par <?php echo __d('default'.Configure::read('Cg.departement'), 'le Président du Conseil Général');?> au cours de l'année (1)</strong></th>
				<td><strong><?php echo $this->Locale->number( Hash::get( $results, 'Indicateurdelai.delai_moyen_orientation' ) );?></strong></td>
			</tr>
			<tr>
				<th><strong>b. Délai moyen entre la décision d'orientation et la signature d'un contrat au cours de l'année (2)</strong></th>
				<td><strong><?php echo $this->Locale->number( Hash::get( $results, 'Indicateurdelai.delai_moyen_signature' ) );?></strong></td>
			</tr>
			<?php foreach( $types_cers as $type_cer => $delais ):?>
				<tr>
					<th><strong><?php echo __d( 'statistiquesministerielles', "Indicateurdelai.{$type_cer}_delai_moyen" );?></strong></th>
					<td><strong><?php
						$value = Hash::get( $results, "Indicateurdelai.{$type_cer}_delai_moyen" );
						$value = ( is_null( $value ) ? 'ND' : $this->Locale->number( $value ) );
						echo $value;
					?></strong></td>
				</tr>
				<tr>
					<th><?php echo __d( 'statistiquesministerielles', "Indicateurdelai.{$type_cer}_nombre_moyen" );?></th>
					<td><?php
						$value = Hash::get( $results, "Indicateurdelai.{$type_cer}_nombre_moyen" );
						$value = ( is_null( $value ) ? 'ND' : $this->Locale->number( $value ) );
						echo $value;
					?></td>
				</tr>
				<tr>
					<th><?php echo __d( 'statistiquesministerielles', "Indicateurdelai.{$type_cer}_delai_{$delais['nbMoisTranche1']}_mois" );?></th>
					<td><?php
						$value = Hash::get( $results, "Indicateurdelai.{$type_cer}_delai_{$delais['nbMoisTranche1']}_mois" );
						$value = ( is_null( $value ) ? 'ND' : $this->Locale->number( $value ) );
						echo $value;
					?></td>
				</tr>
				<tr>
					<th><?php echo __d( 'statistiquesministerielles', "Indicateurdelai.{$type_cer}_delai_{$delais['nbMoisTranche1']}_{$delais['nbMoisTranche2']}_mois" );?></th>
					<td><?php
						$value = Hash::get( $results, "Indicateurdelai.{$type_cer}_delai_{$delais['nbMoisTranche1']}_{$delais['nbMoisTranche2']}_mois" );
						$value = ( is_null( $value ) ? 'ND' : $this->Locale->number( $value ) );
						echo $value;
					?></td>
				</tr>
				<tr>
					<th><?php echo __d( 'statistiquesministerielles', "Indicateurdelai.{$type_cer}_delai_plus_{$delais['nbMoisTranche2']}_mois" );?></th>
					<td><?php
						$value = Hash::get( $results, "Indicateurdelai.{$type_cer}_delai_plus_{$delais['nbMoisTranche2']}_mois" );
						$value = ( is_null( $value ) ? 'ND' : $this->Locale->number( $value ) );
						echo $value;
					?></td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>

	<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv_delais', 'visualisation' ) + Hash::flatten( $this->request->data, '__' ),
			true
		);
	?></li>
	</ul>

	<p>(1) Sur le champ des bénéficiaires dans le champ des droits et devoirs dont la date d'orientation (première orientation)
		est dans l'année (la date d'ouverture de droit n'est pas nécessairement dans l'année). On considère que la date
		d'ouverture de droit correspond à la date de dépôt de la demande, c'est-à-dire, selon la loi, le premier jour du mois du
		dépôt de la demande.</p>
	<p>(2) Sur le champ des bénéficiaires dans le champ des droits et devoirs dont la date d'orientation (première orientation)
		est dans l'année et dont la date de signature du contrat est dans l'année (en ne comptant que les primo-contrats et non
		les renouvellements de contrat). Par ailleurs, pour le PPAE, le champ se limite aux personnes qui signent nouvellement
		un PPAE (hors personnes qui en ont déjà un avant le processus d'orientation).</p>
	<p>(3) Sur le champ des bénéficiaires dans le champ des droits et devoirs dont la date d'orientation (première orientation)
		est dans l'année et dont la date de signature du contrat est dans l'année (en ne comptant que les primo-contrats et non
		les renouvellements de contrat).<br/>
		La personne bénéficiaire du RSA orientée vers Pôle emploi signe un <strong>PPAE</strong> (L262-34). Les PPAE signés par une
		personne bénéficiaire du RSA mais dont le référent unique n'appartient pas à <strong>Pôle emploi</strong> ne sont pas à prendre en
		compte.<br/>
		Pour les délais, le champ se limite également aux personnes qui signent nouvellement un PPAE (hors personnes qui en
		ont déjà un avant le processus d'orientation).</p>
	<p>(4) Sur le champ des bénéficiaires dans le champ des droits et devoirs dont la date d'orientation (première orientation)
		est dans l'année et dont la date de signature du contrat est dans l'année (en ne comptant que les primo-contrats et non
		les renouvellements de contrat).<br/>
		Selon la loi, un <strong>Contrat d'Engagement Réciproque en matière d'insertion professionnelle</strong> (L262-35) est signé par la
		personne bénéficiaire du RSA orientée vers un <strong>organisme participant au service public de l'emploi (SPE) autre que
		Pôle emploi</strong> : autres organismes publics de placement professionnel (PLIE, AFPA, maison de l'emploi, mission locale,
		etc.), organismes d'appui à la création et au développement d'entreprise, entreprises de travail temporaire, agences
		privées de placement, insertion par l'activité économique (IAE), autres organismes publics ou privés de placement
		professionnel.. Le <strong>SPE</strong> est compris au sens large.</p>
	<p>(5) Sur le champ des bénéficiaires dans le champ des droits et devoirs dont la date d'orientation (première orientation)
		est dans l'année et dont la date de signature du contrat est dans l'année (en ne comptant que les primo-contrats et non
		les renouvellements de contrat).<br/>
		Selon la loi, un <strong>Contrat d'Engagement Réciproque en matière d'insertion sociale ou professionnelle</strong> (L262-36) est
		signé par la personne bénéficiaire du RSA orientée vers un <strong>autre organisme</strong> : Conseil général, Caf, Msa, CCAS/CIAS,
		associations d'insertion, autres organismes d'insertion, Agence départementale d'insertion dans les DOM.</p>
<?php endif;?>