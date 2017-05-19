<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
	
	echo $this->Default3->titleForLayout($infos);
	
	echo $this->element( 'ancien_dossier' );

	echo $this->Default3->actions(
		WebrsaAccess::actionAdd("/Tags/add/{$modele}/{$id}", $ajoutPossible)
	);
	
	echo $this->Default3->index(
		$results, 
		$this->Translator->normalize(
			array(
				'Categorietag.name',
				'Valeurtag.name',
				'Tag.etat',
				'Tag.commentaire',
				'Tag.limite',
				'Tag.created',
			) + WebrsaAccess::links(
				array(
					'/tags/edit/#Tag.id#',
					'/tags/cancel/#Tag.id#',
					'/tags/delete/#Tag.id#' => array('confirm' => true),
				)
			)
		), 
		array(
			'options' => $options,
			'paginate' => false
		)
	);	
?>
