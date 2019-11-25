<?php

	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$foyer_id = $this->request->data['Recourgracieux']['foyer_id'];

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

	if ( Configure::read( 'Recoursgracieux.Creancerecoursgracieux.Activer' ) ) {
		echo '<fieldset  id="creance" class="col6" ><h2>'. __m('Recourgracieux::proposer::titleCreance').'</h2>';
		$action = "proposer".$this->request->data['Typerecoursgracieux']['usage']."creances";
		if( empty( $creances ) ) {
			echo '<p class="notice">'.__m('Recourgracieux::proposer::emptyCreances').'</p>';
		}else{
				echo $this->Default3->index(
					$creances,
					$this->Translator->normalize(
						array(
							'Creance.natcre',
							'Creance.rgcre',
							'Creance.dtimplcre',
							'Creance.mtinicre',
							'Creance.perioderegucre',
							'Titrecreancier.etat',
							'/Recoursgracieux/'.$action.'/#Creance.id#/'.$this->request->data['Recourgracieux']['id'].'/'.$this->request->data['Recourgracieux']['typerecoursgracieux_id']
								=> array(
								'class' => 'edit',
								'title' => __m('Recourgracieux::proposer::Ajoutercreance')
								),
						)
					),
					array(
						'paginate' => false,
						'options' => $options,
						'empty_label' => __m('Recourgracieux::proposer::emptyCreances'),
					)
				);
		}
		echo '</fieldset>';
		echo '<fieldset  id="creancesrecoursgracieux" class="col6" ><h2>'. __m('Recourgracieux::proposer::titleCreancerecoursgracieux').'</h2>';
		if( empty( $creancesrecoursgracieux) ) {
			echo '<p class="notice">'.__m('Recourgracieux::proposer::emptyCreancesrecoursgracieux').'</p>';
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
								'/Recoursgracieux/deleteproposition/#Creancerecoursgracieux.id#' => array(
									'class' => 'delete',
									'title' => __m('Recourgracieux::proposer::Supprimercreance')
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
								'Creancerecoursgracieux.mention',
								'/Recoursgracieux/deleteproposition/#Creancerecoursgracieux.id#' => array(
									'class' => 'delete',
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
		}
		echo '</fieldset>';
	}

	if ( Configure::read( 'Recoursgracieux.Indurecoursgracieux.Activer' ) ) {
		echo '<fieldset  id="indu" class="col6" ><h2>'. __m('Recourgracieux::proposer::titleIndu').'</h2>';
		$action = "proposer".$this->request->data['Typerecoursgracieux']['usage']."indus";
		if( empty( $indus ) ) {
			echo '<p class="notice">'.__m('Recourgracieux::proposer::emptyIndus').'</p>';
		}else{
				echo $this->Default3->index(
					$indus,
					$this->Translator->normalize(
						array(
							'Infofinanciere.natpfcre',
							'Infofinanciere.rgcre',
							'Infofinanciere.dttraimoucompta',
							'Infofinanciere.mtmoucompta',
							'/Recoursgracieux/'.$action.'/#Infofinanciere.id#/'.$this->request->data['Recourgracieux']['id'].'/'.$this->request->data['Recourgracieux']['typerecoursgracieux_id']
								=> array(
								'class' => 'edit',
								'title' => __m('Recourgracieux::proposer::Ajouterindu')
								),
						)
					),
					array(
						'paginate' => false,
						'options' => $options,
						'empty_label' => __m('Recourgracieux::proposer::emptyIndus'),
					)
				);
		}
		echo '</fieldset>';
		echo '<fieldset  id="indusrecoursgracieux" class="col6" ><h2>'. __m('Recourgracieux::proposer::titleIndurecoursgracieux').'</h2>';
		if( empty( $indusrecoursgracieux) ) {
			echo '<p class="notice">'.__m('Recourgracieux::proposer::emptyIndusrecoursgracieux').'</p>';
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
								'/Recoursgracieux/deleteproposition/#Indurecoursgracieux.id#' => array(
									'class' => 'delete',
									'title' => __m('Recourgracieux::proposer::Supprimerindu')
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
								'Indurecoursgracieux.mention',
								'/Recoursgracieux/deleteproposition/#Indurecoursgracieux.id#' => array(
									'class' => 'delete',
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
		echo '</fieldset>';
	}

	echo $this->Default3->subform(
		array(
			'Recourgracieux.encours' => array(
				'type' => 'checkbox'
			),
			'Recourgracieux.mention'  => array('type' => 'textarea'),
			'Recourgracieux.etat' => array('type' => 'hidden', 'value' => 'ATTVALIDATION'),
			'Recourgracieux.id' => array('type' => 'hidden', 'value' => $this->request->data['Recourgracieux']['id']),
			'Recourgracieux.foyer_id' => array( 'type' => 'hidden', 'value' => $foyer_id),
			),
		array('options' => $options)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "recourgracieux_{$this->request->params['action']}_form" ) );
