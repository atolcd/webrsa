<?php $this->pageTitle =  __d( 'contratinsertion', "Contratsinsertion::{$this->action}" ); ?>

<?php
	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Form->create( 'Contratsinsertionview', array( 'type' => 'post', 'id' => 'contratform', 'novalidate' => true ) );

	if ( isset( $contratinsertion['Contratinsertion']['avenant_id'] ) && !empty( $contratinsertion['Contratinsertion']['avenant_id'] ) ) {
		$num = 'Avenant';
	}
	else{
		if( Configure::read( 'Cg.departement' ) == 66 ) {
			$num = $contratinsertion['Contratinsertion']['num_contrat_66'];
			$num = Set::enum( $num, $options['num_contrat_66'] );
		}
		else {
			$num = Set::enum( $contratinsertion['Contratinsertion']['num_contrat'], $options['num_contrat'] );
		}
	}
	$duree = "{$contratinsertion['Contratinsertion']['duree_engag']} mois";
	$forme = Set::enum( $contratinsertion['Contratinsertion']['forme_ci'], $forme_ci );
	$decision_ci = Set::enum( $contratinsertion['Contratinsertion']['decision_ci'], $decision_ci );

	if( Configure::read( 'Cg.departement' ) == 58 ) {
		echo $this->Default2->view(
			$contratinsertion,
			array(
				'Personne.nom_complet' => array( 'type' => 'text' ),
				'Contratinsertion.num_contrat' => array( 'type' => 'text', 'value' => $num ),
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Referent.nom_complet' => array( 'type' => 'text' ),
				'Contratinsertion.rg_ci' => array( 'type' => 'text' ),
				'Contratinsertion.duree_engag' => array( 'type' => 'text', 'value' => $duree ),
				'Contratinsertion.dd_ci',
				'Contratinsertion.df_ci'
			),
			array( 'id' => 'vueContrat' )
		);
	}
	else if( Configure::read( 'Cg.departement' ) == 66 ) {

		if( ( $contratinsertion['Contratinsertion']['positioncer'] == 'annule' ) ){

			echo $this->Xhtml->tag('div', $this->Xhtml->tag('strong', 'Raison de l\'annulation'));
			echo $this->Default->view(
				$contratinsertion,
				array(
					'Contratinsertion.motifannulation' => array( 'type' => 'text' )
				),
				array(
					'widget' => 'table',
					'class' => 'aere'
				)
			);

		}
		else if( ( $contratinsertion['Contratinsertion']['positioncer'] == 'annule' ) && ( $contratinsertion['Contratinsertion']['decision_ci'] == 'N' ) ){

			echo $this->Xhtml->tag('div', $this->Xhtml->tag('strong', 'Raison de l\'annulation'));
			echo $this->Default->view(
				$contratinsertion,
				array(
					'Contratinsertion.observ_ci' => array( 'type' => 'text' )
				),
				array(
					'widget' => 'table',
					'class' => 'aere'
				)
			);
		}

		echo $this->Default2->view(
			$contratinsertion,
			array(
				'Personne.nom_complet' => array( 'type' => 'text' ),
				'Contratinsertion.forme_ci' => array( 'type' => 'text', 'value' => $forme  ),
				'Contratinsertion.num_contrat' => array( 'type' => 'text', 'value' => $num ),
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Referent.nom_complet' => array( 'type' => 'text' ),
				'Contratinsertion.rg_ci' => array( 'type' => 'text' ),
				'Contratinsertion.sitfam_ci',
				'Contratinsertion.sitpro_ci',
				'Contratinsertion.observ_benef',
				'Contratinsertion.nature_projet',
				'Contratinsertion.engag_object',
				'Contratinsertion.duree_engag' => array( 'type' => 'text', 'value' => $duree ),
				'Contratinsertion.dd_ci',
				'Contratinsertion.df_ci',
				'Contratinsertion.lieu_saisi_ci' => array( 'type' => 'text' ),
				'Contratinsertion.date_saisi_ci'
			),
			array( 'class' => 'aere', 'id' => 'vueContrat' )
		);
// 			debug( $contratinsertion );
		echo '<h2 >Décision concernant le CER</h2>';
		echo $this->Default2->view(
			$contratinsertion,
			array(
				'Contratinsertion.decision_ci' => array( 'type' => 'text', 'value' => $decision_ci ),
				'Contratinsertion.datedecision' => array( 'type' => 'date' ),
				'Propodecisioncer66.listeMotifs66' => array( 'label' => 'Motif(s) de non validation', 'type' => 'text' )
			),
			array(
				'widget' => 'table',
				'class' => 'aere'
			)
		);

		echo '<h2 >Actions déjà en cours</h2>';
		if( !empty( $fichescandidature ) ) {
			foreach( $fichescandidature as $fichecandidature ){
				echo $this->Default2->view(
					$fichecandidature,
					array(
						'Actioncandidat.name' => array( 'type' => 'text' ),
						'Actioncandidat.Contactpartenaire.Partenaire.libstruc' => array( 'type' => 'text' ),
						'Referent.nom' => array( 'type' => 'text', 'value' => '#Referent.qual# #Referent.nom# #Referent.prenom#' ),
						'Actioncandidat.ddaction',
						'Actioncandidat.hasfichecandidature' => array( 'type' => 'boolean' )
					)
				);
			}
		}
		else{
			echo '<p class="notice">Aucune action en cours</p>';
		}
	}
	else if( Configure::read( 'Cg.departement' ) == 93 ) {
		echo $this->Default2->view(
			$contratinsertion,
			array(
				'Personne.nom_complet' => array( 'type' => 'text' ),
				'Contratinsertion.forme_ci' => array( 'type' => 'text', 'value' => $forme  ),
				'Contratinsertion.num_contrat' => array( 'type' => 'text', 'value' => $num ),
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Referent.nom_complet' => array( 'type' => 'text' ),
				'Contratinsertion.rg_ci' => array( 'type' => 'text' ),
				'Contratinsertion.diplomes',
				'Contratinsertion.expr_prof',
				'Contratinsertion.form_compl',
				'Contratinsertion.actions_prev' => array( 'type' => 'boolean' ),
				'Contratinsertion.obsta_renc',
				'Contratinsertion.nature_projet',
				'Action.libelle' => array( 'label' => 'Action engagée' ),
				'Actioninsertion.dd_action' => array( 'label' => 'Date de début de l\'action' ),
				'Contratinsertion.duree_engag' => array( 'type' => 'text', 'value' => $duree ),
				'Contratinsertion.dd_ci',
				'Contratinsertion.df_ci',
				'Contratinsertion.lieu_saisi_ci' => array( 'type' => 'text' ),
				'Contratinsertion.date_saisi_ci',
			)
		);
	}
	else if( Configure::read( 'Cg.departement' ) == 976 ) {
		$options = Hash::merge(
			$options,
			array(
				'Contratinsertion' => array(
					'decision_ci' => $this->viewVars['decision_ci'],
					'duree_engag' => $this->viewVars['duree_engag'],
				)
			)
		);

		echo $this->Default3->view(
			$contratinsertion,
			array(
				'Personne.nom_complet',
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Referent.nom_complet',
				'Contratinsertion.rg_ci',
				'Contratinsertion.nature_projet',
				'Contratinsertion.observ_ci',
				'Contratinsertion.observ_benef',
				'Contratinsertion.duree_engag' => array( 'type' => 'text' ),
				'Contratinsertion.dd_ci',
				'Contratinsertion.df_ci',
				'Contratinsertion.lieu_saisi_ci',
				'Contratinsertion.date_saisi_ci',
				// Motifs de refus ?
				'Contratinsertion.decision_ci',
				'Contratinsertion.datedecision',
			),
			array(
				'options' => $options
			)
		);
	}
?>
<div class="submit">
	<?php echo $this->Xform->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) ); ?>
</div>
<?php echo $this->Xform->end();?>