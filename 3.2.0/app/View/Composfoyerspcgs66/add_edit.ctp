<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Compofoyerpcg66.id',
				'Compofoyerpcg66.name',
				'Compofoyerpcg66.actif' => array( 'empty' => true )
			)
		)
	);
?>