<?php
	echo $this->Default3->titleForLayout( $personne );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default3->form(
		array(
			'Situationallocataire.id' => array( 'type' => 'hidden' ),
			'Situationallocataire.personne_id' => array( 'type' => 'hidden' ),
			'Situationallocataire.qual' => array( 'type' => 'hidden' ),
			'Situationallocataire.nom' => array( 'type' => 'hidden' ),
			'Situationallocataire.prenom' => array( 'type' => 'hidden' ),
			'Situationallocataire.nomnai' => array( 'type' => 'hidden' ),
			'Situationallocataire.nir' => array( 'type' => 'hidden' ),
			'Situationallocataire.sexe' => array(
				'view' => true,
				'type' => 'text',
				'hidden' => true,
				'options' => $options['Situationallocataire']['sexe']
			),
			'Situationallocataire.dtnai' => array( 'type' => 'hidden' ),
			'Situationallocataire.rolepers' => array( 'type' => 'hidden' ),
			'Situationallocataire.toppersdrodevorsa' => array( 'type' => 'hidden' ),
			'Situationallocataire.nati' => array(
				'empty' => true,
				'options' => $options['Situationallocataire']['nati'],
				'required' => true
			),
			'Situationallocataire.identifiantpe' => array( 'type' => 'hidden' ),
			'Situationallocataire.datepe' => array( 'type' => 'hidden' ),
			'Situationallocataire.etatpe' => array( 'type' => 'hidden' ),
			'Situationallocataire.codepe' => array( 'type' => 'hidden' ),
			'Situationallocataire.motifpe' => array( 'type' => 'hidden' ),
			'Situationallocataire.numvoie' => array( 'type' => 'hidden' ),
			'Situationallocataire.libtypevoie' => array( 'type' => 'hidden' ),
			'Situationallocataire.nomvoie' => array( 'type' => 'hidden' ),
			'Situationallocataire.complideadr' => array( 'type' => 'hidden' ),
			'Situationallocataire.compladr' => array( 'type' => 'hidden' ),
			'Situationallocataire.numcom' => array( 'type' => 'hidden' ),
			'Situationallocataire.codepos' => array( 'type' => 'hidden' ),
			'Situationallocataire.nomcom' => array( 'type' => 'hidden' ),
			'Situationallocataire.numdemrsa' => array( 'type' => 'hidden' ),
			'Situationallocataire.matricule' => array( 'type' => 'hidden' ),
			'Situationallocataire.fonorg' => array( 'type' => 'hidden' ),
			'Situationallocataire.etatdosrsa' => array( 'type' => 'hidden' ),
			'Situationallocataire.sitfam' => array( 'type' => 'hidden' ),
			'Situationallocataire.sitfam_view' => array(
				'options' => $options['Situationallocataire']['sitfam_view'],
				'view' => true,
				'type' => 'text',
				'hidden' => true
			),
			'Situationallocataire.nbenfants' => array( 'type' => 'hidden' ),
			'Situationallocataire.dtdemrsa' => array( 'type' => 'hidden' ),
			'Situationallocataire.dtdemrmi' => array( 'type' => 'hidden' ),
			'Situationallocataire.statudemrsa' => array( 'type' => 'hidden' ),
			'Situationallocataire.numdepins' => array( 'type' => 'hidden' ),
			'Situationallocataire.typeserins' => array( 'type' => 'hidden' ),
			'Situationallocataire.numcomins' => array( 'type' => 'hidden' ),
			'Situationallocataire.numagrins' => array( 'type' => 'hidden' ),
//			'Situationallocataire.natpf_serialize' => array( 'type' => 'hidden' ),
			'Situationallocataire.natpf_socle' => array( 'type' => 'hidden' ),
			'Situationallocataire.natpf_majore' => array( 'type' => 'hidden' ),
			'Situationallocataire.natpf_activite' => array( 'type' => 'hidden' ),
			/*'Situationallocataire.natpf_view' => array(
				'options' => $options['Situationallocataire']['natpf_view'],
				'view' => true,
				'type' => 'text',
				'hidden' => true
			),*/
			'Situationallocataire.natpf_d1' => array(
				'options' => $options['Situationallocataire']['natpf_d1'],
				'view' => true,
				'type' => 'text',
				'hidden' => true
			),
			'Situationallocataire.tranche_age_view' => array(
				'options' => $options['Situationallocataire']['tranche_age_view'],
				'view' => true,
				'type' => 'text',
				'hidden' => true
			),
			'Situationallocataire.anciennete_dispositif_view' => array(
				'options' => $options['Situationallocataire']['anciennete_dispositif_view'],
				'view' => true,
				'type' => 'text',
				'hidden' => true
			),
			'Questionnaired1pdv93.id' => array( 'type' => 'hidden' ),
			'Questionnaired1pdv93.personne_id' => array( 'type' => 'hidden' ),
			'Questionnaired1pdv93.rendezvous_id' => array( 'type' => 'hidden' ),
			'Questionnaired1pdv93.inscritpe' => array(
				'options' => $options['Questionnaired1pdv93']['inscritpe'],
				'empty' => true,
				'required' => true
			),
			'Questionnaired1pdv93.marche_travail' => array(
				'options' => $options['Questionnaired1pdv93']['marche_travail'],
				'empty' => true,
				'required' => true
			),
			'Questionnaired1pdv93.vulnerable' => array(
				'options' => $options['Questionnaired1pdv93']['vulnerable'],
				'empty' => true,
				'required' => true
			),
			'Questionnaired1pdv93.diplomes_etrangers' => array(
				'options' => $options['Questionnaired1pdv93']['diplomes_etrangers'],
				'empty' => true,
				'required' => true
			),
			'Questionnaired1pdv93.categorie_sociopro' => array(
				'options' => $options['Questionnaired1pdv93']['categorie_sociopro'],
				'empty' => true,
				'required' => true
			),
			'Questionnaired1pdv93.nivetu' => array(
				'options' => $options['Questionnaired1pdv93']['nivetu'],
				'view' => true,
				'type' => 'text',
				'hidden' => true
			),
			'Questionnaired1pdv93.autre_caracteristique' => array(
				'options' => $options['Questionnaired1pdv93']['autre_caracteristique'],
				'view' => true,
				'type' => 'text',
				'hidden' => true
			),
			'Questionnaired1pdv93.autre_caracteristique_autre' => array(
				'type' => 'hidden'
			),
			'Questionnaired1pdv93.conditions_logement' => array(
				'options' => $options['Questionnaired1pdv93']['conditions_logement'],
				'empty' => true,
				'required' => true
			),
			'Questionnaired1pdv93.conditions_logement_autre' => array(
				'required' => true
			),
			'Questionnaired1pdv93.date_validation' => array(
				'type' => 'hidden',
				'empty' => true
			),
		),
		array(
			'buttons' => array( 'Validate', 'Cancel' )
		)
	);
?>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		observeDisableFieldsOnValue(
			'Questionnaired1pdv93AutreCaracteristique',
			[ 'Questionnaired1pdv93AutreCaracteristiqueAutre' ],
			[ 'autres' ],
			false,
			false
		);

		observeDisableFieldsOnValue(
			'Questionnaired1pdv93ConditionsLogement',
			[ 'Questionnaired1pdv93ConditionsLogementAutre' ],
			[ 'autre' ],
			false,
			false
		);
	} );
</script>