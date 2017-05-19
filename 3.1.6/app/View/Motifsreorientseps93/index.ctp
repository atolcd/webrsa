<h1><?php echo $this->pageTitle = 'Liste des motifs des demandes de réorientation à passer en EP';?></h1>

<?php
	echo $this->Default2->index(
		$motifsreorientseps93,
		array(
			'Motifreorientep93.name'
		),
		array(
			'actions' => array(
				'Motifsreorientseps93::edit',
				'Motifsreorientseps93::delete'
			),
			'add' => array( 'Motifsreorientseps93.add' )
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'gestionseps',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>