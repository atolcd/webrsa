<?php
	$activateTitreCreancier = Configure::read( 'Creances.titrescreanciers' );
	if ( !empty($activateTitreCreancier)	&& $activateTitreCreancier == true ){
		$activateTitreCreancier = true;
	}else{
		$activateTitreCreancier = false;
	}

	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	echo $this->element('default_index');

	//Visualisation des Créances
	if( empty( $creances ) ) {
		echo '<p class="notice">'.__m('Creances::index::emptyCreance').'</p>';
	}else{
		echo $this->Default3->view(
			$creances[0],
			$this->Translator->normalize(
				array(
					'Creance.dtimplcre',
					'Creance.orgcre',
					'Creance.natcre',
					'Creance.etat',
					'Creance.rgcre',
					'Creance.moismoucompta',
					'Creance.motiindu',
					'Creance.oriindu',
					'Creance.respindu',
					'Creance.ddregucre',
					'Creance.dfregucre',
					'Creance.dtdercredcretrans',
					'Creance.mtsolreelcretrans',
					'Creance.mtinicre',
					'Creance.datemotifemission',
					'Creance.mention',
					'Creance.motifemissioncreance_id' => array(
						'options' => $listMotifs
					),
					'Creance.commentairevalidateur',
				)
			),
			array(
				'paginate' => false,
				'options' => $options,
				'empty_label' => __m('Creances::index::emptyLabel'),
				'th' => true
			)
		);
	}
	
	echo $this->Xhtml->link(
		'Retour',
		array('action' => 'index', $creances[0]['Creance']['foyer_id'])
	);

?>