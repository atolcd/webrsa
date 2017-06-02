<h1> <?php echo $this->pageTitle = 'Liste des saisines de demande de réorientation des structures référentes'; ?> </h1>
<?php
	require_once( 'index.ctp' );

	$myServiceinstructeur_id = $this->Session->read( 'Auth.User.serviceinstructeur_id' );
	$myGroup = $this->Session->read( 'Auth.Group.name' );
	$disabled = "(
		'#Dossierep.etatdossierep#' != 'cree'
		|| (
			'#Reorientationep93.structurereferente_id#' != '{$myServiceinstructeur_id}'
			&& '{$myGroup}' != 'Administrateurs'
		)
	)";

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
			'Reorientationep93.accordaccueil' => array( 'type' => 'boolean' ),
			'Reorientationep93.accordallocataire' => array( 'type' => 'boolean' ),
			'Dossierep.Passagecommissionep.0.etatdossierep',
		),
		array(
			'actions' => array(
				'Reorientationseps93::edit' => array(
					'disabled' => $disabled,
				),
				'Reorientationseps93::delete' => array(
					'disabled' => $disabled,
				)
			),
			'groupColumns' => array(
				'Dossier' => array( 1, 2 ),
				'Service référent demandeur' => array( 3, 4 ),
				'Service référent d\'accueil' => array( 5, 6, 7 ),
			),
			'paginate' => 'Reorientationep93',
			'options' => $options
		)
	);
?>