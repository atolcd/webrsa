<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array_merge(
				array(
					'Serviceinstructeur.lib_service',
					'Serviceinstructeur.num_rue',
					'Serviceinstructeur.type_voie',
					'Serviceinstructeur.nom_rue',
					'Serviceinstructeur.code_insee',
					'Serviceinstructeur.code_postal',
					'Serviceinstructeur.ville',
					'Serviceinstructeur.email',
					'Serviceinstructeur.numdepins',
					'Serviceinstructeur.typeserins',
					'Serviceinstructeur.numcomins',
					'Serviceinstructeur.numagrins'
				),
				Configure::read( 'Recherche.qdFilters.Serviceinstructeur' )
					? array( 'Serviceinstructeur.sqrecherche' => array( 'type' => 'boolean' ) )
					: array(),
				array(
					'/Servicesinstructeurs/edit/#Serviceinstructeur.id#' => array(
						'title' => true
					),
					'/Servicesinstructeurs/delete/#Serviceinstructeur.id#' => array(
						'title' => true,
						'confirm' => true,
						'disabled' => 'true == "#Serviceinstructeur.has_linkedrecords#"'
					)
				)
			),
			'options' => $options
		)
	);
?>