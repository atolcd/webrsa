<?php
	echo $this->Default3->titleForLayout();

    echo $this->Default3->view(
        $decisiondossierpcg66,
		$this->Translator->normalize(
			array(
				'Decisionpdo.libelle',
				'Decisiondossierpcg66.commentairetechnicien',
				'Decisiondossierpcg66.datepropositiontechnicien',
				'Decisiondossierpcg66.avistechnique',
				'Decisiondossierpcg66.commentaireavistechnique',
				'Useravistechnique.nom_complet',
				'Decisiondossierpcg66.dateavistechnique',
				'Decisiondossierpcg66.validationproposition',
				'Decisiondossierpcg66.commentairevalidation',
				'Userproposition.nom_complet',
				'Decisiondossierpcg66.datevalidation',
				'Dossierpcg66.etatdossierpcg',
				'Notificationdecisiondossierpcg66.0.name',
				'Decisiondossierpcg66.datetransmissionop',
				'Decisiondossierpcg66.motifannulation'
			)
		),
        array(
            'class' => 'aere',
            'options' => $options,
			'th' => true
        )
    );

	if ($this->Permissions->checkDossier('decisionsdossierspcgs66', 'avistechnique', $dossierMenu) || $this->Permissions->checkDossier('decisionsdossierspcgs66', 'validation', $dossierMenu)) {
	   echo $this->Default3->view(
			$decisiondossierpcg66,
			$this->Translator->normalize(
				array(
					'Decisiondossierpcg66.commentaire'
				)
			),
			array(
				'class' => 'aere',
				'options' => $options,
				'th' => true
			)
		);
	}

	echo "<h2>Pièces liées à la décision du dossier</h2>";
	echo $this->Fileuploader->results(Set::classicExtract($decisiondossierpcg66, 'Fichiermodule'));

	echo $this->Default3->actions(
		array(
			"/Dossierspcgs66/edit/{$decisiondossierpcg66['Dossierpcg66']['id']}" => array(
				'class' => 'back',
				'text' => 'Retour',
				'title' => false
			)
		)
	);
?>