<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Typecontrat.id',
				'Typecontrat.libelle'
			)
		)
	);