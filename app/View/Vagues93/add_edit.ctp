<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Vague93.id',
				'Vague93.datedebut' => array( 'type' => 'date', 'dateFormat' => 'DMY', 'minYear' => '2016', 'maxYear' => date('Y')+1 ),
				'Vague93.datefin' => array( 'type' => 'date', 'dateFormat' => 'DMY', 'minYear' => '2016', 'maxYear' => date('Y')+1 ),
			)
		)
	);
?>