<?php
	$this->pageTitle = $this->action == 'edit' ? __m('Tutoriel.edit') : __m('Tutoriel.ajout');

	echo '<h1>' . $this->pageTitle . '</h1>';

	$formId = isset( $formId ) ? $formId : Inflector::camelize( "{$this->request->params['controller']}_{$this->request->params['action']}_form" );
	$actions['/Tutoriels/index'] = array( 'class' => 'back' );

	echo $this->Default3->actions( $actions );

	echo $this->Form->create(
		'Tutoriels',
		array(
			'type' => 'post',
			'id' => $formId,
			'novalidate' => 'novalidate'
		)
	);

	$fields = array(
		'Tutoriel.rg' => array('label' => __m('Tutoriel.rg') ),
		'Tutoriel.titre' => array('label' => __m('Tutoriel.titre') ),
		'Tutoriel.parentid' => array( 'label' => __m('Tutoriel.parentid'), 'type' => 'select', 'empty' => true ),
		'Tutoriel.actif' => array( 'label' => __m('Tutoriel.actif'), 'type' => 'checkbox')
	 ) ;

	echo $this->Default3->subform( $fields, array( 'options' => $options ) );

	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', required( __m('Tutoriel.ajoutPJ' ) ) )
		. $this->Html->tag(
			'fieldset',
			$this->Fileuploader->create(
				isset($fichier) ? $fichier : array(),
				array( 'action' => 'ajaxfileupload' )
			),
			array(
				'id' => 'filecontainer-piecejointe',
				'class'=> "noborder invisible"
			)
		)
	);

	if(!empty( $fichierPresent )) {
		echo '<h2>' . __m('Tutoriel.PJPresente') . '</h2>' . $this->Fileuploader->results($fichierPresent);
	}

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
