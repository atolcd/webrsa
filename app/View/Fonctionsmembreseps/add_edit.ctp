<h1>
<?php
	if( $this->action == 'add' ) {
		echo $this->pageTitle = 'Ajout d\'une fonction pour les membres d\'E.P.';
	}
	else {
		echo $this->pageTitle = 'Modification d\'une fonction pour les membres d\'E.P.';
	}
?>
</h1>

<?php
	echo $this->Default->form(
		array(
			'Fonctionmembreep.name'
		),
		array(
			'id' => 'FonctionmembreepAddEditForm',
			'options' => $options
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'fonctionsmembreseps',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>