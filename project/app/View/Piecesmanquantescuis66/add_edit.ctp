<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Piecemanquantecui66.id',
				'Piecemanquantecui66.name',
				'Piecemanquantecui66.actif' => array( 'type' => 'checkbox' )
			)
		)
	);
?>