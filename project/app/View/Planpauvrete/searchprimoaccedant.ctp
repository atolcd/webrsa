<?php
	$departement = Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );
	$actions = array();
	$modelName = 'Personne';
	$this->start( 'custom_search_filters' );
	$activite = '';
	if(isset($options['Activite']) && !empty($options['Activite'])) {
		$activite = $this->Xform->input( 'Search.Activite.act', array( 'label' => __d( 'activite', 'Activite.categoriepro' ), 'type' => 'select', 'empty' => true, 'options' => $options['Activite']['act'] ) );
	}

	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __m('PlanPauvrete.parcours') )
		. $activite
		. $this->Default3->subform(
			array(
				'Search.Personne.is_inscritpe' => array( 'empty' => true )
			),
			array( 'options' => array( 'Search' => $options ) )
		)
	);
	$this->end();
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'actions' => $actions,
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' ),
			'modelName' => $modelName
		)
	);
