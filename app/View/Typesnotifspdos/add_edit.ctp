<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Typenotifpdo.id',
				'Typenotifpdo.libelle',
				'Typenotifpdo.modelenotifpdo'
			)
		)
	);
?>