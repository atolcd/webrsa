<?php
	$recoursgracieux[0]['Recourgracieux'] = $this->request->data['Recourgracieux'];

	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$foyer_id = $this->request->data['Recourgracieux']['foyer_id'];

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

	echo '<fieldset  id="creance" class="col6" ><h2>'. __m('Recourgracieux::decider::titleRecours').'</h2>';
	//Visualisation d'un Recours gracieux
	if( empty( $recoursgracieux) ) {
		echo '<p class="notice">'.__m('Recoursgracieux::index::emptyLabel').'</p>';
	}else{
		echo $this->Default3->index(
			$recoursgracieux,
			$this->Translator->normalize(
				array(
					'Recourgracieux.etatDepuis',
					'Recourgracieux.dtarrivee',
					'Recourgracieux.dtbutoir',
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
		if ( Configure::read( 'Recoursgracieux.Creancerecoursgracieux.enabled' ) ) {
			echo '<h2>'. __m('Recourgracieux::proposer::titleCreancerecoursgracieux').'</h2>';
			if( empty( $creancesrecoursgracieux) ) {
				echo '<p class="notice">'.__m('Recourgracieux::decider::emptyCreancesrecoursgracieux').'</p>';
			}else{
				if ( $this->request->data['Typerecoursgracieux']['usage'] == 'contestation' ) {
					echo $this->Default3->index(
						$creancesrecoursgracieux,
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
									'options' => array( '1' => __m('YES'), '0' => __m('NO'))
								),
								'Creancerecoursgracieux.motifproposrecoursgracieux_id' => array(
									'options' => $listMotifs
								),
								'Creancerecoursgracieux.mention',
								'Creancerecoursgracieux.regularisation'=> array(
									'options' => array( '1' => __m('YES'), '0' => __m('NO'))
								),
							)
						),
						array(
							'paginate' => false,
							'options' => $options,
							'empty_label' => __m('Recourgracieux::decider::emptyCreancesrecoursgracieux'),
						)
					);
				}
				if ( $this->request->data['Typerecoursgracieux']['usage'] == 'remise' ){
					echo $this->Default3->index(
						$creancesrecoursgracieux,
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
								'Creancerecoursgracieux.mention'
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
		if ( Configure::read( 'Recoursgracieux.Indurecoursgracieux.enabled' ) ) {
			echo '<h2>'. __m('Recourgracieux::proposer::titleIndurecoursgracieux').'</h2>';
			if( empty( $indusrecoursgracieux) ) {
				echo '<p class="notice">'.__m('Recourgracieux::decider::emptyIndusrecoursgracieux').'</p>';
			}else{
				if ( $this->request->data['Typerecoursgracieux']['usage'] == 'contestation' ) {
					echo $this->Default3->index(
						$indusrecoursgracieux,
						$this->Translator->normalize(
							array(
								'Indurecoursgracieux.natpfcre' => array(
									'type' => 'select',
									'options' => $options['Infofinanciere']['natpfcre']
								),
								'Indurecoursgracieux.rgcre',
								'Indurecoursgracieux.mtmoucompta',
								'Indurecoursgracieux.dttraimoucompta',
								'Indurecoursgracieux.mntindus',
								'Indurecoursgracieux.refuscontestation'=> array(
									'options' => array( '1' => __m('YES'), '0' => __m('NO'))
								),
								'Indurecoursgracieux.motifproposrecoursgracieux_id' => array(
									'options' => $listMotifs
								),
								'Indurecoursgracieux.mention',
								'Indurecoursgracieux.regularisation'=> array(
									'options' => array( '1' => __m('YES'), '0' => __m('NO'))
								),
							)
						),
						array(
							'paginate' => false,
							'options' => $options,
							'empty_label' => __m('Recourgracieux::decider::emptyIndusrecoursgracieux'),
						)
					);
				}
				if ( $this->request->data['Typerecoursgracieux']['usage'] == 'remise' ){
					echo $this->Default3->index(
						$indusrecoursgracieux,
						$this->Translator->normalize(
							array(
								'Indurecoursgracieux.natpfcre' => array(
									'type' => 'select',
									'options' => $options['Infofinanciere']['natpfcre']
								),
								'Indurecoursgracieux.rgcre',
								'Indurecoursgracieux.mtmoucompta',
								'Indurecoursgracieux.dttraimoucompta',
								'Indurecoursgracieux.mntindus',
								'Indurecoursgracieux.prcentremise',
								'Indurecoursgracieux.mntremise',
								'Indurecoursgracieux.motifproposrecoursgracieux_id' => array(
									'options' => $listMotifs
								),
								'Indurecoursgracieux.mention'
							)
						),
						array(
							'paginate' => false,
							'options' => $options,
							'empty_label' => __m('Recourgracieux::proposer::emptyIndusrecoursgracieux'),
						)
					);
				}
			}
		}
	}
	echo '</fieldset>';
	if ($this->request->data['Recourgracieux']['etat'] == 'VALIDTRAITEMENT') {
		$this->request->data['Recourgracieux']['validation'] = 1;
	}
	echo $this->Default3->subform(
		array(
			'Recourgracieux.mention'  => array('type' => 'textarea'),
			'Recourgracieux.dtdecision' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Recourgracieux.validation' => array(
				'type' => 'radio',
				'label' => __m('Validation'),
				'options' => array( '1' => __m('YES'), '2' => __m('NO'))
			),
			'Recourgracieux.regularisation' => array('type' => 'hidden'),
			'Recourgracieux.etat' => array('type' => 'hidden'),
			'Recourgracieux.id' => array('type' => 'hidden', 'value' => $this->request->data['Recourgracieux']['id']),
			'Recourgracieux.foyer_id' => array( 'type' => 'hidden', 'value' => $foyer_id),
			),
		array('options' => $options)
	);

	// ******************* Partie Email ****************
	echo $this->element( 'Email/edit' );

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "recourgracieux_{$this->request->params['action']}_form" ) );
