<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Typeaction.libelle',
				'/Typesactions/edit/#Typeaction.id#' => array(
					'title' => true
				),
				'/Typesactions/delete/#Typeaction.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Typeaction.has_linkedrecords#"'
				)
			)
		)
	);
?>