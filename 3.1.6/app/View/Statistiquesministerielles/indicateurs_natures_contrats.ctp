<?php require_once( dirname( __FILE__ ).DS.'search.ctp' ); ?>
<?php if( !empty( $this->request->data ) ): ?>
	<h2>2 - Nature des actions d'insertion inscrites dans les contrats d'engagement réciproque en cours de validité</h2>
	<?php
		$annee = Hash::get( $this->request->data, 'Search.annee' );
	?>
	<table>
		<caption>2 - Nature des actions d'insertion inscrites dans les contrats d'engagement réciproque en cours de validité au 31 décembre de l'année <?php echo $annee;?> (voir notice)</caption>
		<tbody>
			<tr>
				<th><strong>a. Actions des contrats d'engagement réciproque en cours de validité <u>au 31 décembre</u> pour les personnes dont le référent unique <u>au 31 décembre</u> appartenait à un <u>organisme appartenant ou participant au SPE autre que Pôle emploi (1)</u></strong></th>
				<td><strong><?php echo $this->Locale->number( Hash::get( $results, 'Indicateurnature.delai_moyen_orientation' ) );?></strong></td>
			</tr>
			<?php foreach( $results['Indicateurnature']['spe'] as $label => $count ):?>
				<tr>
					<th><?php echo $label;?></th>
					<td><?php
						if( $count !== null ) {
							echo $this->Locale->number( $count );
						}
						else {
							echo 'ND';
						}
					?></td>
				</tr>
			<?php endforeach;?>
			<tr>
				<th><strong>b. Actions des contrats d'engagement réciproque en cours de validité <u>au 31 décembre</u> pour les personnes dont le référent unique <u>au 31 décembre</u> appartenait à un <u>organisme n'appartenant et ne participant pas au SPE</u> (2)</strong></th>
				<td><strong><?php echo $this->Locale->number( Hash::get( $results, 'Indicateurnature.delai_moyen_orientation' ) );?></strong></td>
			</tr>
			<?php foreach( $results['Indicateurnature']['horsspe'] as $label => $count ):?>
				<tr>
					<th><?php echo $label;?></th>
					<td><?php
						if( $count !== null ) {
							echo $this->Locale->number( $count );
						}
						else {
							echo 'ND';
						}
					?></td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	<p>(1) Selon la loi, un <strong>Contrat d'Engagement Réciproque en matière d'insertion professionnelle</strong> (L262-35) est signé par la personne bénéficiaire
		du RSA orientée vers un <strong>organisme appartenant ou participant au service public de l'emploi (SPE) autre que Pôle emploi</strong> : organismes
		publics (ou émanant de collectivités publiques) de placement professionnel (PLIE, AFPA, maison de l'emploi, mission locale, etc.) autres que Pôle
		emploi, organismes d'appui à la création et au développement d'entreprise, entreprises de travail temporaire, agences privées de placement,
		structures d'insertion par l'activité économique (IAE), autres organismes privés de placement professionnel. Le <strong>SPE</strong> est compris dans cette enquête
		au sens large.<br/>
		Un contrat ayant <u>plusieurs actions inscrites</u> sera comptabilisé autant de fois qu'il y a d'actions.</p>
	<p>(2) Selon la loi, un <strong>Contrat d'Engagement Réciproque en matière d'insertion sociale ou professionnelle</strong> (L262-36) est signé par la personne
		bénéficiaire du RSA orientée vers un <strong>organisme n'appartenant et ne participant pas au service public de l'emploi (SPE)</strong> : Conseil
		départemental, Métropole de Lyon, Agence départementale d'insertion dans certains DOM, Conseil territorial dans les COM, Caf, Msa, CCAS/CIAS,
		associations d'insertion non classées dans le SPE, autres organismes d'insertion non classés dans le SPE.<br/>
		Un contrat ayant <u>plusieurs actions inscrites</u> sera comptabilisé autant de fois qu'il y a d'actions.</p>
	<p>(3) Dans les DOM hors Mayotte, il est possible de conclure avec un bénéficiaire du revenu de solidarité active un contrat d'insertion par l'activité
		(CIA). Le titulaire d'un CIA est affecté à l'exécution de tâches d'utilité sociale (art. L.522-8 du CASF).</p>
<?php endif;?>