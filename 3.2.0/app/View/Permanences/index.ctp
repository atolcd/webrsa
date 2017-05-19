<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Permanence.libpermanence',
				'Structurereferente.lib_struc',
				'Permanence.numtel',
				'Permanence.numvoie',
				'Permanence.typevoie',
				'Permanence.nomvoie',
				'Permanence.codepos',
				'Permanence.ville',
				'Permanence.actif',
				'/Permanences/edit/#Permanence.id#' => array(
					'title' => true
				),
				'/Permanences/delete/#Permanence.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Permanence.has_linkedrecords#"'
				)
			)
		)
	);
?>