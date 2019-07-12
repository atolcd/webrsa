<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Typetitrecreancierannulationreduction.id',
				'Typetitrecreancierannulationreduction.nom',
				'Typetitrecreancierannulationreduction.actif' => array( 'type' => 'checkbox', 'value' => 1 ),
			)
		)
	);
?>