<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	echo $this->element('default_index');

	//Visualisation d'un Recours gracieux
	if( empty( $recoursgracieux ) ) {
		echo '<p class="notice">'.__m('Recoursgracieux::index::emptyLabel').'</p>';
	}else{
		echo $this->Default3->view(
			$recoursgracieux[0],
			$this->Translator->normalize(
				array(
					'Recourgracieux.etat',
					'Recourgracieux.dtarrivee',
					'Recourgracieux.dtbutoire',
					'Recourgracieux.dtreception',
					'Recourgracieux.dtaffectation',
					'Recourgracieux.originerecoursgracieux_id'=> array(
					'type' => 'select',
					'options' => $options['Originerecoursgracieux']['origine']
				),
					'Recourgracieux.typerecoursgracieux_id'=> array(
					'type' => 'select',
					'options' => $options['Typerecoursgracieux']['type']
				),
					'Recourgracieux.user_id',
				)
			),
			array(
				'paginate' => false,
				'options' => $options,
				'empty_label' => __m('Recoursgracieux::index::emptyLabel'),
				'th' => true
			)
		);
	}

	echo $this->Xhtml->link(
		'Retour',
		array('action' => 'index', $recoursgracieux[0]['Recourgracieux']['foyer_id'])
	);

?>