<?php
	$departement = Configure::read( 'Cg.departement' );

	$fields = array(
		'Originepdo.id',
		'Originepdo.libelle'
	);

	if( 66 == $departement ) {
		$fields = array_merge(
			$fields,
			array(
				'Originepdo.originepcg' => array( 'type' => 'radio' ),
				'Originepdo.cerparticulier' => array( 'type' => 'radio' )
			)
		);
	}
	else {
		$fields = array_merge(
			$fields,
			array(
				'Originepdo.originepcg' => array( 'type' => 'hidden', 'value' => 'N' )
			)
		);
	}

	$fields['Originepdo.actif'] = array( 'type' => 'checkbox' );

	echo $this->element( 'WebrsaParametrages/add_edit', array( 'fields' => $fields ) );
?>