<?php
	$departement = (int)Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );

	$actions = array();
?>

<?php $this->start( 'custom_search_filters' );?>

<fieldset>
	<legend><?php echo __m( 'Search.Creances' ); ?></legend>
	<?php
		echo $this->Xform->input(
			'Search.Creance.orgcre',
			array(
				'label' => 'Origine de la créances',
				'type' => 'select',
				'empty' => true,
				'options' => $options['Creance']['orgcre']
			)
		);
		echo $this->Xform->input(
			'Search.Creance.motiindu',
			array(
				'label' => 'Motif de l\'indu',
				'type' => 'select',
				'empty' => true,
				'options' => $options['Creance']['motiindu']
			)
		);
	?>
</fieldset>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'actions' => $actions,
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);
?>