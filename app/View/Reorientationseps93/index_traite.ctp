<h1> <?php echo $this->pageTitle = 'Écran de synthèse des demandes de réorientation étudiées en EP'; ?> </h1>
<?php
	require_once( 'index.ctp' );

	echo $this->Default2->index(
		$reorientationseps93,
		array(
			'Reorientationep93.created' => array( 'type' => 'date' ),
			// Allocataire
			'Dossierep.Personne.nom',
			'Dossierep.Personne.prenom',
			// Orientation de départ
			'Orientstruct.Typeorient.lib_type_orient',
			'Orientstruct.Structurereferente.lib_struc',
			// Orientation d'accueil
			'Typeorient.lib_type_orient',
			'Structurereferente.lib_struc',
			'Dossierep.Passagecommissionep.0.etatdossierep',
			'Dossierep.Passagecommissionep.0.Decisionreorientationep93.0.decision',
			'Dossierep.Passagecommissionep.0.Decisionreorientationep93.0.Typeorient.lib_type_orient' => array( 'type' => 'text' ),
			'Dossierep.Passagecommissionep.0.Decisionreorientationep93.0.Structurereferente.lib_struc' => array( 'type' => 'text' ),
			'Dossierep.Passagecommissionep.0.Commissionep.dateseance' => array( 'type' => 'date' ),
		),
		array(
			'groupColumns' => array(
				'Dossier' => array( 1, 2 ),
				'Service référent demandeur' => array( 3, 4 ),
				'Service référent d\'accueil' => array( 5, 6, 7 ),
				'Réorientation finale' => array( 9, 10 ),
			),
			'paginate' => 'Reorientationep93',
			'options' => $options
		)
	);
?>