<?php
	echo $this->element('default_index');

	echo $this->Default3->index(
		$personnes,
		$this->Translator->normalize(
			array(
				'Prestation.rolepers',
				'Personne.qual',
				'Personne.nom',
				'Personne.prenom',
				'Personne.dtnai',
				'Calculdroitrsa.toppersdrodevorsa',
			) + WebrsaAccess::links(
				array(
					'/Personnes/view/#Personne.id#',
					'/Personnes/edit/#Personne.id#',
					'/Personnes/filelink/#Personne.id#',
				)
			)
		),
		array(
			'options' => $options,
			'paginate' => false,
		)
	);