<?php
	$departement = (int)Configure::read( 'Cg.departement' );

	$fields = array(
		'Regroupementep.name'
	);

	if ( 93 !== $departement ) {
		foreach( $options['Regroupementep']['themes'] as $theme ) {
			$fields[] = "Regroupementep.{$theme}";
		}
	}

	if ( 66 === $departement ) {
		$fields[] = "Regroupementep.nbminmembre";
		$fields[] = "Regroupementep.nbmaxmembre";
	}

	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array_merge(
				$fields,
				array(
					'/Regroupementseps/edit/#Regroupementep.id#' => array(
						'title' => true
					),
					'/Regroupementseps/delete/#Regroupementep.id#' => array(
						'title' => true,
						'confirm' => true,
						'disabled' => 'true == "#Regroupementep.has_linkedrecords#"'
					)
				)
			),
			'backUrl' => '/Parametrages/index/#eps'
		)
	);
?>