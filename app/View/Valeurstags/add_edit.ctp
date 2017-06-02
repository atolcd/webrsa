<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Valeurtag.id',
				'Valeurtag.categorietag_id' => array( 'empty' => true ),
				'Valeurtag.name'
			)
		)
	);
?>