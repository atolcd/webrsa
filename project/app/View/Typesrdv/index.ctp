<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array_merge(
				array(
					'Typerdv.libelle',
					'Typerdv.modelenotifrdv',
					'Typerdv.code_type'
				),
				66 == Configure::read( 'Cg.departement' )
					? array( 'Typerdv.nbabsaveplaudition' )
					: array()
				,
				array(
					'/Typesrdv/edit/#Typerdv.id#' => array(
						'title' => true
					),
					'/Typesrdv/delete/#Typerdv.id#' => array(
						'title' => true,
						'confirm' => true,
						'disabled' => 'true == "#Typerdv.has_linkedrecords#"'
					)
				)

			),
			'backUrl' => '/Parametrages/index/#rendezvous'
		)
	);
?>