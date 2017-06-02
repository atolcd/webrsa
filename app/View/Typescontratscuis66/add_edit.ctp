<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Typecontratcui66.id',
				'Typecontratcui66.name',
				'Typecontratcui66.actif' => array( 'type' => 'checkbox' )
			)
		)
	);
?>