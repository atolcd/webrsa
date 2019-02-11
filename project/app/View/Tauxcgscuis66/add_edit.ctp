<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Tauxcgcui66.id',
				'Tauxcgcui66.typeformulaire',
				'Tauxcgcui66.secteurmarchand',
				'Tauxcgcui66.typecontrat',
				'Tauxcgcui66.tauxfixeregion' => array( 'class' => 'percent' ),
				'Tauxcgcui66.priseenchargeeffectif' => array( 'class' => 'percent' ),
				'Tauxcgcui66.tauxcg' => array( 'class' => 'percent' )
			)
		)
	);
?>