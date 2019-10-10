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
					'Recourgracieux.etatDepuis',
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
					'Recourgracieux.poledossierpcg66_id'=> array(
						'type' => 'select',
						'options' => $options['Poledossierpcg66']['name']
					),
					'Recourgracieux.user_id'=> array(
						'type' => 'select',
						'options' => $options['Dossierpcg66']['user_id']
					),
				)
			),
			array(
				'paginate' => false,
				'options' => $options,
				'empty_label' => __m('Recoursgracieux::index::emptyLabel'),
				'th' => true
			)
		);
		if ( ! empty ($propositions) ) {
			echo '<br><br> <h1>' . __m('Recoursgracieux::view::propositions') .  '</h1>';
			if ( $typerecoursgracieux['Typerecoursgracieux']['usage'] == 'contestation' ) {
				echo $this->Default3->index(
					$propositions,
					$this->Translator->normalize(
						array(
							'Creancerecoursgracieux.natcre' => array(
								'type' => 'select',
								'options' => $options['Creance']['natcre']
							),
							'Creancerecoursgracieux.rgcre',
							'Creancerecoursgracieux.mtinicre',
							'Creancerecoursgracieux.dtimplcre',
							'Creancerecoursgracieux.perioderegucre',
							'Creancerecoursgracieux.etattitre' => array(
								'type' => 'select',
								'options' => $options['Titrecreancier']['etat']
							),
							'Creancerecoursgracieux.mntindus',
							'Creancerecoursgracieux.refuscontestation'=> array(
								 'options' => array( '1' => __m('YES'), '2' => __m('NO'))
							),
							'Creancerecoursgracieux.motifproposrecoursgracieux_id' => array(
								'options' => $listMotifs
							),
							'Creancerecoursgracieux.mention',
							'Creancerecoursgracieux.dossierpcg_id',
							'/Recoursgracieux/deleteproposition/#Creancerecoursgracieux.id#'
						)
					),
					array(
						'paginate' => false,
						'options' => $options,
						'empty_label' => __m('Recourgracieux::proposer::emptyCreancesrecoursgracieux'),
					)
				);
			}
			if ( $typerecoursgracieux['Typerecoursgracieux']['usage'] == 'remise' ){
				echo $this->Default3->index(
					$propositions,
					$this->Translator->normalize(
						array(
							'Creancerecoursgracieux.natcre' => array(
								'type' => 'select',
								'options' => $options['Creance']['natcre']
							),
							'Creancerecoursgracieux.rgcre',
							'Creancerecoursgracieux.mtinicre',
							'Creancerecoursgracieux.dtimplcre',
							'Creancerecoursgracieux.perioderegucre',
							'Creancerecoursgracieux.etattitre' => array(
								'type' => 'select',
								'options' => $options['Titrecreancier']['etat']
							),
							'Creancerecoursgracieux.mntindus',
							'Creancerecoursgracieux.prcentremise',
							'Creancerecoursgracieux.mntremise',
							'Creancerecoursgracieux.motifproposrecoursgracieux_id' => array(
								'options' => $listMotifs
							),
							'Creancerecoursgracieux.mention',
							'/Recoursgracieux/deleteproposition/#Creancerecoursgracieux.id#'
						)
					),
					array(
						'paginate' => false,
						'options' => $options,
						'empty_label' => __m('Recourgracieux::proposer::emptyCreancesrecoursgracieux'),
					)
				);
			}
		}
	}

	echo $this->Xhtml->link(
		'Retour',
		array('action' => 'index', $recoursgracieux[0]['Recourgracieux']['foyer_id'])
	);

?>