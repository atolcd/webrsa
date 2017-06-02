<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Textareacourrierpdo.id',
				'Textareacourrierpdo.courrierpdo_id' => array( 'empty' => true ),
				'Textareacourrierpdo.nomchampodt',
				'Textareacourrierpdo.name' => array( 'type' => 'text' ),
				'Textareacourrierpdo.ordre'
			)
		)
	);
?>