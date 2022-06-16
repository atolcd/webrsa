<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Soussujetcer.id',
				'Soussujetcer.libelle',
				'Soussujetcer.sujetcer_id'
			)
		)
	);