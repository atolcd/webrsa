<?php
	echo $this->element('default_index');
	
	echo $this->Default3->index(
		$contratsinsertion,
		$this->Translator->normalize(
			array(
				'Contratinsertion.forme_ci',
				'Contratinsertion.num_contrat_66',
				'Contratinsertion.dd_ci',
				'Contratinsertion.df_ci',
				'Contratinsertion.date_saisi_ci',
				'Contratinsertion.decision_ci',
				'Contratinsertion.datedecision',
				'Contratinsertion.positioncer',
			)
			+ WebrsaAccess::links(
				array(
					'/Contratsinsertion/view/#Contratinsertion.id#',
					'/Contratsinsertion/edit/#Contratinsertion.id#',
					'/Proposdecisionscers66/propositionsimple/#Contratinsertion.id#' => array(
						'condition' => "'#Contratinsertion.forme_ci#' === 'S'",
						'class' => 'button valider'
					),
					'/Proposdecisionscers66/propositionparticulier/#Contratinsertion.id#' => array(
						'condition' => "'#Contratinsertion.forme_ci#' !== 'S'",
						'class' => 'button valider'
					),
					'/Contratsinsertion/ficheliaisoncer/#Contratinsertion.id#',
					'/Contratsinsertion/notifbenef/#Contratinsertion.id#',
					'/Contratsinsertion/notificationsop/#Contratinsertion.id#' => array(
						'class' => 'button notifop'
					),
					'/Contratsinsertion/impression/#Contratinsertion.id#',
					'/Contratsinsertion/notification/#Contratinsertion.id#',
					'/Contratsinsertion/reconduction_cer_plus_55_ans/#Contratinsertion.id#' => array(
						'class' => 'button reconduction'
					),
					'/Contratsinsertion/cancel/#Contratinsertion.id#',
					'/Contratsinsertion/filelink/#Contratinsertion.id#'
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
					),
					'Contratinsertion.duree_engag',
				)
			)
		)
	);