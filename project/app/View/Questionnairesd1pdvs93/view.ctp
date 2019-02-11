<?php
	echo $this->Default3->titleForLayout( $questionnaired1pdv93 );

	echo $this->Html->tag( 'h2', 'Réponses au questionnaire D1' );
	echo $this->Default3->view(
		$questionnaired1pdv93,
		array(
			'Situationallocataire.sexe',
			'Situationallocataire.nati',
//			'Situationallocataire.natpf_view',
			'Situationallocataire.natpf_d1',
			'Situationallocataire.tranche_age_view',
			'Situationallocataire.anciennete_dispositif_view',
			'Questionnaired1pdv93.inscritpe',
			'Questionnaired1pdv93.marche_travail',
			'Questionnaired1pdv93.vulnerable',
			'Questionnaired1pdv93.diplomes_etrangers',
			'Questionnaired1pdv93.categorie_sociopro',
			'Questionnaired1pdv93.nivetu',
			'Questionnaired1pdv93.autre_caracteristique',
			'Questionnaired1pdv93.autre_caracteristique_autre',
			'Questionnaired1pdv93.conditions_logement',
			'Questionnaired1pdv93.conditions_logement_autre',
			'Questionnaired1pdv93.date_validation',
		),
		array(
			'options' => $options
		)
	);

	echo $this->DefaultDefault->actions(
		$this->Default3->DefaultAction->back()
	);
?>