<?php
	echo $this->Default3->titleForLayout($personneDem);

	echo $this->element( 'ancien_dossier' );

	$perm = $this->Permissions->permList(array( 'add', 'view', 'edit', 'cancel', 'delete' ), $dossierMenu);

	echo $this->Default3->actions(
		array(
			"/Dossierspcgs66/add/{$foyer_id}" => array(
				'disabled' => !$perm['add']
			),
		)
	);

	echo $this->Default3->index(
		$results,
		$this->Translator->normalize(
			array(
				'Typepdo.libelle',
				'Dossierpcg66.datereceptionpdo',
				'Pole.user' => array('class' => 'custom color #Poledossierpcg66.classname#'),
				'Traitementpcg66.datereception',
				'Dossierpcg66.etatdossierpcg_full',
				'Personnepcg66.situationpdo_list_libelle_ulli',
				'Decisionpdo.libelle',
				'Dossierpcg66.bilan_de',
				'/dossierspcgs66/view/#Dossierpcg66.id#' => array( 'disabled' => !$perm['view'] ),
				'/dossierspcgs66/edit/#Dossierpcg66.id#' => array(
					'disabled' => (!$perm['edit'] ? 'true' : 'false').' || "#Dossierpcg66.etatdossierpcg#" === "annule"'
				),
				'/dossierspcgs66/cancel/#Dossierpcg66.id#' => array(
					'disabled' => (!$perm['cancel'] ? 'true' : 'false').' || "#Dossierpcg66.etatdossierpcg#" === "annule"'
				),
				'/dossierspcgs66/delete/#Dossierpcg66.id#' => array( 'disabled' => !$perm['delete'], 'confirm' => true ),
			)
		),
		array(
			'options' => $options,
			'paginate' => false,
			'innerTable' => $this->Translator->normalize(
				array(
					'Dossierpcg66.motifannulation' => array(
						'condition' => "'#Dossierpcg66.etatdossierpcg#' === 'annule'"
					)
				)
			)
		)
	);
?>
