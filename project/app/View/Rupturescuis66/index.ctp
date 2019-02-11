<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
	
	echo $this->Default3->titleForLayout();

	echo $this->element( 'ancien_dossier' );

	if ( empty($results) ){
		echo $this->Default3->actions(
			WebrsaAccess::actionAdd("/Rupturescuis66/add/{$cui_id}", $ajoutPossible)
		);
	}

	// A-t'on des messages à afficher à l'utilisateur ?
	if( !empty( $messages ) ) {
		foreach( $messages as $message => $class ) {
			echo $this->Html->tag( 'p', __d( $this->request->params['controller'], $message ), array( 'class' => "message {$class}" ) );
		}
	}

	echo $this->Default3->index(
		$results,
		array(
			'Rupturecui66.observation',
			'Rupturecui66.daterupture',
			'Rupturecui66.dateenregistrement',
			'Rupturecui66.motif' => array( 'type' => 'select' ),
		) + WebrsaAccess::links(
			array(
				'/Rupturescuis66/edit/#Rupturecui66.id#',
				'/Rupturescuis66/delete/#Rupturecui66.id#',
				'/Rupturescuis66/filelink/#Rupturecui66.id#' => array(
					'msgid' => __m('/Rupturescuis66/filelink').' (#Fichiermodule.nombre#)'
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