<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Permanence.id',
				'Permanence.libpermanence',
				'Permanence.structurereferente_id' => array( 'empty' => true ),
				'Permanence.numtel',
				'Permanence.numvoie',
				'Permanence.typevoie' => array( 'empty' => true ),
				'Permanence.nomvoie',
				'Permanence.compladr',
				'Permanence.codepos',
				'Permanence.ville',
				'Permanence.actif' => array( 'type' => 'radio' )
			)
		)
	);
?>