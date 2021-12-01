<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Categorieutilisateur.libelle',
				'Categorieutilisateur.actif',
				'/Categoriesutilisateurs/edit/#Categorieutilisateur.id#' => array(
					'title' => true
				),
				'/Categoriesutilisateurs/delete/#Categorieutilisateur.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Categorieutilisateur.has_linkedrecords#"'
				)
			)
		)
	);
?>