<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if( $this->action == 'edit' ) {
		$foyer_id = $this->request->data['Creance']['foyer_id'];
	}

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

	echo $this->Default3->subform(
		array(
			'Titrecreancier.id' => array( 'type' => 'hidden' ),
			'Titrecreancier.creance_id' => array( 'type' => 'hidden'),
			'Titrecreancier.dtemissiontitre' => array('type' => 'hidden', ),
			'Titrecreancier.etat'=> array('type' => 'hidden'),
			'Titrecreancier.type' => array( 'type' => 'hidden', ),
			'Titrecreancier.numtitr'=> array('type' => 'hidden'),
			'Titrecreancier.mnttitr' => array('type' => 'hidden'),
			'Titrecreancier.qual' => array('type' => 'hidden'),
			'Titrecreancier.nom' => array('type' => 'hidden'),
			'Titrecreancier.nir' => array('type' => 'hidden'),
			'Titrecreancier.numtel' => array('type' => 'hidden'),
			'Titrecreancier.titulairecompte'=> array('type' => 'hidden'),
			'Titrecreancier.iban'=> array('type' => 'hidden'),
			'Titrecreancier.bic'=> array('type' => 'hidden'),
			'Titrecreancier.comban'=> array('type' => 'hidden'),
			'Titrecreancier.mention'=> array('type' => 'hidden'),
			'Titrecreancier.motifemissiontitrecreancier_id' => array('type' => 'hidden'),
			'Titrecreancier.datemotifemission' => array('type' => 'hidden'),
			'Titrecreancier.dtemm' => array('type' => 'hidden'),
			'Titrecreancier.typeadr' => array('type' => 'hidden'),
			'Titrecreancier.etatadr' => array('type' => 'hidden'),
			'Titrecreancier.complete' => array('type' => 'hidden'),
			'Titrecreancier.localite' => array('type' => 'hidden'),
		),
		array(
			'options' => $options
		)
	);

	echo "<h3>". __m('Titrecreancier::view::titleMotifEmission')."</h3>";
	echo $this->Default3->view(
		$this->request->data,
		$this->Translator->normalize(
			array(
				'Titrecreancier.mention',
				'Titrecreancier.motifemissiontitrecreancier_id' => array(
					'options' => $listMotifs
				),
				'Titrecreancier.datemotifemission',
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Titrecreancier::index::emptyLabel'),
			'th' => true
		)
	);

	echo $this->Default3->subform(
		array(
			'Titrecreancier.id' => array( 'type' => 'hidden' ),
			'Titrecreancier.etat'=> array('type' => 'hidden','value' => 'VALI'),
			'Titrecreancier.dtvalidation' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Titrecreancier.commentairevalidateur'=> array('type' => 'textarea'),
			'Titrecreancier.validation' => array(
				'type' => 'radio',
				 'label' => __m('Validation'),
				 'options' => array( '1' => __m('YES'), '2' => __m('NO'))
			),
		),
		array(
			'options' => $options
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "titrecreancier_{$this->request->params['action']}_form" ) );

	?>