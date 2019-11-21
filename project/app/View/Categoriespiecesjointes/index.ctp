<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Categoriepiecejointe.nom',
				'Categoriepiecejointe.mailauto' => array( 'type' => 'boolean' ),
				'Categoriepiecejointe.actif' => array( 'type' => 'boolean' ),
				'/Categoriespiecesjointes/edit/#Categoriepiecejointe.id#' => array(
					'title' => true
				),
				'/Categoriespiecesjointes/delete/#Categoriepiecejointe.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Categoriepiecejointe.has_linkedrecords#"'
				)
			)
		)
	);