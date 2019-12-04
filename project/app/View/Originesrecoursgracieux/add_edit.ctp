<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Originerecoursgracieux.id',
				'Originerecoursgracieux.name',
				'Originerecoursgracieux.actif' => array( 'type' => 'checkbox', 'value' => 1 ),
			)
		)
	);
?>