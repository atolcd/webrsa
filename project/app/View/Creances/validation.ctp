<?php

	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if( $this->action == 'edit' ) {
		$foyer_id = $this->request->data['Creance']['foyer_id'];
	}

	//echo $this->Default3->DefaultForm->create( null, array(  ));
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

	echo $this->Default3->subform(
		array(
			'Creance.id' => array( 'type' => 'hidden' ),
			'Creance.orgcre' => array( 'type' => 'hidden'),
			'Creance.foyer_id' => array( 'type' => 'hidden'),
			'Creance.dtimplcre' => array('type' => 'hidden'),
			'Creance.natcre' => array('type' => 'hidden'),
			'Creance.rgcre' => array('type' => 'hidden'),
			'Creance.motiindu'=> array('type' => 'hidden'),
			'Creance.oriindu'=> array('type' => 'hidden'),
			'Creance.respindu'=> array('type' => 'hidden'),
			'Creance.ddregucre' => array('type' => 'hidden'),
			'Creance.dfregucre' => array( 'type' => 'hidden'),
			'Creance.dtdercredcretrans' => array('type' => 'hidden'),
			'Creance.mtsolreelcretrans'=> array('type' => 'hidden'),
			'Creance.mtinicre'=> array('type' => 'hidden'),
			'Creance.moismoucompta'=> array('type' => 'hidden'),
			'Creance.motifemissioncreance_id'=> array('type' => 'hidden'),
			'Creance.etat'=> array('type' => 'hidden')
		),
		array(
			'options' => $options
		)
	);

	echo "<h3>". __m('Creances::view::titleMotifEmission')."</h3>";
	echo $this->Default3->view(
		$this->request->data,
		$this->Translator->normalize(
			array(
				'Creance.mention',
				'Creance.motifemissioncreance_id' => array(
					'options' => $listMotifs
				),
				'Creance.datemotifemission',
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Creances::index::emptyLabel'),
			'th' => true
		)
	);
	echo $this->Default3->subform(
		array(
			'Creance.commentairevalidateur' => array('type' => 'textarea'),
			'Creance.validation' => array(
				'type' => 'checkbox'
			),
		),
		array(
			'options' => $options
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "creance_{$this->request->params['action']}_form" ) );

?>