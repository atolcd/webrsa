<?php
	$addLink = '/Proposcontratsinsertioncovs58/add/'.$personne_id;

	echo $this->element('default_index', array( 'addLink' => $addLink ));

	$defaultParams = array('paginate' => false, 'options' => $options);

	// Valeurs avec concatenation
	foreach ($contratsinsertion as $key => $data) {
		if (Hash::get($data, 'Cov58.datecommission')) {
			$contratsinsertion[$key]['Cov58']['infocov'] = 'Site '.Hash::get($data, 'Sitecov58.name').'", le '
				.$this->Locale->date("Datetime::full", Hash::get($data, 'Cov58.datecommission'))
			;
		}
	}

	if (!empty($sanctionseps58)) {
		echo '<h2>Signalements pour non respect du contrat</h2>';
		echo $this->Default3->index(
			$sanctionseps58,
			$this->Translator->normalize(
				array(
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Sanctionep58.created',
					'Passagecommissionep.etatdossierep',
				) + WebrsaAccess::links(
					array(
						'/Sanctionseps58/deleteNonrespectcer/#Sanctionep58.id#' => array(
							'confirm' => 'Confirmer la suppession ?',
							'class' => 'delete'
						),
					)
				)
			),
			$defaultParams
		);
		echo '<br/>';
	}

	if (Hash::get($propocontratinsertioncov58, '0.Propocontratinsertioncov58.id')) {
		echo '<h2>Contrat en cours de validation par la commission d\'orientation et de validation</h2>';
		echo $this->Default3->index(
			$propocontratinsertioncov58,
			$this->Translator->normalize(
					array(
					'Personne.nom',
					'Personne.prenom',
					'Propocontratinsertioncov58.dd_ci',
					'Propocontratinsertioncov58.df_ci',
					'Propocontratinsertioncov58.avenant_id' => array(
						'label' => __d( 'contratinsertion', 'Contratinsertion.num_contrat'),
						'condition' => "'#Propocontratinsertioncov58.avenant_id#' !== ''",
						'value' => 'Avenant'
					),
					'Propocontratinsertioncov58.num_contrat' => array(
						'label' => __d( 'contratinsertion', 'Contratinsertion.num_contrat'),
						'condition' => "'#Propocontratinsertioncov58.avenant_id#' === ''"
					),
					'Passagecov58.etatdossiercov',
				) + WebrsaAccess::links(
					array(
						'/Proposcontratsinsertioncovs58/edit/#Personne.id#',
						'/Proposcontratsinsertioncovs58/delete/#Propocontratinsertioncov58.id#' => array(
							'confirm' => true
						),
					)
				)
			),
			$defaultParams + array(
				'paginate' => false,
			)
		);
		echo '<br/>';
	}

	echo '<h2>Contrats effectifs</h2>';
	echo $this->Default3->index(
		$contratsinsertion,
		$this->Translator->normalize(
				array(
				'Contratinsertion.num_contrat',
				'Contratinsertion.dd_ci',
				'Contratinsertion.df_ci',
				'Contratinsertion.decision_ci',
				'Contratinsertion.datevalidation_ci',
				'Cov58.infocov', // Calculé
				'Decisionpropocontratinsertioncov58.commentaire',
			)
			+ WebrsaAccess::links(
				array(
					'/Contratsinsertion/view/#Contratinsertion.id#',
					'/Contratsinsertion/edit/#Contratinsertion.id#',
					'/Contratsinsertion/impression/#Contratinsertion.id#',
					'/Contratsinsertion/delete/#Contratinsertion.id#' => array(
						'confirm' => true
					),
					'/Sanctionseps58/nonrespectcer/#Contratinsertion.id#',
					'/Proposcontratsinsertioncovs58/add/#Contratinsertion.personne_id#/#Contratinsertion.id#' => array(
						'msgid' => 'Avenant',
						'title' => 'Avenant'
					),
					'/Contratsinsertion/filelink/#Contratinsertion.id#',
				)
			)
		),
		$defaultParams
	);