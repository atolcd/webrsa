<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Questionpcg66.id',
				'Questionpcg66.defautinsertion' => array( 'empty' => true ),
				'Questionpcg66.compofoyerpcg66_id' => array( 'empty' => true ),
				'Questionpcg66.recidive' => array( 'type' => 'radio' ),
				'Questionpcg66.phase' => array( 'empty' => true ),
				'Questionpcg66.decisionpcg66_id' => array( 'empty' => true )
			)
		)
	);
?>