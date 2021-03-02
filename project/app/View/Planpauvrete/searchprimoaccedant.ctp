<?php
	$departement = Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );
	$actions = array();
	$modelName = 'Personne';

	$this->start( 'custom_search_filters' );
?>
<fieldset>
	<legend><?php echo __m('PlanPauvrete.parcours') ?></legend>
	<?php
		echo $this->Xform->input( 'Search.Activite.act', array( 'label' => __d( 'activite', 'Activite.categoriepro' ), 'type' => 'select', 'empty' => true, 'options' => $options['Activite']['act'] ) );
	?>
</fieldset>

<?php
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
