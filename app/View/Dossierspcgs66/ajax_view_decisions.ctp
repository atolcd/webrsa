<?php
	// Donne le domain du plus haut niveau de précision (prefix, action puis controller)
	$domain = current(WebrsaTranslator::domains());
	$defaultParams = compact('options', 'domain');
	
	App::uses('WebrsaAccess', 'Utility');
	
	echo $this->Default3->index(
		$decisionsdossierspcgs66,
		array(
			'Decisionpdo.libelle' => array('label' => 'Proposition'),
			
			'Decisiondossierpcg66.datepropositiontechnicien' => array('label' => 'Date proposition'),
			
			'Decisiondossierpcg66.user_id' => array('label' => 'Agent ayant émis la proposition', 'options' => $users),
			'Decisiondossierpcg66.useravistechnique_id' => array('label' => 'Agent ayant émis l\'avis technique', 'options' => $users),
			'Decisiondossierpcg66.userproposition_id' => array('label' => 'Agent ayant émis la validation', 'options' => $users),
		)
		+ WebrsaAccess::links(
			array(
				'/Decisionsdossierspcgs66/view/#Decisiondossierpcg66.id#',
			),
			array('regles_metier' => false)
		),
		$defaultParams + array(
			'paginate' => false,
		)
	);