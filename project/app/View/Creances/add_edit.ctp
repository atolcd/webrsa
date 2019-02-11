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
			'Creance.orgcre' => array( 'type' => 'hidden','value' =>'MAN'),
			'Creance.foyer_id' => array( 'type' => 'hidden','value' =>$foyer_id),
		),
		array(
			'options' => $options
		)
	);

	if ( $this->action == 'edit') {
		echo $this->Default3->subform(
			array(
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
				'Creance.mention'=> array('type' => 'hidden')
			),
			array(
				'options' => $options
			)
		);
	}else{
		echo $this->Default3->subform(
			array(
				'Creance.dtimplcre' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Creance.natcre' => array('required' => true),
				'Creance.rgcre' => array('type' => 'number','required' => true),
				'Creance.motiindu',
				'Creance.oriindu',
				'Creance.respindu',
				'Creance.ddregucre' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Creance.dfregucre' => array( 'type' => 'date','dateFormat' => 'DMY'),
				'Creance.dtdercredcretrans' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Creance.mtsolreelcretrans'=> array('type' => 'number','required' => true),
				'Creance.mtinicre'=> array('type' => 'number','required' => true),
				'Creance.moismoucompta'=> array('type' => 'date', 'dateFormat' => 'DMY'),
			),
			array(
				'options' => $options
			)
		);
	}
	echo $this->Default3->subform(
		array(
			'Creance.mention'=> array('type' => 'textarea')
		),
		array(
			'options' => $options
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "creance_{$this->request->params['action']}_form" ) );

	?>