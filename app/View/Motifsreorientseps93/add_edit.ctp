<h1>
<?php
	if( $this->action == 'add' ) {
		echo $this->pageTitle = 'Ajout d\'un motif de demande de réorientation';
	}
	else {
		echo $this->pageTitle = 'Modification d\'un motif de demande de réorientation';
	}
?>
</h1>

<?php
	echo $this->Default->form(
		array(
			'Motifreorientep93.name'
		),
		array(
			'id' => 'Motifreorientep93AddEditForm'
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'motifsreorientseps93',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>