<?php
	echo $this->Default2->index(
		$dossiers[$theme],
		array(
			'Dossier.numdemrsa',
			'Dossier.matricule',
			'Personne.qual',
			'Personne.nom',
			'Personne.prenom',
			'Personne.dtnai',
			'Typeorient.lib_type_orient',
			'Structurereferente.lib_struc',
			'Orientstruct.date_valid',
			'Foyer.enerreur' => array( 'type' => 'string', 'class' => 'foyer_enerreur' ),
			'Passagecommissionep.chosen' => array( 'input' => 'checkbox' ),
		),
		array(
			'cohorte' => true,
			'options' => $options,
			'hidden' => array( 'Dossierep.id', 'Passagecommissionep.id' ),
			'paginate' => Inflector::classify( $theme ),
			'id' => $theme,
			'labelcohorte' => array(
				'Enregistrer',
				'Annuler' => array( 'name' => 'Cancel' ),
			),
			'cohortehidden' => array( 'Choose.theme' => array( 'value' => $theme ) ),
			'trClass' => $trClass,
		)
	);
?>