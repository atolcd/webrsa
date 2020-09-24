<?php
	$departement = Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );
	$actions = array();
	$modelName = 'Personne';
?>


<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'actions' => $actions,
			'exportcsv' => array( 'action' => 'exportcsv' ),
			'modelName' => $modelName
		)
	);
