<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Suiviaideapretypeaide.typeaide',
				'Suiviaideapre.nom_complet'
			),
			'addUrl' => true === empty( $results )
				? '/Suivisaidesaprestypesaides/add'
				: '/Suivisaidesaprestypesaides/edit'
			,
			'backUrl' => '/Parametrages/index/#apres'
		)
	);
?>