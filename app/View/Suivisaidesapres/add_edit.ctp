<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Suiviaideapre.id',
				'Suiviaideapre.qual' => array( 'empty' => true ),
				'Suiviaideapre.nom',
				'Suiviaideapre.prenom',
				'Suiviaideapre.numtel' => array( 'maxlength' => 14 )
			)
		)
	);
?>