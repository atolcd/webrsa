<?php
	$colonnes = [
		'Typeorient.id',
		'Typeorient.lib_type_orient',
		'Parent.lib_type_orient',
		'Typeorient.modele_notif',
		'Typeorient.modele_notif_cohorte'
	];
	if(Configure::read('Cg.departement') == 58){
		$colonnes[] = 'Typeorient.has_exceptions';
	}
	$colonnes = array_merge(
		$colonnes,
		[
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
		]
	);
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => $colonnes,
			'options' => $options
		)
	);
?>