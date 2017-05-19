<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Poledossierpcg66.id',
				'Poledossierpcg66.name',
				'Poledossierpcg66.originepdo_id' => array( 'empty' => true ),
				'Poledossierpcg66.typepdo_id' => array( 'empty' => true ),
				'Poledossierpcg66.isactif' => array( 'type' => 'radio' )
			)
		)
	);
?>