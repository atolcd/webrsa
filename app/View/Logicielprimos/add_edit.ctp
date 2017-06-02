<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Logicielprimo.id',
				'Logicielprimo.name',
				'Logicielprimo.actif' => array( 'type' => 'checkbox' ),
			)
		)
	);
?>