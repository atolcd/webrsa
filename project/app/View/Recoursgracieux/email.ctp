<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	$paramsElement = array(
		'addLink' => false,
	);
	echo $this->element('default_index',$paramsElement);

	//Visualisation d'un Recours gracieux
	if( empty( $recoursgracieux ) ) {
		echo '<p class="notice">'.__m('Recoursgracieux::index::emptyLabel').'</p>';
	}else{

		echo $this->element( 'Email/view' );

	}

	echo $this->Xhtml->link(
		'Retour',
		array('action' => 'index', $recoursgracieux['Recourgracieux']['foyer_id'])
	);
