<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	echo $this->Default3->titleForLayout($personne);

	echo $this->Default3->actions(
		WebrsaAccess::actionAdd("/Questionnairesb7pdvs93/add/{$personne['Personne']['id']}", $ajoutPossible)
	);

	// A-t'on des messages à afficher à l'utilisateur ?
	echo $this->Default3->messages( $messages );

	echo $this->Default3->index(
		$questionnaireb7pdv93,
		array(
			'Questionnaireb7pdv93.dateemploi' => array ('format' => '%m/%Y'),
			'Typeemploi.name',
			'Dureeemploi.name',
			'Expproromev3.Appellationromev3.name',
			'Questionnaireb7pdv93.created',
			'Questionnaireb7pdv93.modified',
		)+ WebrsaAccess::links(
			array(
				'/Questionnairesb7pdvs93/edit/#Personne.id#/#Questionnaireb7pdv93.id#',
				'/Questionnairesb7pdvs93/delete/#Questionnaireb7pdv93.id#' => array(
					'confirm' => true
				)
			)
		),
        array(
            'options' => $options,
			'paginate' => false,
        )
	);
?>