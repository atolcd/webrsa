<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Action.id',
				'Action.code',
				'Action.libelle',
				'Action.typeaction_id' => array( 'empty' => true )
			)
		)
	);
?>