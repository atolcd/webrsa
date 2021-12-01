<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Categorieutilisateur.id',
				'Categorieutilisateur.libelle',
				'Categorieutilisateur.actif'
			)
		)
	);
?>