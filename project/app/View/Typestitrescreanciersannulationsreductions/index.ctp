<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typetitrecreancierannulationreduction.id',
				'Typetitrecreancierannulationreduction.nom',
				'Typetitrecreancierannulationreduction.actif' => array( 'type' => 'boolean' ),
				'/Typestitrescreanciersannulationsreductions/edit/#Typetitrecreancierannulationreduction.id#' => array(
					'title' => true
				),
				'/Typestitrescreanciersannulationsreductions/delete/#Typetitrecreancierannulationreduction.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Typetitrecreancierannulationreduction.has_linkedrecords#"'
				)
			)
		)
	);