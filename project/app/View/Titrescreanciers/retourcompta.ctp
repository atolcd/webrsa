<?php

	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if( $this->action == 'edit' ) {
		$foyer_id = $this->request->data['Creance']['foyer_id'];
	}

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

	echo '<fieldset  id="titrecreancier" class="col6" ><h2>'. __m('Titrecreancier::view::titleTitrecreancier').'</h2>';
	echo $this->Default3->view(
		$this->request->data,
		$this->Translator->normalize(
			array(
				'Titrecreancier.dtemissiontitre',
				'Titrecreancier.numtitr',
				'Titrecreancier.mnttitr',
				'Titrecreancier.type'=> array( 'type' => 'select' ),
				'Titrecreancier.dtvalidation',
				'Titrecreancier.etat',
				'Titrecreancier.mention',
				'Titrecreancier.qual',
				'Titrecreancier.nom',
				'Titrecreancier.nir',
				'Titrecreancier.numtel',
				'Titrecreancier.titulairecompte',
				'Titrecreancier.iban',
				'Titrecreancier.bic',
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Titrecreancier::view::emptyLabel'),
		)
	);
	echo '</fieldset>';

	echo '<fieldset  id="titrecreancier_conjoint" class="col6" ><h3>'. __m('Titrecreancier::view::creanceCouple').'</h3>';
	if ($this->request->data['Titrecreancier']['cjtactif'] == 1 ){
		$this->Default3->view(
			$this->request->data,
			$this->Translator->normalize(
				array(
					'Titrecreancier.qualcjt',
					'Titrecreancier.nomcjt',
					'Titrecreancier.nircjt',
				)
			),
			array(
				'paginate' => false,
				'options' => $options,
				'empty_label' => __m('Titrecreancier::view::emptyLabel'),
			)
		);
	}
	echo '</fieldset>';

	echo $this->Default3->subform(
		array(
			'Titrecreancier.id' => array( 'type' => 'hidden' ),
			'Titrecreancier.creance_id' => array( 'type' => 'hidden'),
			'Titrecreancier.etat'=> array('type' => 'hidden'),
			'Titrecreancier.type' => array( 'type' => 'hidden', ),
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
		),
		array(
			'options' => $options
		)
	);

	echo $this->Default3->subform(
		array(
			'Titrecreancier.id' => array( 'type' => 'hidden' ),
			'Titrecreancier.etat'=> array('type' => 'hidden','value' => 'TITREEMIS'),
			'Titrecreancier.dtbordereau' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Titrecreancier.numtitr',
			'Titrecreancier.numbordereau'=> array( 'type' => 'numeric' ),
			'Titrecreancier.numtier',
		),
		array(
			'options' => $options
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "titrecreancier_{$this->request->params['action']}_form" ) );

	?>