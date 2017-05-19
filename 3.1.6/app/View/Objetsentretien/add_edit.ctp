<h1>
<?php
	if( $this->action == 'add' ) {
		echo $this->pageTitle = 'Ajout d\'un objet d\'entretien';
	}
	else {
		echo $this->pageTitle = 'Modification d\'un objet d\'entretien';
	}
?>
</h1>

<?php
	echo $this->Default->form(
		array(
			'Objetentretien.name',
			'Objetentretien.modeledocument'
		),
		array(
			'id' => 'ObjetentretienAddEditForm'
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'objetsentretien',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>