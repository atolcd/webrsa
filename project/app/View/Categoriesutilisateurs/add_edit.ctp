<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Categorieutilisateur.id',
				'Categorieutilisateur.libelle',
				'Categorieutilisateur.code' => ['disabled' => ($this->action == 'edit')],
				'Categorieutilisateur.actif' => ['default' => true]
			)
		)
	);
?>