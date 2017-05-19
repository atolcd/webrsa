<?php
	echo $this->element('default_index');
	
	echo $this->Default3->index(
		$personnes_referents,
		$this->Translator->normalize(
			array(
				'Referent.nom_complet',
				'Referent.fonction',
				'Referent.numero_poste',
				'Referent.email',
				'Structurereferente.lib_struc',
				'PersonneReferent.dddesignation',
				'PersonneReferent.dfdesignation',
			) + WebrsaAccess::links(
				array(
					'/PersonnesReferents/edit/#PersonneReferent.id#',
					'/PersonnesReferents/cloturer/#PersonneReferent.id#',
					'/PersonnesReferents/filelink/#PersonneReferent.id#',
				)
			)
		),
		array(
			'paginate' => false,
			'empty_label' => __m('PersonnesReferents::index::emptyLabel'),
		)
	);