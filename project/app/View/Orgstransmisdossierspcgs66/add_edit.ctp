<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Orgtransmisdossierpcg66.id',
				'Orgtransmisdossierpcg66.name',
				'Orgtransmisdossierpcg66.poledossierpcg66_id' => array( 'empty' => true ),
				'Orgtransmisdossierpcg66.generation_auto' => array( 'empty' => true ),
				'Orgtransmisdossierpcg66.isactif' => array( 'empty' => true )
			)
		)
	);
?>