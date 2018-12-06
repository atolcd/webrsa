<?php

	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if( empty( $creances ) ) {
			echo '<p class="notice">Aucun creance trouvée.</p>';
	}else{
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
					'Creance.mtsolreelcretrans',
					'Creance.mtinicre'
				)
			),
			array(
				'paginate' => false,
				'options' => $options,
				'empty_label' => __m('Creances::index::emptyLabel'),
			)
		);
	}

	echo '<br>';

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

	echo $this->Default3->subform(
		array(
			'Titrecreancier.id' => array( 'type' => 'hidden' ),
			'Titrecreancier.creance_id' => array( 'type' => 'hidden', 'value' => $creance_id),
			'Titrecreancier.dtemissiontitre' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Titrecreancier.dtvalidation' => array('type' => 'hidden', 'dateFormat' => 'DMY','empty'=> true),
			'Titrecreancier.etat'=> array('type' => 'hidden'),
			'Titrecreancier.type' => array( 'type' => 'select', 'options' => $options['Typetitrecreancier']['type_actif'] ),
			'Titrecreancier.numtitr'=> array('type' => 'hidden'),
			'Titrecreancier.mnttitr' => array('type' => 'number','required' => true),
			'Titrecreancier.qual',
			'Titrecreancier.nom',
			'Titrecreancier.nir' => array('type' => 'number'),
			'Titrecreancier.numtel' => array('type' => 'number'),
			'Titrecreancier.titulairecompte',
			'Titrecreancier.iban',
			'Titrecreancier.bic',
			'Titrecreancier.comban',
			'Titrecreancier.mention'=> array('type' => 'textarea'),
			'Titrecreancier.dtemm' => array('type' => 'hidden'),
			'Titrecreancier.typeadr' => array('type' => 'hidden'),
			'Titrecreancier.etatadr' => array('type' => 'hidden'),
			'Titrecreancier.complete' => array('type' => 'hidden'),
			'Titrecreancier.localite' => array('type' => 'hidden')
		),
		array(
			'options' => $options
		)
	);

	echo $this->Xhtml->link(
		'Modifier l\'adresse',
		array('controller' => 'adressesfoyers', 'action' => 'index', $foyer_id)
	);

	$adresses[]=$this->request->data;
	echo $this->Default3->index(
		$adresses,
		$this->Translator->normalize(
			array(
				'Titrecreancier.dtemm',
				'Titrecreancier.typeadr',
				'Titrecreancier.etatadr',
				'Titrecreancier.complete',
				'Titrecreancier.localite'
			)
		),
		array(
			'paginate' => false,
			'options' => $options
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "titrecreancier_{$this->request->params['action']}_form" ) );

	?>