<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Categoriepiecejointe.id',
				'Categoriepiecejointe.nom',
				'Categoriepiecejointe.mailauto' => array( 'type' => 'checkbox'),
				'Categoriepiecejointe.actif' => array( 'type' => 'checkbox', 'value' => 1 ),
			)
		)
	);
?>