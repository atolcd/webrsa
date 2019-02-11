<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
	
	echo $this->Default3->titleForLayout();

	echo $this->element( 'ancien_dossier' );

	echo $this->Default3->actions(
		WebrsaAccess::actionAdd("/Cuis66/email_add/{$personne_id}/{$cui_id}", $ajoutPossible, array('class' => 'add'))
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
			'Emailcui.titre',
			'Emailcui.created',
			'Emailcui.dateenvoi',
		) + WebrsaAccess::links(
			array(
				'/Cuis66/email_send/#Emailcui.personne_id#/#Emailcui.cui_id#/#Emailcui.id#',
				'/Cuis66/email_view/#Emailcui.personne_id#/#Emailcui.id#' => array('class' => 'view'),
				'/Cuis66/email_edit/#Emailcui.personne_id#/#Emailcui.id#' => array('class' => 'edit'),
				'/Cuis66/email_delete/#Emailcui.id#' => array('class' => 'delete'),
			)
		),
		array(
			'options' => $options,
			'paginate' => false
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
	