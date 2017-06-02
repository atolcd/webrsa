<?php
	$departement = (int)Configure::read( 'Cg.departement' );

	$fields = array( 'Originepdo.libelle' );

	if( 66 === $departement ) {
		$fields = array_merge(
			$fields,
			array(
				'Originepdo.originepcg',
				'Originepdo.cerparticulier'
			)
		);
	}

	$fields['Originepdo.actif'] = array( 'type' => 'boolean' );

	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array_merge(
				$fields,
				array(
					'/Originespdos/edit/#Originepdo.id#' => array(
						'title' => true
					),
					'/Originespdos/delete/#Originepdo.id#' => array(
						'title' => true,
						'confirm' => true,
						'disabled' => 'true == "#Originepdo.has_linkedrecords#"'
					)
				)
			),
			'backUrl' => '/Parametrages/index/#pdos'
		)
	);
?>