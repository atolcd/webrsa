<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Typeorient.id',
				'Typeorient.lib_type_orient',
				'Typeorient.parentid' => array( 'type' => 'select', 'empty' => true ),
				'Typeorient.modele_notif',
				'Typeorient.modele_notif_cohorte',
				'Typeorient.code_type_orient',
				'Typeorient.actif' => array( 'type' => 'radio', 'legend' => required( __d( 'typeorient', 'Typeorient.actif' ) ) ),
				'Typeorient.actif_dossier',
			),
			'options' => $options
		)
	);

	echo $this->Default3->index(
		$typesorients,
		$this->Translator->normalize(
			array(
				'Typeorient.id',
				'Typeorient.lib_type_orient',
				'Parent.lib_type_orient',
				'Typeorient.modele_notif',
				'Typeorient.modele_notif_cohorte',
				'Typeorient.actif',
				'Typeorient.actif_dossier',
			)
		),
		array(
			'options' => $options,
			'paginate' => false
		)
	);
?>
