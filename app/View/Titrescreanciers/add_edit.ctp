<?php

	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

	echo $this->Default3->subform(
		array(
			'Titrecreancier.id' => array( 'type' => 'hidden' ),
			'Titrecreancier.creance_id' => array( 'type' => 'hidden', 'value' => $creance_id),
			'Titrecreancier.dtemissiontitre' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Titrecreancier.dtvalidation' => array('type' => 'hidden', 'dateFormat' => 'DMY','empty'=> true),
			'Titrecreancier.etatranstitr'=> array('type' => 'hidden','value' => 'CRE'),//'type' => 'hidden',
			'Titrecreancier.numtitr',
			'Titrecreancier.mnttitr' => array('type' => 'number','required' => true),
			'Titrecreancier.typetitre',
			'Titrecreancier.mention'=> array('type' => 'textarea'),
			'Titrecreancier.qual',
			'Titrecreancier.nom',
			'Titrecreancier.nir' => array('type' => 'number'),
			'Titrecreancier.numtel' => array('type' => 'number'),
		),
		array(
			'options' => $options
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "titrecreancier_{$this->request->params['action']}_form" ) );

	?>