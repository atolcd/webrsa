<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Motiffichedeliaison.id',
				'Motiffichedeliaison.name',
				'Motiffichedeliaison.actif' => array( 'type' => 'checkbox' ),
			)
		)
	);
?>