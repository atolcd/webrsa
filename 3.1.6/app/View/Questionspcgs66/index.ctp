<h1><?php echo $this->pageTitle = 'Tableau des questions';?></h1>

<?php
	if ( $compteurs['Decisionpcg66'] == 0 ) {
		echo "<p class='error'>Merci d'ajouter au moins une décision avant d'ajouter une EP.</p>";
	}
	if ( $compteurs['Compofoyerpcg66'] == 0 ) {
		echo "<p class='error'>Merci d'ajouter au moins une composition de foyer avant d'ajouter une EP.</p>";
	}

	echo $this->Default2->index(
		$questionspcgs66,
		array(
			'Questionpcg66.defautinsertion',
			'Compofoyerpcg66.name',
			'Questionpcg66.recidive',
			'Questionpcg66.phase',
			'Decisionpcg66.name'
		),
		array(
			'options' => $options,
			'actions' => array(
				'Questionspcgs66::edit',
				'Questionspcgs66::delete'
			),
			'add' => array( 'Questionpcg66.add', 'disabled' => ( $compteurs['Decisionpcg66'] == 0 || $compteurs['Compofoyerpcg66'] == 0 ) )
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'decisionsdossierspcgs66',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>