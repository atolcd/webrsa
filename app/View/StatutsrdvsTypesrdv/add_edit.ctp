<h1>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if( $this->action == 'add' ) {
		echo $this->pageTitle = 'Ajout d\'un lien entre statut et type de RDV';
	}
	else {
		echo $this->pageTitle = 'Modification d\'un lien entre statut et type de RDV';
	}
?>
</h1>

<?php
	echo $this->Xform->create( null, array( 'id' => 'StatutrdvTyperdvAddEditForm' ) );

	if (isset($this->request->data['StatutrdvTyperdv']['id']))
		echo $this->Form->input('StatutrdvTyperdv.id', array('type'=>'hidden'));

	echo $this->Default->subform(
		array(
			'StatutrdvTyperdv.typerdv_id' => array('required' => true, 'type' => 'select', 'options' => $typesrdv),
			'StatutrdvTyperdv.statutrdv_id' => array('required' => true, 'type' => 'select', 'options' => $statutsrdvs),
			'StatutrdvTyperdv.nbabsenceavantpassagecommission' => array('required' => true, 'type' => 'text'),
			'StatutrdvTyperdv.typecommission' => array( 'required' => true, 'type' => 'select' ),
			'StatutrdvTyperdv.motifpassageep' => array('type' => 'text'),
		),
		array(
			'options' => $options
		)
	);



	echo $this->Xform->end( __( 'Save' ) );

    echo $this->Default->button(
		'back',
        array(
        	'controller' => 'statutsrdvs_typesrdv',
        	'action'     => 'index'
        ),
        array(
        	'id' => 'Back'
        )
	);
?>