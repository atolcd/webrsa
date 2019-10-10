<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Typerecoursgracieux.id',
				'Typerecoursgracieux.nom',
				'Typerecoursgracieux.usage',
				'Typerecoursgracieux.actif' => array( 'type' => 'checkbox', 'value' => 1 ),
			)
		)
	);
?>