<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
	
	echo $this->Default3->titleForLayout();

	echo $this->element( 'ancien_dossier' );

	echo $this->Default3->actions(
		WebrsaAccess::actionAdd("/Suspensionscuis66/add/{$cui_id}", $ajoutPossible)
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
			'Suspensioncui66.datedebut',
			'Suspensioncui66.datefin',
			'Suspensioncui66.duree',
			'Suspensioncui66.motif' => array( 'type' => 'select' ),
		) + WebrsaAccess::links(
			array(
				'/Suspensionscuis66/view/#Suspensioncui66.id#',
				'/Suspensionscuis66/edit/#Suspensioncui66.id#',
				'/Suspensionscuis66/delete/#Suspensioncui66.id#',
				'/Suspensionscuis66/filelink/#Suspensioncui66.id#' => array(
					'msgid' => __m('/Suspensionscuis66/filelink').' (#Fichiermodule.nombre#)'
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