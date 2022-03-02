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


	if(Configure::read('Cg.departement') == 58){
		echo '<h1>'.__m('exceptionimpressiontypeorient.titre').'</h1>';

		echo $this->Default3->actions([]
			+ array( '/exceptionsimpressionstypesorients/add/'.$id => array( 'disabled' => false ) )
		);
		echo $this->Default3->index(
			$exceptions,
			$this->Translator->normalize(
				array(
					'Exceptionimpressiontypeorient.origine',
					'Exceptionimpressiontypeorient.act',
					'Exceptionimpressiontypeorient.porteurprojet',
					'Exceptionimpressiontypeorient.modele_notif',
					'Exceptionimpressiontypeorient.actif',
					'/exceptionsimpressionstypesorients/edit/#Exceptionimpressiontypeorient.id#' => array(
						'title' => false
					),
					'/exceptionsimpressionstypesorients/monter/#Exceptionimpressiontypeorient.id#/#Typeorient.id#' => array(
						'title' => false,
						'disabled' => '#Exceptionimpressiontypeorient.id# =='. $premier_id
					),
					'/exceptionsimpressionstypesorients/descendre/#Exceptionimpressiontypeorient.id#/#Typeorient.id#' => array(
						'title' => false,
						'disabled' => '#Exceptionimpressiontypeorient.id# =='. $dernier_id
					),
					'/exceptionsimpressionstypesorients/delete/#Exceptionimpressiontypeorient.id#' => array(
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
	}

	echo '<h1>'.__m('typesorients.liste').'</h1>';

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
		]
	);


	echo $this->Default3->index(
		$typesorients,
		$this->Translator->normalize(
			$colonnes
		),
		array(
			'options' => $options,
			'paginate' => false
		)
	);

