<?php
	echo $this->Default3->csv(
		$results,
		array(
			'Dossier.matricule',
			'Personne.nom_complet',
			'Adressefoyer.dtemm',
			'Adresse.localite',
			'Adressefoyer2.dtemm' => array( 'type' => 'date' ),
			'Adresse2.localite',
			'Adressefoyer3.dtemm' => array( 'type' => 'date' ),
			'Adresse3.localite',
		),
		array(
			'options' => $options
		)
	);
?>