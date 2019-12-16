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
		echo $this->Default3->view(
			$recoursgracieux[0],
			$this->Translator->normalize(
				array(
					'Recourgracieux.etatDepuis',
					'Recourgracieux.dtarrivee',
					'Recourgracieux.dtbutoir',
					'Recourgracieux.originerecoursgracieux_id'=> array(
						'type' => 'select',
						'options' => $options['Originerecoursgracieux']['origine']
					),
					'Recourgracieux.dtreception',
					'Recourgracieux.dtaffectation',
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
					'Recourgracieux.mention',
					'Recourgracieux.dtdecision',
				)
			),
			array(
				'paginate' => false,
				'options' => $options,
				'empty_label' => __m('Recoursgracieux::index::emptyLabel'),
				'th' => true
			)
		);
		if ( ! empty ($recoursgracieux) ) {
			echo '<br><br> <h1>' . __m('Recoursgracieux::view::propositions') .  '</h1>';
			if ( $typerecoursgracieux['Typerecoursgracieux']['usage'] == 'contestation' ) {
				if ( Configure::read( 'Recoursgracieux.Creancerecoursgracieux.enabled' ) ) {
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
								'/Dossierspcgs66/view/#Creancerecoursgracieux.dossierpcg_id#' => array(
									'disabled' => 'empty ("#Creancerecoursgracieux.dossierpcg_id#")'
								),
							)
						),
						array(
							'paginate' => false,
							'options' => $options,
							'empty_label' => __m('Recourgracieux::proposer::emptyCreancesrecoursgracieux'),
						)
					);
				}
				if ( Configure::read( 'Recoursgracieux.Indurecoursgracieux.enabled' ) ) {
					echo $this->Default3->index(
						$indusrecoursgracieux,
						$this->Translator->normalize(
							array(
								'Indurecoursgracieux.natpfcre' => array(
									'type' => 'select',
									'options' => $options['Creance']['natcre']
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
								'/Dossierspcgs66/view/#Indurecoursgracieux.dossierpcg_id#' => array(
									'disabled' => 'empty ("#Indurecoursgracieux.dossierpcg_id#")'
								),
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
			if ( $typerecoursgracieux['Typerecoursgracieux']['usage'] == 'remise' ){
				if ( Configure::read( 'Recoursgracieux.Creancerecoursgracieux.enabled' ) ) {
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
								'Creancerecoursgracieux.mention',
							)
						),
						array(
							'paginate' => false,
							'options' => $options,
							'empty_label' => __m('Recourgracieux::proposer::emptyCreancesrecoursgracieux'),
						)
					);
				}
				if ( Configure::read( 'Recoursgracieux.Indurecoursgracieux.enabled' ) ) {
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
								'Indurecoursgracieux.mention',
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

		echo "<h2>".__m('Recourgracieux::fileuploader::titleFileView')."</h2>" ;
		echo $this->Fileuploader->results($piecesjointes);

		echo '<br>';
		echo $this->element( 'Email/view' );

		if( isset($historiques) && !empty($historiques)) {
			echo '<br><br> <h1>' . __m('Recoursgracieux::view::history') .  '</h1>';
			echo $this->Default3->index(
				$historiques,
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
	}

	echo $this->Xhtml->link(
		'Retour',
		array('action' => 'index', $recoursgracieux[0]['Recourgracieux']['foyer_id'])
	);
