<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Conclusioncer.libelle',
				'/Conclusioncer/edit/#Conclusioncer.id#' => array(
					'title' => true
				),
				'/Conclusioncer/delete/#Conclusioncer.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Conclusioncer.has_linkedrecords#"'
				)
			)
		)
	);