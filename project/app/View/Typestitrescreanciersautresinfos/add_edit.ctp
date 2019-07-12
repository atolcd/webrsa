<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Typetitrecreancierautreinfo.id',
				'Typetitrecreancierautreinfo.nom',
				'Typetitrecreancierautreinfo.actif' => array( 'type' => 'checkbox', 'value' => 1),
			)
		)
	);
?>