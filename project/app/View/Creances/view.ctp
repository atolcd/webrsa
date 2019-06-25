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
					'Creance.mention',
					'Creance.datemotifemission',
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

	if( isset($historique) && !empty($historique)) {
		echo '<br><br> <h1>' . __m('Creance::view::history') .  '</h1>';
		echo $this->Default3->index(
			$historique,
			$this->Translator->normalize(
				array(
					'Historiqueetat.created' => array('type' => 'date', 'dateFormat' => 'DMY'),
					'Historiqueetat.evenement',
					'Historiqueetat.nom',
					'Historiqueetat.prenom',
					'Historiqueetat.modele',
					'Historiqueetat.etat'
				)
				),
				array('paginate' => false)
		);
	}

	echo $this->Xhtml->link(
		'Retour',
		array('action' => 'index', $creances[0]['Creance']['foyer_id'])
	);

?>