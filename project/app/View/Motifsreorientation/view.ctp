<?php
	echo $this->Theme->view(
		$ep,
		array(
			'Ep.id',
			'Ep.name',
			'Ep.date',
			'Ep.terminee' => array( 'type' => 'boolean' ),
		),
		array(
			'widget' => 'table',
			'id' => 'Ep'
		)
	);
?>