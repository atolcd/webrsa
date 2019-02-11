<?php
	// TODO: bouton add, ...
	echo $this->Default3->titleForLayout();

	echo $this->element( 'ancien_dossier' );

	App::uses( 'WebrsaAccess', 'Utility' );
	WebrsaAccess::init( $dossierMenu );

	echo $this->Default3->actions(
		array(
			"/Fichesprescriptions93/add/{$personne_id}" => array(
				'disabled' => false === WebrsaAccess::addIsEnabled( "/Fichesprescriptions93/add/{$personne_id}", $ajoutPossible )
			),
		)
	);

	// A-t'on des messages à afficher à l'utilisateur ?
	echo $this->Default3->messages( $messages );

	echo $this->Default3->index(
		$results,
		array(
			'Ficheprescription93.created' => array( 'type' => 'date' ),
			'Thematiquefp93.name',
			'Categoriefp93.name',
			'Ficheprescription93.prestaname',
			'Ficheprescription93.actionname',
			'Ficheprescription93.statut',
		)
		+ WebrsaAccess::links(
			array(
				'/Fichesprescriptions93/edit/#Ficheprescription93.id#',
				'/Fichesprescriptions93/cancel/#Ficheprescription93.id#',
				'/Fichesprescriptions93/impression/#Ficheprescription93.id#'
			)
		),
		array(
			'options' => $options,
			'paginate' => false,
			'innerTable'  => array(
				'Ficheprescription93.modified' => array(
					'type' => 'date',
				),
				'Thematiquefp93.type',
				//'Thematiquefp93.yearthema',
				'Ficheprescription93.dd_action' => array(
					'type' => 'date',
				),
				'Ficheprescription93.df_action' => array(
					'type' => 'date',
				),
			)
		)
	);
?>