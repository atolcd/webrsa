<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Tiersprestataireapre.nomtiers' ,
				'Tiersprestataireapre.siret' ,
				'Tiersprestataireapre.adresse',
				'Tiersprestataireapre.numtel' ,
				'Tiersprestataireapre.adrelec' ,
				'Tiersprestataireapre.aidesliees',
				'/Tiersprestatairesapres/edit/#Tiersprestataireapre.id#' => array(
					'title' => true
				),
				'/Tiersprestatairesapres/delete/#Tiersprestataireapre.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Tiersprestataireapre.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#apres'
		)
	);
?>