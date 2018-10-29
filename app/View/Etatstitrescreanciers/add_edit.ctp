<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Etattitrecreancier.id',
				'Etattitrecreancier.name',
				'Etattitrecreancier.actif' => array( 'type' => 'checkbox' )
			)
		)
	);
?>