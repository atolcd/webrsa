<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Accueilarticle.id',
				'Accueilarticle.title',
				'Accueilarticle.content',
				'Accueilarticle.publicationto',
				'Accueilarticle.publicationfrom',
				'Accueilarticle.actif' => array( 'type' => 'boolean' ),
				'/Accueilsarticles/edit/#Accueilarticle.id#' => array(
					'title' => true
				),
				'/Accueilsarticles/delete/#Accueilarticle.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Accueilarticle.has_linkedrecords#"'
				)
			)
		)
	);
?>