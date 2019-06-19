<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typetitrecreancierautreinfo.id',
				'Typetitrecreancierautreinfo.nom',
				'Typetitrecreancierautreinfo.actif' => array( 'type' => 'boolean' ),
				'/Typestitrescreanciersautresinfos/edit/#Typetitrecreancierautreinfo.id#' => array(
					'title' => true
				),
				'/Typestitrescreanciersautresinfos/delete/#Typetitrecreancierautreinfo.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Typetitrecreancierautreinfo.has_linkedrecords#"'
				)
			)
		)
	);