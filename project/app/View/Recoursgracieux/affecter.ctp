<?php

	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$foyer_id = $this->request->data['Recourgracieux']['foyer_id'];

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

	echo $this->Default3->subform(
		array(
			'Recourgracieux.etat' => array('type' => 'hidden', 'value' => 'ATTINSTRUCTION'),
			'Recourgracieux.id' => array('type' => 'hidden'),
			'Recourgracieux.foyer_id' => array( 'type' => 'hidden', 'value' => $foyer_id),
			),
		array('options' => $options)
	);

	echo $this->Default3->subform(
		array(
			'Recourgracieux.typerecoursgracieux_id' => array(
				'type' => 'select',
				'options' => $options['Typerecoursgracieux']['type_actif']
			),
			'Recourgracieux.poledossierpcg66_id' => array(
				'type' => 'select', 'empty' => true,
				'options' => $options['Poledossierpcg66']['name_actif']
			),
			'Recourgracieux.user_id' => array(
				'type' => 'select', 'empty' => true ,
				'options' => $options['Dossierpcg66']['prefix_user_id']
			),
			'Recourgracieux.dtaffectation' => array(
				'type' => 'date', 'dateFormat' => 'DMY',
				'maxYear' => date( 'Y' ) + 1, 'minYear'=> 2009
				),
		),
		array(
			'options' => $options
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "recourgracieux_{$this->request->params['action']}_form" ) );
?>

<script type="text/javascript">
document.observe( "dom:loaded", function() {
    dependantSelect( 'RecourgracieuxUserId', 'RecourgracieuxPoledossierpcg66Id' );
} );
</script>

