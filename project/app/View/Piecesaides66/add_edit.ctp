<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Pieceaide66.id',
				'Pieceaide66.name',
				'Pieceaide66.actif' => array( 'type' => 'checkbox' )
			)
		)
	);
?>