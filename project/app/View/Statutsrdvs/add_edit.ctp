<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array_merge(
				array(
					'Statutrdv.id',
					'Statutrdv.libelle',
					'Statutrdv.code_statut',
					'Statutrdv.actif' => array( 'type' => 'checkbox', 'value' => 1 )
				),
				( 58 == Configure::read( 'Cg.departement' ) )
					? array( 'Statutrdv.provoquepassagecommission' => array( 'type' => 'radio' ) )
					: array()
				,
				( 66 == Configure::read( 'Cg.departement' ) )
					? array( 'Statutrdv.permetpassageepl' => array( 'type' => 'radio' ) )
					: array()
			)
		)
	);
?>