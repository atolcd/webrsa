<?php

	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

	echo $this->Default3->subform(
		array(
			'Titrecreancier.id' => array( 'type' => 'hidden' ),
			'Titrecreancier.creance_id' => array( 'type' => 'hidden' ),
			'Titrecreancier.etat'=> array('type' => 'hidden', 'value' => 'VALIDAVIS')
		),
		array(
			'options' => $options
		)
	);

	if(! empty($this->request->data['Titrecreancier']['commentairevalidateur'])
		|| ! empty($this->request->data['Titrecreancier']['dtvalidation'])
	){
		echo "<h3>". __m('Titrecreancier::view::titleInfoValidateur')."</h3>";
		echo $this->Default3->view(
			$this->request->data,
			$this->Translator->normalize(
				array(
					'Titrecreancier.dtvalidation',
					'Titrecreancier.commentairevalidateur',
				)
			),
			array(
				'paginate' => false,
				'options' => $options,
				'empty_label' => __m('Titrecreancier::index::emptyLabel'),
				'th' => true
			)
		);
	}

	if( isset($this->request->data['Titrecreancier']['motifemissiontitrecreancier_id']) && !empty($this->request->data['Titrecreancier']['motifemissiontitrecreancier_id'])) {
		$motifvalue = $this->request->data['Titrecreancier']['motifemissiontitrecreancier_id'];
	}else{
		$motifvalue = null;
	}

	echo $this->Default3->subform(
		array(
			'Titrecreancier.Motifemissiontitrecreancier' => array(
				 'type' => 'select',
				 'label' => 'Motif d\'emission des titres creanciers',
				 'options' => $listMotifs,
				 'value' => $motifvalue
			),
			'Titrecreancier.mention' => array('type' => 'textarea'),
			'Titrecreancier.datemotifemission' => array('type' => 'date', 'dateFormat' => 'DMY' ),
		),
		array(
			'options' => $options
		)
	);

	/* Section Instrcution En cours */
	echo $this->Default3->subform(
		array(
			'Titrecreancier.instructionencours' => array(
				'type' => 'checkbox'
			),
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "titrecreancier_{$this->request->params['action']}_form" ) );

	?>