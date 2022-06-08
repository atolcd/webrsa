<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Soussujetcer.libelle',
				'Valeurparsoussujetcer.libelle',
				'/Valeursparsoussujetscers/edit/#Valeurparsoussujetcer.id#' => array(
					'title' => true
				),
				'/Valeursparsoussujetscers/delete/#Valeurparsoussujetcer.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Valeurparsoussujetcer.has_linkedrecords#"'
				)
			)
		)
	);
?>