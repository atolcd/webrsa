<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$foyer_id = $this->request->data['Recourgracieux']['foyer_id'];

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );
	echo $this->Default3->index(
		array($indus),
		$this->Translator->normalize(
			array(
				'Infofinanciere.natpfcre',
				'Infofinanciere.rgcre',
				'Infofinanciere.dttraimoucompta',
				'Infofinanciere.mtmoucompta',
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Recourgracieux::proposer::emptyIndus'),
		)
	);
	echo $this->Default3->subform(
		array(
			'Indurecoursgracieux.natpfcre' => array('type' => 'hidden', 'value' => $indus['Infofinanciere']['natpfcre']),
			'Indurecoursgracieux.rgcre' => array('type' => 'hidden', 'value' => $indus['Infofinanciere']['rgcre']),
			'Indurecoursgracieux.mtmoucompta' => array('type' => 'hidden', 'value' => $indus['Infofinanciere']['mtmoucompta']),
			'Indurecoursgracieux.dttraimoucompta' => array('type' => 'hidden', 'value' => $indus['Infofinanciere']['dttraimoucompta']),
		),
		array('options' => $options)
	);
	echo $this->Default3->subform(
		array(
			'Indurecoursgracieux.mntindus' => array( 'value' => $indus['Infofinanciere']['mtmoucompta']),
			'Indurecoursgracieux.refuscontestation' => array(
				'type' => 'radio',
				'options' => array( '1' => __m('YES'), '0' => __m('NO'))
			),
			'Indurecoursgracieux.motifproposrecoursgracieux_id' => array(
				'options' => $listMotifs
			),
			'Indurecoursgracieux.mention' => array('type' => 'textarea'),
			'Indurecoursgracieux.regularisation' => array(
				'type' => 'radio',
				'label' => __m('Regularisation'),
				'options' => array( '1' => __m('YES'), '0' => __m('NO'))
			),
		),
		array('options' => $options)
	);

	echo $this->Default3->subform(
		array(
			'Indurecoursgracieux.indus_id' => array('type' => 'hidden', 'value' => $this->request->params['pass'][0]),
			'Indurecoursgracieux.recours_id' => array('type' => 'hidden', 'value' => $this->request->params['pass'][1]),
			'Indurecoursgracieux.types_id' => array('type' => 'hidden', 'value' => $this->request->params['pass'][2]),
		),
		array('options' => $options)
	);
	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "recourgracieux_{$this->request->params['action']}_form" ) );
