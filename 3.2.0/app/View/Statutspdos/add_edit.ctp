<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Statutpdo.id' => array( 'type' => 'hidden' ),
				'Statutpdo.libelle' => array( 'required' => true ),
				'Statutpdo.isactif' => array( 'type' => 'radio', 'empty' => false )
			)
		)
	);
?>