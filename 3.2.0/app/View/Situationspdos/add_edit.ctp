<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Situationpdo.id' => array( 'type' => 'hidden' ),
				'Situationpdo.libelle' => array( 'required' => true ),
				'Situationpdo.isactif' => array( 'type' => 'radio', 'empty' => false )
			)
		)
	);
?>