<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Participantcomite.qual',
				'Participantcomite.nom',
				'Participantcomite.prenom',
				'Participantcomite.fonction',
				'Participantcomite.organisme',
				'Participantcomite.numtel',
				'Participantcomite.mail',
				'/Participantscomites/edit/#Participantcomite.id#' => array(
					'title' => true
				),
				'/Participantscomites/delete/#Participantcomite.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Participantcomite.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#apres'
		)
	);
?>