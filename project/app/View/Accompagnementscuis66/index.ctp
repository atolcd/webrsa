<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
	
	echo $this->Default3->titleForLayout();

	echo $this->element( 'ancien_dossier' );

	echo $this->Default3->actions(
		WebrsaAccess::actionAdd("/Accompagnementscuis66/add/{$cui_id}", $ajoutPossible)
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
			'Accompagnementcui66.genre',
			'Accompagnementcui66.organismesuivi',
			'Accompagnementcui66.datededebut',
			'Accompagnementcui66.datedefin',
		) + WebrsaAccess::links(
			array(
				'/Accompagnementscuis66/view/#Accompagnementcui66.id#',
				'/Accompagnementscuis66/edit/#Accompagnementcui66.id#',
				'/Accompagnementscuis66/impression/#Accompagnementcui66.id#',
				'/Accompagnementscuis66/delete/#Accompagnementcui66.id#',
				'/Accompagnementscuis66/filelink/#Accompagnementcui66.id#' => array(
					'msgid' => __m('/Accompagnementscuis66/filelink').' (#Fichiermodule.nombre#)'
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