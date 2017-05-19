<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Participantcomite.id',
				'Participantcomite.qual' => array( 'empty' => true ),
				'Participantcomite.nom',
				'Participantcomite.prenom',
				'Participantcomite.fonction' => array( 'type' => 'text' ),
				'Participantcomite.organisme' => array( 'type' => 'text' ),
				'Participantcomite.numtel' => array( 'maxlength' => 14 ),
				'Participantcomite.mail'
			)
		)
	);
?>