<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typeorient.id',
				'Typeorient.lib_type_orient',
				'Parent.lib_type_orient',
				'Typeorient.modele_notif',
				'Typeorient.modele_notif_cohorte',
				'Typeorient.actif',
				'Typeorient.actif_dossier',
				'Typeorient.code_type_orient',
				'/Typesorients/edit/#Typeorient.id#' => array(
					'title' => false
				),
				'/Typesorients/delete/#Typeorient.id#' => array(
					'title' => false,
					'confirm' => true,
					'disabled' => '0 != "#Typeorient.has_linkedrecords#"'
				)
			),
			'options' => $options
		)
	);
?>