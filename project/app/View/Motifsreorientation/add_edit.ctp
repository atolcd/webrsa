<?php
	echo $this->Theme->form(
		array(
			'Ep.name',
			'Ep.date',
			'Ep.terminee' => array( 'type' => 'checkbox' ),
		)
	);
?>