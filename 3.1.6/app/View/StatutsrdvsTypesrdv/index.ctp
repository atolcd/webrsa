<h1><?php echo $this->pageTitle = 'Gestion pour passage en commission par objet et type de RDV';?></h1>

<?php

		echo $this->Default2->index(
			$statutsrdvs_typesrdv,
			array(
				'Typerdv.libelle',
				'Statutrdv.libelle',
				'StatutrdvTyperdv.nbabsenceavantpassagecommission',
				'StatutrdvTyperdv.typecommission',
				'StatutrdvTyperdv.motifpassageep'
			),
			array(
				'actions' => array(
					'StatutsrdvsTypesrdv::edit',
					'StatutsrdvsTypesrdv::delete'
				),
				'add' => array( 'StatutrdvTyperdv.add' ),
				'options' => $options
			)
		);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'gestionsrdvs',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);

?>
