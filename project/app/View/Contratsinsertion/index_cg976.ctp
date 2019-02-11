<?php
	echo $this->element('default_index');

	echo $this->Default3->index(
		$contratsinsertion,
		$this->Translator->normalize(
			array(
				'Contratinsertion.date_saisi_ci',
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Contratinsertion.dd_ci',
				'Contratinsertion.df_ci',
				'Contratinsertion.duree_engag' => array( 'type' => 'text' ),
				'Contratinsertion.decision_ci',
				'Contratinsertion.datevalidation_ci',
			)
			+ WebrsaAccess::links(
				array(
					'/Contratsinsertion/view/#Contratinsertion.id#',
					'/Contratsinsertion/edit/#Contratinsertion.id#',
					'/Contratsinsertion/valider/#Contratinsertion.id#',
					'/Contratsinsertion/impression/#Contratinsertion.id#',
					'/Contratsinsertion/cancel/#Contratinsertion.id#',
					'/Contratsinsertion/delete/#Contratinsertion.id#' => array(
						'confirm' => true
					),
					'/Contratsinsertion/filelink/#Contratinsertion.id#',
				)
			)
		),
		array(
			'options' => $options,
			'paginate' => false,
			'innerTable' => $this->Translator->normalize(
				array(
					'Contratinsertion.motifannulation' => array(
						'condition' => "'#Contratinsertion.motifannulation#' !== ''"
					)
				)
			)
		)
	);