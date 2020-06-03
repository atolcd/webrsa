<?php
	echo $this->Default3->csv(
		$results,
		array(
			'Dossier.matricule', //'Numero CAF/MSA',
			'Personne.nom_complet', //'Nom / Prénom du demandeur',
			'Personne.dtnai', //'Date de naissance du demandeur',
			'Adresse.numvoie', //'Adresse'
			'Adresse.libtypevoie',
			'Adresse.nomvoie',
			'Adresse.complideadr',
			'Adresse.compladr',
			'Adresse.codepos',
			'Adresse.nomcom',
			'Conjoint.nom_complet', //'Nom / Prénom du conjoint',
			'Dossier.dtdemrsa', //'Date ouverture de droits',
			'Referentorientant.nom_complet',// TODO //'Ref. charge de l\'evaluation',
			'Structurereferenteparcours.lib_struc', // Type de structure
			'Orientstruct.date_valid', //'Date orientation (COV)',
			'Orientstruct.rgorient', //'Rang orientation (COV)',
			'Referentparcours.nom_complet', //'Referent unique',
			'Typocontrat.lib_typo', // Type du contrat
			'Contratinsertion.dd_ci', //'Date debut (CER)',
			'Contratinsertion.df_ci', //'Date fin (CER)',
			'Contratinsertion.rg_ci', //'Rang (CER)',
			'Contratinsertion.positioncer', // Position du contrat
			'Historiqueetatpe.etat', //'Dernier état Pole Emploi',
			'Historiqueetatpe.date', //'Date inscription Pole Emploi',
			'Commissionep.dateseance', //'Date (EP)',
			'Dossierep.themeep' //'Motif (EP)',
		),
		array(
			'options' => $options
		)
	);
?>