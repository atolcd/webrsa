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