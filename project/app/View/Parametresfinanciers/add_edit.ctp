<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Parametrefinancier.id',
				'Parametrefinancier.entitefi',
				'Parametrefinancier.engagement',
				'Parametrefinancier.tiers',
				'Parametrefinancier.codecdr',
				'Parametrefinancier.libellecdr',
				'Parametrefinancier.natureanalytique',
				'Parametrefinancier.lib_natureanalytique',
				'Parametrefinancier.programme',
				'Parametrefinancier.lib_programme',
				'Parametrefinancier.apreforfait',
				'Parametrefinancier.aprecomplem',
				'Parametrefinancier.natureimput'
			)
		)
	);
?>