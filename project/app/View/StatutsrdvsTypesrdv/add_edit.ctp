<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'StatutrdvTyperdv.typerdv_id' => array( 'empty' => true ),
				'StatutrdvTyperdv.statutrdv_id' => array( 'empty' => true ),
				'StatutrdvTyperdv.nbabsenceavantpassagecommission',
				'StatutrdvTyperdv.typecommission' => array( 'empty' => true ),
				'StatutrdvTyperdv.motifpassageep' => array( 'type' => 'text' )
			)
		)
	);
?>