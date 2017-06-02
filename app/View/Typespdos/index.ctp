<?php
	$departement = (int)Configure::read( 'Cg.departement' );

	$fields = array(
		'Typepdo.libelle'
	);

	if( 66 === $departement ) {
		$fields = array_merge(
			$fields,
			array(
				'Typepdo.originepcg' => array( 'type' => 'radio' ),
				'Typepdo.cerparticulier' => array( 'type' => 'radio' )
			)
		);
	}

	$fields['Typepdo.actif'] = array( 'type' => 'boolean' );

	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array_merge(
				$fields,
				array(
					'/Typespdos/edit/#Typepdo.id#' => array(
						'title' => true
					),
					'/Typespdos/delete/#Typepdo.id#' => array(
						'title' => true,
						'confirm' => true,
						'disabled' => 'true == "#Typepdo.has_linkedrecords#"'
					)
				)
			),
			'backUrl' => '/Parametrages/index/#pdos'
		)
	);
?>