<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Membreep.id',
				'Membreep.fonctionmembreep_id' => array( 'empty' => true ),
				'Membreep.qual',
				'Membreep.nom',
				'Membreep.prenom',
				'Membreep.organisme',
				'Membreep.tel',
				'Membreep.mail',
				'Membreep.numvoie',
				'Membreep.typevoie' => array( 'empty' => true ),
				'Membreep.nomvoie',
				'Membreep.compladr',
				'Membreep.codepostal',
				'Membreep.ville'
			)
		)
	);
?>