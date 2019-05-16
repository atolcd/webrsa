<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Dreesorganisme.id',
				'Dreesorganisme.lib_dreesorganisme',
				'Dreesorganisme.parentid' => array( 'type' => 'select', 'empty' => false ),
				'Dreesorganisme.actif' => array( 'type' => 'checkbox' ),
			),
			'options' => $options
		)
	);
?>