<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Tiersprestataireapre.id',
				'Tiersprestataireapre.nomtiers',
				'Tiersprestataireapre.siret',
				'Tiersprestataireapre.numvoie',
				'Tiersprestataireapre.typevoie' => array( 'empty' => true ),
				'Tiersprestataireapre.nomvoie',
				'Tiersprestataireapre.compladr',
				'Tiersprestataireapre.codepos',
				'Tiersprestataireapre.ville',
				'Tiersprestataireapre.canton',
				'Tiersprestataireapre.numtel',
				'Tiersprestataireapre.adrelec',
				'Tiersprestataireapre.nomtiturib',
				'Tiersprestataireapre.etaban',
				'Tiersprestataireapre.guiban',
				'Tiersprestataireapre.numcomptban',
				'Tiersprestataireapre.nometaban',
				'Tiersprestataireapre.clerib',
				'Tiersprestataireapre.aidesliees' => array( 'empty' => true )
			)
		)
	);
?>