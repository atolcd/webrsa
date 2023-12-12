<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => 	array(
				'Listedecisionsuspensionsep93.libelle',
				'Listedecisionsuspensionsep93.premier_niveau',
				'Listedecisionsuspensionsep93.deuxieme_niveau',
				'Listedecisionsuspensionsep93.actif',
				'/Listedecisionssuspensionseps93/edit/#Listedecisionsuspensionsep93.id#' => array(
					'title' => true
				),
			),
			'backUrl' => '/Parametrages/index/#eps',
			'addDisabled' => true
		)
	);
?>