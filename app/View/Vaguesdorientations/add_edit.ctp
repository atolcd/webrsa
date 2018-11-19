<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Vaguedorientation.id',
				'Vaguedorientation.datedebut' => array( 'type' => 'date', 'dateFormat' => 'DMY', 'minYear' => '2016', 'maxYear' => date('Y')+1 ),
				'Vaguedorientation.datefin' => array( 'type' => 'date', 'dateFormat' => 'DMY', 'minYear' => '2016', 'maxYear' => date('Y')+1 ),
			)
		)
	);
?>