<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Sortieaccompagnementd2pdv93.id',
				'Sortieaccompagnementd2pdv93.name',
				'Sortieaccompagnementd2pdv93.code',
				'Sortieaccompagnementd2pdv93.codetypeemploi',
				'Sortieaccompagnementd2pdv93.parent_id' => array( 'empty' => true ),
				'Sortieaccompagnementd2pdv93.actif' => array( 'type' => 'checkbox' ),
			)
		)
	);
?>