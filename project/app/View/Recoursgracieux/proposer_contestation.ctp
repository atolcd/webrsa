<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$foyer_id = $this->request->data['Recourgracieux']['foyer_id'];

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );
	echo $this->Default3->index(
		array($creances),
		$this->Translator->normalize(
			array(
				'Creance.natcre',
				'Creance.rgcre',
				'Creance.dtimplcre',
				'Creance.mtinicre',
				'Creance.perioderegucre',
				'Titrecreancier.etat',
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Recourgracieux::proposer::emptyCreances'),
		)
	);
	echo $this->Default3->subform(
		array(
			'Creancerecoursgracieux.natcre' => array('type' => 'hidden', 'value' => $creances['Creance']['natcre']),
			'Creancerecoursgracieux.rgcre' => array('type' => 'hidden', 'value' => $creances['Creance']['rgcre']),
			'Creancerecoursgracieux.mtinicre' => array('type' => 'hidden', 'value' => $creances['Creance']['mtinicre']),
			'Creancerecoursgracieux.dtimplcre' => array('type' => 'hidden', 'value' => $creances['Creance']['dtimplcre']),
			'Creancerecoursgracieux.ddregucre' => array('type' => 'hidden', 'value' => $creances['Creance']['ddregucre']),
			'Creancerecoursgracieux.dfregucre' => array('type' => 'hidden', 'value' => $creances['Creance']['dfregucre']),
			'Creancerecoursgracieux.etattitre' => array('type' => 'hidden', 'value' => $creances['Titrecreancier']['etat']),
		),
		array('options' => $options)
	);
	echo $this->Default3->subform(
		array(
			'Creancerecoursgracieux.mntindus' => array( 'value' => $creances['Creance']['mtinicre']),
			'Creancerecoursgracieux.refuscontestation' => array(
				'type' => 'radio',
				'options' => array( '1' => __m('YES'), '0' => __m('NO'))
			),
			'Creancerecoursgracieux.motifproposrecoursgracieux_id' => array(
				'options' => $listMotifs
			),
			'Creancerecoursgracieux.encours' => array(
				'type' => 'checkbox'
			),
			'Creancerecoursgracieux.mention' => array('type' => 'textarea'),
			'Creancerecoursgracieux.regularisation' => array(
				'type' => 'radio',
				'label' => __m('Regularisation'),
				'options' => array( '1' => __m('YES'), '0' => __m('NO'))
			),
			//'Creancerecoursgracieux.dossierpcg_id' ,
		),
		array('options' => $options)
	);

	echo $this->Default3->subform(
		array(
			'Creancerecoursgracieux.creances_id' => array('type' => 'hidden', 'value' => $this->request->params['pass'][0]),
			'Creancerecoursgracieux.recours_id' => array('type' => 'hidden', 'value' => $this->request->params['pass'][1]),
			'Creancerecoursgracieux.types_id' => array('type' => 'hidden', 'value' => $this->request->params['pass'][2]),
		),
		array('options' => $options)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "recourgracieux_{$this->request->params['action']}_form" ) );
?>