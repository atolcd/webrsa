<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	echo $this->Default3->titleForLayout();

	echo $this->element( 'ancien_dossier' );

	echo $this->Default3->actions(
		WebrsaAccess::actionAdd("/Propositionscuis66/add/{$cui_id}", $ajoutPossible)
	);

	// A-t'on des messages à afficher à l'utilisateur ?
	if( !empty( $messages ) ) {
		foreach( $messages as $message => $class ) {
			echo $this->Html->tag( 'p', __d( $this->request->params['controller'], $message ), array( 'class' => "message {$class}" ) );
		}
	}

	echo $this->Default3->index(
		$results,
		array(
			'Propositioncui66.donneuravis',
			'Propositioncui66.dateproposition',
			'Propositioncui66.avis',
		) + WebrsaAccess::links(
			array(
				'/Propositionscuis66/view/#Propositioncui66.id#',
				'/Propositionscuis66/edit/#Propositioncui66.id#' => array('class' => 'edit'),
				'/Propositionscuis66/impression_aviselu/#Propositioncui66.id#' => array('class' => 'impression'),
				'/Propositionscuis66/impression/#Propositioncui66.id#' => array('class' => 'impression'),
				'/Propositionscuis66/delete/#Propositioncui66.id#' => array(
					'confirm' => true
				),
				'/Propositionscuis66/filelink/#Propositioncui66.id#' => array(
					'msgid' => __m('/Propositionscuis66/filelink').' (#Fichiermodule.nombre#)'
				),
			)
		),
		array(
			'options' => $options,
			'paginate' => false,
		)
	);

	echo '<br />' . $this->Default->button(
		'back',
		array(
			'controller' => 'cuis',
			'action'     => 'index',
			$personne_id
		),
		array(
			'id' => 'Back',
			'class' => 'aere'
		)
	);