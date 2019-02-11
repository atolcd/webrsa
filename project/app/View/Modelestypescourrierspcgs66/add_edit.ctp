<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Modeletypecourrierpcg66.id' => array( 'type' => 'hidden' ),
				'Modeletypecourrierpcg66.name',
				'Modeletypecourrierpcg66.typecourrierpcg66_id' => array( 'empty' => true ),
				'Modeletypecourrierpcg66.modeleodt',
				'Modeletypecourrierpcg66.ismontant' => array( 'empty' => true ),
				'Modeletypecourrierpcg66.isdates' => array( 'empty' => true ),
				'Modeletypecourrierpcg66.isactif' => array( 'empty' => true )
			)
		)
	);
?>