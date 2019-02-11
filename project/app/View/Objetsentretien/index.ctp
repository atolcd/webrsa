<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Objetentretien.name',
				'Objetentretien.modeledocument',
				'/Objetsentretien/edit/#Objetentretien.id#' => array(
					'title' => true
				),
				'/Objetsentretien/delete/#Objetentretien.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Objetentretien.has_linkedrecords#"'
				)
			)
		)
	);
?>