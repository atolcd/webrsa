<?php

	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	echo $this->element('default_index');
	//Visualisation des Recours gracieux
if( empty( $recoursgracieux ) ) {
	echo '<p class="notice">'.__m('Recoursgracieux::index::emptyRecours').'</p>';
}else{
	echo $this->Default3->index(
		$recoursgracieux,
		$this->Translator->normalize(
			array(
				'Recourgracieux.dtarrivee',
				'Recourgracieux.dtbutoire',
				'Recourgracieux.dtreception',
				'Recourgracieux.originerecoursgracieux_id' => array(
					'options' => $options['Originerecoursgracieux']['origine']
				),
				'Recourgracieux.dtaffectation',
				'Recourgracieux.user_id' => array(
					'options' =>  $options['Dossierpcg66']['user_id']
				),
				'Recourgracieux.etatDepuis',
			)+ WebrsaAccess::links(
				array(
					'/Recoursgracieux/view/#Recourgracieux.id#'
						=> array('class' => 'view',),
					'/Recoursgracieux/filelink/#Recourgracieux.id#',
					'/Recoursgracieux/email/#Recourgracieux.id#',
					'/Recoursgracieux/edit/#Recourgracieux.id#',
					'/Recoursgracieux/affecter/#Recourgracieux.id#',
					'/Recoursgracieux/proposer/#Recourgracieux.id#'
						=> array('class' => 'edit',),
					'/Recoursgracieux/decider/#Recourgracieux.id#'
						=> array('class' => 'edit',),
					'/Recoursgracieux/envoyer/#Recourgracieux.id#'
						=> array('class' => 'affecter',),
					'/Recoursgracieux/traiter/#Recourgracieux.id#'
						=> array('class' => 'affecter',),
					'/Recoursgracieux/delete/#Recourgracieux.id#'
						=> array('class' => 'edit',),
				)
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Recoursgracieux::index::emptyLabel'),
		)
	);
}
?>