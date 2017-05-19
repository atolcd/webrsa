<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'modelName' => 'Personne',
			'exportcsv' => array( 'action' => 'exportcsv_possibles' )
		)
	);
?>