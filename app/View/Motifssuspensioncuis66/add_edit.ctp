<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Motifsuspensioncui66.id',
				'Motifsuspensioncui66.name',
				'Motifsuspensioncui66.actif' => array( 'type' => 'checkbox' )
			)
		)
	);
?>