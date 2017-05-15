<h1><?php echo $this->pageTitle = 'Liste des équipes pluridisciplinaires';?></h1>

<?php
	if ( $compteurs['Regroupementep'] == 0 ) {
		echo "<p class='error'>Merci d'ajouter au moins un regroupement avant d'ajouter une EP.</p>";
	}
	if ( $compteurs['Membreep'] == 0 ) {
		echo "<p class='error'>Merci d'ajouter au moins un membre avant d'ajouter une EP.</p>";
	}

	if( Configure::read( 'Cg.departement' ) == 93  ){
		echo $this->Default2->index(
			$eps,
			array(
				'Ep.identifiant',
				'Ep.adressemail',
				'Regroupementep.name',
				'Ep.name'
			),
			array(
				'actions' => array(
					'Eps::edit',
					'Eps::delete'
				),
				'add' => array( 'Ep.add', 'disabled' => ( $compteurs['Regroupementep'] == 0 || $compteurs['Membreep'] == 0 ) ),
				'options' => $options
			)
		);
	}
	else{
		echo $this->Default2->index(
			$eps,
			array(
				'Ep.identifiant',
				'Regroupementep.name',
				'Ep.name'
			),
			array(
				'actions' => array(
					'Eps::edit',
					'Eps::delete'
				),
				'add' => array( 'Ep.add', 'disabled' => ( $compteurs['Regroupementep'] == 0 || $compteurs['Membreep'] == 0 ) ),
				'options' => $options
			)
		);
	}
?>