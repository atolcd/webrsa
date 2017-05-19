<h1><?php echo $this->pageTitle = 'Décision du CG pour le dossier'; ?></h1>

<?php
	$file = sprintf( 'decisioncg.%s.ctp', Inflector::underscore( $themeName ) );
	require_once  $file ;

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'commissionseps',
			'action'     => 'traitercg',
			$commissionep_id
		),
		array(
			'id' => 'Back'
		)
	);
?>
