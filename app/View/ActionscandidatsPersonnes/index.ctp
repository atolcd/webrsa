<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	echo $this->Default3->titleForLayout();

	echo $this->element( 'ancien_dossier' );

	echo $this->Default3->messages( $messages );

	echo $this->Default3->actions(
		array(
			"/ActionscandidatsPersonnes/add/{$personne_id}" => array(
				'disabled' => true !== WebrsaAccess::addIsEnabled(
					'/actionscandidats_personnes/add',
					$ajoutPossible
				)
			)
		)
	);

	echo $this->Default3->index(
		$actionscandidats_personnes,
		$this->Translator->normalize(
			array(
				'Actioncandidat.name',
				'Referent.nom_complet',
				'Partenaire.libstruc',
				'ActioncandidatPersonne.datesignature',
				'ActioncandidatPersonne.datebilan',
				'ActioncandidatPersonne.positionfiche',
				'ActioncandidatPersonne.sortiele',
				'Motifsortie.name',
			) + WebrsaAccess::links(
				array(
					'/ActionscandidatsPersonnes/view/#ActioncandidatPersonne.id#',
					'/ActionscandidatsPersonnes/edit/#ActioncandidatPersonne.id#',
					'/ActionscandidatsPersonnes/cancel/#ActioncandidatPersonne.id#',
					'/ActionscandidatsPersonnes/printFiche/#ActioncandidatPersonne.id#' => array(
						'class' => 'print'
					),
					'/ActionscandidatsPersonnes/maillink/#ActioncandidatPersonne.id#' => array(
						'class' => 'email'
					),
					'/ActionscandidatsPersonnes/filelink/#ActioncandidatPersonne.id#' => array(
						'msgid' => 'Fichiers liés (#ActioncandidatPersonne.nb_fichiers_lies#)'
					)
				)
			)
		),
		array(
			'options' => $options,
			'paginate' => false,
			'innerTable' => $this->Translator->normalize(
				array(
					'ActioncandidatPersonne.motifannulation' => array(
						'condition' => '"annule" == "#ActioncandidatPersonne.positionfiche#" || "" != "#ActioncandidatPersonne.motifannulation#"'
					)
				)
			)
		)
	);
?>