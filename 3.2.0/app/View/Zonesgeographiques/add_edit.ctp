<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Zonegeographique.id',
				'Zonegeographique.libelle',
				'Zonegeographique.codeinsee'
			)
		)
	);
?>