<?php
	$recoursgracieux[0]['Recourgracieux'] = $this->request->data['Recourgracieux'];

	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$foyer_id = $this->request->data['Recourgracieux']['foyer_id'];

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

	echo $this->Default3->subform(
		array(
			'Recourgracieux.id' => array('type' => 'hidden'),
			'Recourgracieux.etat' => array('type' => 'hidden'),
			'Recourgracieux.foyer_id' => array( 'type' => 'hidden'),
			'Recourgracieux.traiter' => array(
				'type' => 'radio',
				'label' => __m('Traitement'),
				'options' => array( '1' => __m('YES'), '2' => __m('NO'))
			)
		),
		array('options' => $options)
	);

	echo "
	<fieldset>
	<legend>".$this->Default2->label( 'Recourgracieux.haspiecejointe' )."</legend>
	<div style='display: none;'>";
	echo $this->Form->input( 'Recourgracieux.haspiecejointe', array( 'type' => 'radio', 'options' => $options['Recourgracieux']['haspiecejointe'], 'legend' => false, 'fieldset' => false, 'value' => 1 ) );
	echo '</div>
		<fieldset id="filecontainer-piece" class="noborder invisible">';
			echo $this->Fileuploader->create(
				isset($fichiers) ? $fichiers : array(),
				array( 'action' => 'ajaxfileupload' )
			);
			if (!isset ($fichiersEnBase)) {
				$fichiersEnBase = array ();
			}
			echo $this->Fileuploader->results(
				$fichiersEnBase
			);
	echo "</fieldset>".
	$this->Fileuploader->validation( Inflector::camelize( "recourgracieux_{$this->request->params['action']}_form" ), 'Recourgracieux', __m('Pièce jointe') ).
	"</fieldset>";

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "recourgracieux_{$this->request->params['action']}_form" ) );
