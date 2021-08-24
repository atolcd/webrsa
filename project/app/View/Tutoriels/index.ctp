<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Tutoriel.rg',
				'Tutoriel.titre',
				'Parent.titre',
				'Tutoriel.fichiermodule_id' => array('type' => 'boolean'),
				'Tutoriel.actif' => array('type' => 'boolean'),
				'/Tutoriels/edit/#Tutoriel.id#' => array(
					'title' => true
				),
				'/Tutoriels/delete/#Tutoriel.id#' => array(
					'title' => true,
					'confirm' => true,
				)
			)
		)
	);
