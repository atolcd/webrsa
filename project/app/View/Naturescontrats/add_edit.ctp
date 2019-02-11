<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Naturecontrat.id',
				'Naturecontrat.name',
				'Naturecontrat.isduree' => array( 'type' => 'checkbox' )
			)
		)
	);
?>