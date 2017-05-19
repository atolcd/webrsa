<?php
	$departement = Configure::read( 'Cg.departement' );

	if( 66 === $departement ) {
		$fields = array(
			'Decisionpdo.libelle',
			'Decisionpdo.clos',
			'Decisionpdo.cerparticulier',
			'Decisionpdo.isactif',
		);
	}
	else {
		$fields = array(
			'Decisionpdo.libelle',
			'Decisionpdo.clos',
			'Decisionpdo.modeleodt',
			'Decisionpdo.isactif',
		);
	}

	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array_merge(
				$fields,
				array(
					'/Decisionspdos/edit/#Decisionpdo.id#' => array(
						'title' => true
					),
					'/Decisionspdos/delete/#Decisionpdo.id#' => array(
						'title' => true,
						'confirm' => true,
						'disabled' => 'true == "#Decisionpdo.has_linkedrecords#"'
					)
				)
			),
			'backUrl' => '/Parametrages/index/#pdos'
		)
	);
?>