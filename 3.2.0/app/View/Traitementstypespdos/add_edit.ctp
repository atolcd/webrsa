<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Traitementtypepdo.id',
				'Traitementtypepdo.name'
			)
		)
	);
?>