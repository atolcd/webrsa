<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Accueilarticle.id',
				'Accueilarticle.title',
				'Accueilarticle.content',
				'Accueilarticle.actif' => array( 'type' => 'checkbox' ),
				'Accueilarticle.publicationto',
				'Accueilarticle.publicationfrom',
			)
		)
	);
?>