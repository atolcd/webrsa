<?php
	echo $this->element('default_index');
	
	// Ajout des dates sur certaines positions du CUI
	foreach( $results as $key => $value ){
		$etat = $value['Cui66']['etatdossiercui66'];
		$insert = '';
		if ( in_array( $etat, array( 'contratsuspendu', 'rupturecontrat', 'dossierrelance' ) ) ){
			switch ( $etat ){
				case 'contratsuspendu': $insert = new DateTime($value['Suspensioncui66']['datefin']); break;
				case 'rupturecontrat': $insert = new DateTime($value['Rupturecui66']['daterupture']); break;
				case 'dossierrelance': $insert = new DateTime($value['Emailcui']['dateenvoi']); break;
				default: $insert = '';
			}
			$insert = date_format($insert, 'd/m/Y');
		}
		$results[$key]['Cui66']['positioncui66'] = sprintf( __d('cui66', 'ENUM::ETATDOSSIERCUI66::' . $etat  ), $insert );
	}
	
	echo $this->Default3->index(
		$results,
		$this->Translator->normalize(
			array(
				'Cui.faitle',
				'Cui66.positioncui66',
				'Historiquepositioncui66.created',
				'Cui66.positioncui66',
				'Cui.secteurmarchand' => array( 'type' => 'select' ),
				'Partenairecui.raisonsociale',
				'Cui.effetpriseencharge',
				'Cui.finpriseencharge',
				'Decisioncui66.decision',
				'Decisioncui66.datedecision',
				'Cui66.notifie' => array( 'type' => 'select' ),
				'Cui66.raisonannulation',
			) + WebrsaAccess::links(
				array(
					'/Cuis/view/#Cui.id#',
					'/Cuis/edit/#Cui.id#',
					'/Cuis66/impression_fichedeliaison/#Cui.id#' => array('class' => 'impression'),
					'/Cuis66/impression/#Cui.id#',
					'/Cuis66/email/#Cui.personne_id#/#Cui.id#',
					'/Propositionscuis66/index/#Cui.id#' => array('class' => 'proposition'),
					'/Decisionscuis66/index/#Cui.id#' => array('class' => 'valider'),
					'/Cuis66/notification/#Cui66.id#' => array('class' => 'alert'),
					'/Accompagnementscuis66/index/#Cui.id#' => array('class' => 'accompagnement'),
					'/Suspensionscuis66/index/#Cui.id#' => array('class' => 'suspension'),
					'/Rupturescuis66/index/#Cui.id#' => array('class' => 'rupture'),
					'/Cuis66/annule/#Cui66.id#' => array('class' => 'cancel'),
					'/Cuis66/delete/#Cui.id#' => array('confirm' => true),
					'/Cuis66/filelink/#Cui.id#',
				)
			)
		),
		array(
			'options' => $options,
			'paginate' => false,
		)
	);
	