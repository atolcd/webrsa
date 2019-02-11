<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Typetitrecreancier.id',
				'Typetitrecreancier.name',
				'Typetitrecreancier.actif' => array( 'type' => 'checkbox' )
			)
		)
	);
?>