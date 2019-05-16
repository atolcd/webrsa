<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Dreesorganisme.id',
				'Dreesorganisme.lib_dreesorganisme',
				'Parent.lib_dreesorganisme',
				'Dreesorganisme.actif' => array( 'type' => 'boolean' ),
				'/Dreesorganismes/edit/#Dreesorganisme.id#' => array(
					'title' => true
				),
				'/Dreesorganismes/delete/#Dreesorganisme.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Dreesorganisme.has_linkedrecords#"'
				)
			)
		)
	);
?>