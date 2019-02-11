<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Thematiquerdv.id',
				'Thematiquerdv.name',
				'Thematiquerdv.typerdv_id' => array( 'empty' => true ),
				'Thematiquerdv.statutrdv_id' => array( 'empty' => true ),
				'Thematiquerdv.linkedmodel' => array( 'empty' => true )
			)
		)
	);
?>