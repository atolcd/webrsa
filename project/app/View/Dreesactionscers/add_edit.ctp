<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Dreesactionscer.id',
				'Dreesactionscer.lib_dreesactioncer' => array( 'type' => 'textarea', 'style' => 'width: 50%; height: 50px;'),
				'Dreesactionscer.actif' => array( 'type' => 'checkbox' ),
			),
			'options' => $options
		)
	);
?>