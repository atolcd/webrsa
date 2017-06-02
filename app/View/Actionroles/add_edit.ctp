<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Actionrole.id',
				'Actionrole.name',
				'Actionrole.role_id' => array( 'empty' => true ),
				'Actionrole.categorieactionrole_id' => array( 'empty' => true ),
				'Actionrole.description',
				'Actionrole.url'
			)
		)
	);
?>