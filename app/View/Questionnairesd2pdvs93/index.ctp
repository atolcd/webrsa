<?php
	echo $this->Default3->titleForLayout( $personne );

	echo $this->element( 'ancien_dossier' );

	App::uses( 'WebrsaAccess', 'Utility' );
	WebrsaAccess::init( $dossierMenu );

	echo $this->Default3->actions(
		array(
			"/Questionnairesd2pdvs93/add/{$personne['Personne']['id']}" => array(
				'disabled' => false === WebrsaAccess::addIsEnabled( "/Questionnairesd2pdvs93/add/{$personne['Personne']['id']}", $ajoutPossible )
			),
		)
	);

	// A-t'on des messages à afficher à l'utilisateur ?
	echo $this->Default3->messages( $messages );

	echo $this->Default3->index(
		$questionnairesd2pdvs93,
		array(
			'Questionnaired2pdv93.toujoursenemploi' => array( 'type' => 'boolean' ),
			'Questionnaired1pdv93.Rendezvous.Structurereferente.lib_struc',
			'Questionnaired2pdv93.date_validation',
			'Questionnaired2pdv93.modified',
			'Questionnaired2pdv93.situationaccompagnement',
			'Sortieaccompagnementd2pdv93.name',
			'Questionnaired2pdv93.chgmentsituationadmin',
			'Emploiromev3.Appellationromev3.name',
			'Dureeemploi.name',
		)+ WebrsaAccess::links(
			array(
				'/Questionnairesd2pdvs93/edit/#Questionnaired2pdv93.id#',
				'/Questionnairesd2pdvs93/delete/#Questionnaired2pdv93.id#' => array(
					'confirm' => true
				)
			)
		),
        array(
            'options' => $options,
			'paginate' => false,
			'domain' => 'questionnairesd2pdvs93'
        )
	);
?>