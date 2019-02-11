<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Coderomemetierdsp66.id',
				'Coderomemetierdsp66.code',
				'Coderomemetierdsp66.name',
				'Coderomemetierdsp66.coderomesecteurdsp66_id' => array( 'empty' => true )
			)
		)
	);
?>