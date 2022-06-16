<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Valeurparsoussujetcer.id',
				'Valeurparsoussujetcer.libelle',
				'Valeurparsoussujetcer.soussujetcer_id'
			)
		)
	);