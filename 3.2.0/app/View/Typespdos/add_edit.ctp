<?php
	$departement = (int)Configure::read( 'Cg.departement' );

	$fields = array(
		'Typepdo.id',
		'Typepdo.libelle'
	);

	if( 66 === $departement ) {
		$fields = array_merge(
			$fields,
			array(
				'Typepdo.originepcg' => array( 'type' => 'radio' ),
				'Typepdo.cerparticulier' => array( 'type' => 'radio' )
			)
		);
	}
	else {
		$fields = array_merge(
			$fields,
			array(
				'Typepdo.originepcg' => array( 'type' => 'hidden', 'value' => 'N' )
			)
		);
	}

	$fields['Typepdo.actif'] = array( 'type' => 'checkbox' );

	echo $this->element( 'WebrsaParametrages/add_edit', array( 'fields' => $fields ) );
?>