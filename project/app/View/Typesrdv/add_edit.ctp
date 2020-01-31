<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array_merge(
				array(
					'Typerdv.id',
					'Typerdv.libelle',
					'Typerdv.modelenotifrdv',
					'Typerdv.code_type'
				),
				66 === (int)Configure::read( 'Cg.departement' )
					? array( 'Typerdv.nbabsaveplaudition' )
					: array()
			)
		)
	);
?>