<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Descriptionpdo.id',
				'Descriptionpdo.name',
				'Descriptionpdo.modelenotification',
				'Descriptionpdo.sensibilite' => array( 'type' => 'radio' ),
				'Descriptionpdo.decisionpcg' => array( 'type' => 'radio' ),
				'Descriptionpdo.nbmoisecheance' => array( 'empty' => true ),
				'Descriptionpdo.dateactive' => array( 'empty' => true )
			)
		)
	);
?>