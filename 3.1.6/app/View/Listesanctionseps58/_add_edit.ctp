<h1>
<?php
	if( $this->action == 'add' ) {
		echo $this->pageTitle = 'Ajout d\'une sanction pour les Équipes Pluridisciplinaire';
	}
	else {
		echo $this->pageTitle = 'Modification d\'une sanction pour les Équipes Pluridisciplinaire';
	}
?>
</h1>

<?php
	echo $this->Default2->form(
		array(
			'Listesanctionep58.rang' => array( 'required' => true ),
			'Listesanctionep58.sanction' => array( 'required' => true ),
			'Listesanctionep58.duree' => array( 'required' => true )
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'listesanctionseps58',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>