<?php
	echo $this->element('default_index');
	
	$decisionOrientation = array_keys((array)Configure::read('Commissionseps.defautinsertionep66.decision.type'));
	foreach ($bilansparcours66 as $key => $bilanparcours66) {
		/**
		 *  Proposition (uniquement pour thématique Parcours)
		 */
		if ($bilanparcours66['ParcoursPropositionTypeorient']['lib_type_orient']) {
			$bilansparcours66[$key]['Proposition']['lib'] = '<div class="largeColumn">'
					. $bilanparcours66['ParcoursPropositionTypeorient']['lib_type_orient']
				. '</div><div class="largeColumn">'
					. $bilanparcours66['ParcoursPropositionStructurereferente']['lib_struc']
				. '</div>';
		}
		
		/**
		 * Avis
		 */
		$avis = array();
		
		// Ajoute la décision supplémentaire à la décision si elle est disponnible
		if ($bilanparcours66['AuditionAvis']['decisionsup']) {
			$avis[] = value($options['Decisiondefautinsertionep66']['decision'], $bilanparcours66['AuditionAvis']['decisionsup']);
		}
		
		$avis[] = $bilanparcours66['Avis']['thematique'] === 'Parcours'
			? value($options['Decisionsaisinebilanparcoursep66']['decision'], $bilanparcours66['Avis']['decision'])
			: value($options['Decisiondefautinsertionep66']['decision'], $bilanparcours66['Avis']['decision']);
		
		// Dans le cas d'une réorientation, on ajoute l'orientation choisie
		if (in_array($bilanparcours66['Avis']['decision'], $decisionOrientation)) {
			$avis[] = $bilanparcours66['Avis']['lib_type_orient'];
			$avis[] = $bilanparcours66['Avis']['lib_struc'];
		}
		
		$bilansparcours66[$key]['Avis']['lib'] = !empty($avis[0]) 
			? '<div class="largeColumn">'.implode('</div><div class="largeColumn">', $avis).'</div>' 
			: '';
		
		/**
		 * Decision
		 */
		$decision = array();
		
		if ($bilanparcours66['Avis']['thematique'] === 'Parcours') {
			$decision[] = value($options['Decisionsaisinebilanparcoursep66']['decision'], $bilanparcours66['Decision']['decision']);
		}
		
		// Dans le cas d'une réorientation, on ajoute l'orientation choisie
		if (in_array($bilanparcours66['Decision']['decision'], $decisionOrientation)) {
			$decision[] = value($options['Decisiondefautinsertionep66']['decision'], $bilanparcours66['Decision']['decision']);
			$decision[] = $bilanparcours66['Decision']['lib_type_orient'];
			$decision[] = $bilanparcours66['Decision']['lib_struc'];
		}
		
		// Si le Dossier PCG est transmis, on ajoute l'information
		if ($bilanparcours66['Decision']['decision']
			&& $bilanparcours66['Decisionpdo']['libelle']
			&& $bilanparcours66['Dossierpcg66']['etatdossierpcg'] === 'transmisop'
		) {
			$decision[] = 'CGA : ' . $bilanparcours66['Decisionpdo']['libelle'];
		}
		
		$bilansparcours66[$key]['Decision']['lib'] = !empty($decision[0]) 
			? '<div class="largeColumn">'.implode('</div><div class="largeColumn">', $decision).'</div>' 
			: '';
	}
	
	echo $this->Default3->index(
		$bilansparcours66,
		$this->Translator->normalize(
			array(
				'Bilanparcours66.datebilan',
				'Bilanparcours66.positionbilan',
				'Serviceinstructeur.lib_service',
				'Structurereferente.lib_struc',
				'Referent.nom_complet',
				'Bilanparcours66.proposition',
				
				// Motif de la saisine
				'Bilanparcours66.examenauditionpe' => array(
					'condition_group' => 'exam',
					'condition' => "'#Bilanparcours66.examenauditionpe#' !== ''",
					'label' => __m('Bilanparcours66.examen'),
				),
				'Bilanparcours66.examenaudition' => array(
					'condition_group' => 'exam',
					'condition' => "'#Bilanparcours66.examenauditionpe#' === '' && '#Bilanparcours66.examenaudition#' !== ''"
				),
				'Bilanparcours66.choixparcours' => array(
					'condition_group' => 'exam',
					'condition' => "'#Bilanparcours66.examenauditionpe#' === '' && '#Bilanparcours66.examenaudition#' === ''"
				),
				'Proposition.lib',
				'Avis.lib',
				'Decision.lib',
			) + WebrsaAccess::links(
				array(
					'/Bilansparcours66/view/#Bilanparcours66.id#',
					'/Bilansparcours66/edit/#Bilanparcours66.id#',
					'/Bilansparcours66/impression/#Bilanparcours66.id#',
					'/Manifestationsbilansparcours66/index/#Bilanparcours66.id#',
					'/Bilansparcours66/impression_fichedeliaison/#Bilanparcours66.id#' => array (
						'hidden' => !Configure::read('Bilanparcours66.Fichesynthese.Impression'),
						'class' => 'impression'
					),
					'/Bilansparcours66/cancel/#Bilanparcours66.id#',
					'/Bilansparcours66/filelink/#Bilanparcours66.id#',
				)
			)
		),
		array(
			'options' => $options,
			'paginate' => false,
			'empty_label' => __m('Bilansparcours66::index::emptyLabel'),
			'innerTable' => $this->Translator->normalize(
				array(
					'Bilanparcours66.motifannulation' => array(
						'condition' => "in_array('#Bilanparcours66.positionbilan#', array('annule', 'ajourne'))"
					),
					'Avis.commentaire' => array(
						'condition' => "(in_array('#Bilanparcours66.positionbilan#', array('annule', 'ajourne', 'traite')) "
						. "|| in_array('#Avis.decision#', array('reporte', 'annule'))) "
						. "&& '#Avis.havecommentaire#' === '1'"
					),
					'Decision.commentaire' => array(
						'condition' => "(in_array('#Bilanparcours66.positionbilan#', array('annule', 'ajourne', 'traite')) "
						. "|| in_array('#Decision.decision#', array('reporte', 'annule'))) "
						. "&& '#Decision.havecommentaire#' === '1' "
						. "&& '#Decision.commentaire_is_equal#' !== '1'"
					),
				)
			),
		)
	);