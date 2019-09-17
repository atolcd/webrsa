<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Originerecoursgracieux.id',
				'Originerecoursgracieux.nom',
				'Originerecoursgracieux.actif' => array( 'type' => 'checkbox', 'value' => 1 ),
			)
		)
	);
?>