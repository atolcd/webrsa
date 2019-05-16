<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Dreesactionscer.id',
				'Dreesactionscer.lib_dreesactioncer',
				'Dreesactionscer.actif' => array( 'type' => 'boolean' ),
				'/Dreesactionscers/edit/#Dreesactionscer.id#' => array(
					'title' => true
				),
				'/Dreesactionscers/delete/#Dreesactionscer.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Dreesactionscer.has_linkedrecords#"'
				)
			)
		)
	);
?>