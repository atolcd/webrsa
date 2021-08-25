<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Sitecov58.name',
				'Sitecov58.lib_adresse',
				'Sitecov58.num_voie',
				'Sitecov58.type_voie' => array( 'empty' => true ),
				'Sitecov58.nom_voie',
				'Sitecov58.code_postal',
				'Sitecov58.ville',
				'Sitecov58.code_insee',
				'Sitecov58.actif',
				'/Sitescovs58/edit/#Sitecov58.id#' => array(
					'title' => true
				),
				'/Sitescovs58/delete/#Sitecov58.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Sitecov58.has_linkedrecords#"'
				)
			)
		)
	);
?>