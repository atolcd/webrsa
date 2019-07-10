<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	//echo $this->Default3->DefaultForm->create( null, array(  ));
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );
	echo $this->Default3->subform(
		array(
			'Creance.foyer_id' => array('type' => 'hidden'),
			'Dossier.numdemrsa' => array('value' => '','type' => 'text','required' => true),
			'Creance.orgcre' => array('type' => 'hidden', 'value' =>'COP'),
			'Creance.dtimplcre' => array('type' => 'hidden', 'dateFormat' => 'DMY'),
			'Creance.natcre' => array('type' => 'hidden'),
			'Creance.rgcre' => array('type' => 'hidden'),
			'Creance.motiindu' => array('type' => 'hidden'),
			'Creance.oriindu' => array('type' => 'hidden'),
			'Creance.respindu' => array('type' => 'hidden'),
			'Creance.ddregucre' => array('type' => 'hidden', 'dateFormat' => 'DMY'),
			'Creance.dfregucre' => array('type' => 'hidden', 'dateFormat' => 'DMY'),
			'Creance.dtdercredcretrans' => array('type' => 'hidden', 'dateFormat' => 'DMY'),
			'Creance.mtsolreelcretrans' => array('type' => 'money','required' => true),
			'Creance.mtinicre' => array('type' => 'hidden','required' => true),
			'Creance.moismoucompta' => array('type' => 'hidden', 'dateFormat' => 'DMY'),
			'Creance.etat'  => array('type' => 'hidden','required' => true),
			'Creance.mention' => array(
				'value' => "Créance copiée depuis le dossier : ".$this->request->data['Foyer']['Dossier']['numdemrsa']." ".$this->request->data['Creance']['mention'],
				'required' => true
			)
		),
		array(
			'options' => $options
		)
	);

$creances[0] = $this->request->data;
echo $this->Default3->index(
		$creances,
		$this->Translator->normalize(
			array(
				'Creance.dtimplcre',
				'Creance.orgcre',
				'Creance.natcre',
				'Creance.rgcre',
				'Creance.moismoucompta',
				'Creance.motiindu',
				'Creance.oriindu',
				'Creance.respindu',
				'Creance.ddregucre',
				'Creance.dfregucre',
				'Creance.dtdercredcretrans',
				'Creance.mtinicre',
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Creances::index::emptyLabel'),
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "rejetstalendscreances_{$this->request->params['action']}_form" ) );

	?>