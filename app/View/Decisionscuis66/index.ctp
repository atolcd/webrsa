<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
	
	echo $this->Default3->titleForLayout();

	echo $this->element( 'ancien_dossier' );
	
	if ( empty($results) ){
		echo $this->Default3->actions(
			WebrsaAccess::actionAdd("/Decisionscuis66/add/{$cui_id}", $ajoutPossible)
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
			'Decisioncui66.datedecision',
			'Decisioncui66.decision',
			'Decisioncui66.motif' => array( 'type' => 'select' ),
			'Decisioncui66.observation',
		) + WebrsaAccess::links(
			array(
				'/Decisionscuis66/edit/#Decisioncui66.id#',
				'/Decisionscuis66/impression/#Decisioncui66.id#',
				'/Decisionscuis66/impression_decisionelu/#Decisioncui66.id#' => array('class' => 'impression'),
				'/Decisionscuis66/impression_notifbenef/#Decisioncui66.id#' => array('class' => 'impression'),
				'/Decisionscuis66/impression_notifemployeur/#Decisioncui66.id#' => array('class' => 'impression'),
				'/Decisionscuis66/impression_attestationcompetence/#Decisioncui66.id#' => array('class' => 'impression'),
				'/Decisionscuis66/delete/#Decisioncui66.id#',
				'/Decisionscuis66/filelink/#Decisioncui66.id#' => array(
					'msgid' => __m('/Decisionscuis66/filelink').' (#Fichiermodule.nombre#)'
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