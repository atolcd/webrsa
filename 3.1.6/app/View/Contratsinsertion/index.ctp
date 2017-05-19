<?php
	// Donne le domain du plus haut niveau de précision (prefix, action puis controller)
	$domain = current(WebrsaTranslator::domains());
	$defaultParams = compact('options', 'domain');
	
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));
	echo $this->element('ancien_dossier');

	echo $this->Default3->actions(
		WebrsaAccess::actionAdd("/Contratsinsertion/add/{$personne_id}", $ajoutPossible)
	);

	// A-t'on des messages à afficher à l'utilisateur ?
	foreach ((array)$messages as $message => $class) {
		echo $this->Html->tag('p', __m($message), array('class' => "message {$class}"));
	}
	
	echo $this->Default3->index(
		$contratsinsertion,
		array(
			'Contratinsertion.dd_ci',
			'Contratinsertion.df_ci',
			'Contratinsertion.decision_ci',
		)
		+ WebrsaAccess::links(
			array(
				'/Contratsinsertion/view/#Contratinsertion.id#',
				'/Contratsinsertion/edit/#Contratinsertion.id#',
				'/Contratsinsertion/impression/#Contratinsertion.id#',
				'/Contratsinsertion/filelink/#Contratinsertion.id#' => array(
					'msgid' => __m('/Contratsinsertion/filelink').' (#Fichiermodule.count#)'
				),
			)
		),
		$defaultParams + array(
			'paginate' => false,
		)
	);