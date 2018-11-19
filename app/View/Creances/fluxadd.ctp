<?php

	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	//echo $this->Default3->DefaultForm->create( null, array(  ));
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

	echo $this->Default3->subform(
		array(
			'Creance.id' 			=> array('type' => 'hidden'),
			'Creance.foyer_id' 		=> array('type' => 'hidden'),
			'Dossier.numdemrsa' 	=> array('value' =>$this->request->data['Rejettalendcreance']['numdemrsa'],'type' => 'text','required' => true),
			'Creance.orgcre' 		=> array('type' => 'select','value' =>'FLU'),
			'Creance.dtimplcre' 	=> array('value' =>$this->request->data['Rejettalendcreance']['dtimplcre'],'type' => 'date', 'dateFormat' => 'DMY'),
			'Creance.natcre' 		=> array('value' =>$this->request->data['Rejettalendcreance']['natcre'],'required' => true),
			'Creance.rgcre' 		=> array('value' =>$this->request->data['Rejettalendcreance']['rgcre'],'required' => true),
			'Creance.motiindu' 		=> array('value' =>$this->request->data['Rejettalendcreance']['motiindu'],'required' => true),
			'Creance.oriindu' 		=> array('value' =>$this->request->data['Rejettalendcreance']['oriindu'],'required' => true),
			'Creance.respindu' 		=> array('value' =>$this->request->data['Rejettalendcreance']['respindu'],'required' => true),
			'Creance.ddregucre' 	=> array('value' =>$this->request->data['Rejettalendcreance']['ddregucre'],'type' => 'date', 'dateFormat' => 'DMY'),
			'Creance.dfregucre' 	=> array('value' =>$this->request->data['Rejettalendcreance']['dfregucre'],'type' => 'date','dateFormat' => 'DMY'),
			'Creance.dtdercredcretrans' => array('value' =>$this->request->data['Rejettalendcreance']['dtdercredcretrans'],'type' => 'date', 'dateFormat' => 'DMY'),
			'Creance.mtsolreelcretrans'=> array('value' =>$this->request->data['Rejettalendcreance']['mtsolreelcretrans'],'type' => 'money','required' => true),
			'Creance.mtinicre'		=> array('value' =>$this->request->data['Rejettalendcreance']['mtinicre'],'type' => 'money','required' => true),
			'Creance.moismoucompta'	=> array('value' =>$this->request->data['Rejettalendcreance']['moismoucompta'],'type' => 'date', 'dateFormat' => 'DMY'),
			'Creance.mention'		=> array('value' =>$this->request->data['Rejettalendcreance']['mention'],'required' => true)
		),
		array(
			'options' => $options
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "rejetstalendscreances_{$this->request->params['action']}_form" ) );

	?>