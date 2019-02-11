<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Commentairenormecer93.id',
				'Commentairenormecer93.name',
				'Commentairenormecer93.isautre' => array( 'type' => 'checkbox' )
			)
		)
	);
?>