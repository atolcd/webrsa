<?php
	$departement = (int)Configure::read( 'Cg.departement' );

	if( 93 === $departement ){
		$cells = array(
			'Ep.identifiant',
			'Ep.adressemail',
			'Regroupementep.name',
			'Ep.name'
		);
	}
	else {
		$cells = array(
			'Ep.identifiant',
			'Regroupementep.name',
			'Ep.name'
		);
	}

	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array_merge(
				$cells,
				array(
					'/Eps/edit/#Ep.id#' => array(
						'title' => true
					),
					'/Eps/delete/#Ep.id#' => array(
						'title' => true,
						'confirm' => true,
						'disabled' => 'true == "#Ep.has_linkedrecords#"'
					)
				)
			),
			'addDisabled' => ( false !== array_search( 'error', $messages ) ),
			'backUrl' => false
		)
	);
?>