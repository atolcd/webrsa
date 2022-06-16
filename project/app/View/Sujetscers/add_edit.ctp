<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Sujetcer.id',
				'Sujetcer.libelle',
				'Sujetcer.champtexte'
			)
		)
	);