<h1><?php echo $this->pageTitle = 'Liste des dossiers d\'EP';?></h1>

<?php
	echo $this->Default->index(
		$dossierseps,
		array(
			'Personne.qual',
			'Personne.nom',
			'Personne.prenom',
			'Commissionep.dateseance',
			'Dossierep.created',
			'Dossierep.themeep',
			'Dossierep.etapedossierep'
		),
		array(
			'options' => $options
		)
	);
?>