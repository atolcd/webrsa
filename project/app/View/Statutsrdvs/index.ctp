<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array_merge(
				array(
					'Statutrdv.libelle',
					'Statutrdv.code_statut',
				),
				( 58 == Configure::read( 'Cg.departement' ) )
					? array( 'Statutrdv.provoquepassagecommission' )
					: array()
				,
				( 66 == Configure::read( 'Cg.departement' ) )
					? array( 'Statutrdv.permetpassageepl' )
					: array()
				,
				array(
					'/Statutsrdvs/edit/#Statutrdv.id#' => array(
						'title' => true
					),
					'/Statutsrdvs/delete/#Statutrdv.id#' => array(
						'title' => true,
						'confirm' => true,
						'disabled' => 'true == "#Statutrdv.has_linkedrecords#"'
					)
				)
			),
			'backUrl' => '/Parametrages/index/#rendezvous'
		)
	);
?>