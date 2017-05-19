<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Typecourrierpcg66.id',
				'Typecourrierpcg66.name',
				'Typecourrierpcg66.isactif' => array( 'empty' => true )
			)
		)
	);
?>