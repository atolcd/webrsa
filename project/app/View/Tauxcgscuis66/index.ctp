<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Tauxcgcui66.typeformulaire',
				'Tauxcgcui66.secteurmarchand',
				'Tauxcgcui66.typecontrat',
				'Tauxcgcui66.tauxfixeregion',
				'Tauxcgcui66.priseenchargeeffectif',
				'Tauxcgcui66.tauxcg',
				'/Tauxcgscuis66/edit/#Tauxcgcui66.id#' => array(
					'title' => true
				),
				'/Tauxcgscuis66/delete/#Tauxcgcui66.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Tauxcgcui66.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#cuis'
		)
	);
?>