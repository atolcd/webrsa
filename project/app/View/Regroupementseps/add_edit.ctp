<?php
	$departement = Configure::read( 'Cg.departement' );

	$fields = array(
		'Regroupementep.id',
		'Regroupementep.name'
	);

	$fields = array_merge(
		$fields,
		// On laisse la possibilité de choisir comme avant pour le CG 58
		58 == $departement
			? array(
				'Regroupementep.nonorientationproep58' => array( 'type' => 'hidden', 'value' => 'decisionep' ),
				'Regroupementep.regressionorientationep58' => array( 'type' => 'hidden', 'value' => 'decisionep' ),
				'Regroupementep.sanctionep58' => array( 'type' => 'hidden', 'value' => 'decisionep' ),
				'Regroupementep.sanctionrendezvousep58' => array( 'type' => 'hidden', 'value' => 'decisionep' ),
				'Regroupementep.nbminmembre' => array( 'type' => 'hidden', 'value' => 0 ),
				'Regroupementep.nbmaxmembre' => array( 'type' => 'hidden', 'value' => 0 )
			)
			: array()
		,
		// Le choix est également possible pour le CG 66
		66 == $departement
			? array(
				'Regroupementep.saisinebilanparcoursep66' => array( 'empty' => true ),
				'Regroupementep.saisinepdoep66' => array( 'empty' => true ),
				'Regroupementep.defautinsertionep66' => array( 'empty' => true ),
				'Regroupementep.nbminmembre',
				'Regroupementep.nbmaxmembre'
			)
			: array()
		,
		// Le CG 93 ne souhaite pas voir ces choix: pour eux, tout se décide
		// au niveau cg, et toutes les eps traitent potentiellement de tous
		// les thèmes
		93 == $departement
			? array(
				'Regroupementep.reorientationep93' => array( 'type' => 'hidden', 'value' => 'decisioncg' ),
				'Regroupementep.nonrespectsanctionep93' => array( 'type' => 'hidden', 'value' => 'decisioncg' ),
				'Regroupementep.radiepoleemploiep93' => array( 'type' => 'hidden', 'value' => 'decisioncg' ),
				'Regroupementep.nonorientationproep93' => array( 'type' => 'hidden', 'value' => 'decisioncg' ),
				'Regroupementep.signalementep93' => array( 'type' => 'hidden', 'value' => 'decisioncg' ),
				'Regroupementep.contratcomplexeep93' => array( 'type' => 'hidden', 'value' => 'decisioncg' ),
				'Regroupementep.nbminmembre' => array( 'type' => 'hidden', 'value' => 0 ),
				'Regroupementep.nbmaxmembre' => array( 'type' => 'hidden', 'value' => 0 )
			)
			: array()
	);

	echo $this->element( 'WebrsaParametrages/add_edit', array( 'fields' => $fields ) );
?>