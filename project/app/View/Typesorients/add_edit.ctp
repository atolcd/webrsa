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


	echo '<h1>'.__m('exceptionsimpression.titre').'</h1>';

	echo $this->Default3->actions([]
		+ array( '/exceptionsimpressions/add/'.$id => array( 'disabled' => false ) )
	);
	echo $this->Default3->index(
		$exceptions,
		$this->Translator->normalize(
			array(
				'Exceptionsimpression.origine',
				'Exceptionsimpression.act',
				'Exceptionsimpression.porteurprojet',
				'Exceptionsimpression.modele_notif',
				'Exceptionsimpression.actif',
				'/Exceptionsimpressions/edit/#Exceptionsimpression.id#' => array(
					'title' => false
				),
				'/Exceptionsimpressions/monter/#Exceptionsimpression.id#/#Typeorient.id#' => array(
					'title' => false,
					'disabled' => '#Exceptionsimpression.id# =='. $premier_id
				),
				'/Exceptionsimpressions/descendre/#Exceptionsimpression.id#/#Typeorient.id#' => array(
					'title' => false,
					'disabled' => '#Exceptionsimpression.id# =='. $dernier_id
				),
				'/Exceptionsimpressions/delete/#Exceptionsimpression.id#' => array(
					'title' => false,
					'confirm' => true,
				),
			)
		),
		array(
			'options' => $options,
			'paginate' => false
		)
	);

	echo '</br></br>';

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

