<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Typerecoursgracieux.id',
				'Typerecoursgracieux.name',
				'Typerecoursgracieux.usage',
				'Typerecoursgracieux.actif' => array( 'type' => 'checkbox', 'value' => 1 ),
			)
		)
	);
?>