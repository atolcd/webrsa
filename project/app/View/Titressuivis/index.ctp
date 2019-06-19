<?php
	$activateTitreCreancier = Configure::read( 'Creances.titrescreanciers' );
	if ( !empty($activateTitreCreancier)	&& $activateTitreCreancier == true ){
		$activateTitreCreancier = true;
	}else{
		$activateTitreCreancier = false;
	}

App::uses('WebrsaAccess', 'Utility');
WebrsaAccess::init($dossierMenu);

echo $this->element('default_index', array('addLink' => false));

$backUrl = '/titrescreanciers/index/'.$titresCreanciers['Titrecreancier']['creance_id'];
echo $this->Default3->actions(array( $backUrl => array( 'class' => 'back' )));

//Visualisation des titres
if( empty( $titresCreanciers ) ) {
	echo '<p class="notice">'. __m('Titressuivisannulationsreductions::index::emptyTitrecreancier').'</p>';
}else{
	$titreEnCours[0] = $titresCreanciers;
	echo $this->Default3->index(
		$titreEnCours,
		$this->Translator->normalize(
			array(
				'Titrecreancier.dtemissiontitre' => array('label' => __d('titrescreanciers', 'Titrecreancier.dtemissiontitre') ),
				'Titrecreancier.numtitr' => array('label' => __d('titrescreanciers', 'Titrecreancier.numtitr') ),
				'Titrecreancier.mnttitr' => array('label' => __d('titrescreanciers', 'Titrecreancier.mnttitr') ),
				'Titrecreancier.type' => array('label' => __d('titrescreanciers', 'Titrecreancier.type') ),
				'Titrecreancier.dtvalidation' => array('label' => __d('titrescreanciers', 'Titrecreancier.dtvalidation') ),
				'Titrecreancier.etat' => array('label' => __d('titrecreancier', 'Titrecreancier.etat') ),
				'Titrecreancier.mention' => array('label' => __d('titrescreanciers', 'Titrecreancier.mention') ),
				'Titrecreancier.qual' => array('label' => __d('titrescreanciers', 'Titrecreancier.qual') ),
				'Titrecreancier.nom' => array('label' => __d('titrescreanciers', 'Titrecreancier.nom') ),
				'Titrecreancier.numtel' => array('label' => __d('titrescreanciers', 'Titrecreancier.numtel') ),
			)
		),
		array(
			'paginate' => false,
			'empty_label' => __m('Titrecreancier::index::emptyLabel'),
		)
	);
	echo '<br>';

	// ******************* Partie Annulation / réduction ****************
	echo "<h2>". __m('Titressuivisannulationsreductions::index::titleTitresuivit')."</h2>";
	
	// Activation du bouton Ajouter
	echo $this->Default3->actions(array(
		'/Titressuivisannulationsreductions/add/' . $titresCreanciers[0]['Titrecreancier']['id'] => array( 'disabled' => $options['annreduc_ajoutDisabled'] )
	));

	//Visualisation des annulations / réductions
	if( empty( $titresAnnRed ) ) {
		echo '<p class="notice">'. __m('Titressuivisannulationsreductions::index::emptyTitresuivit').'</p>';
	}else{
		echo $this->Default3->index(
			$titresAnnRed,
			$this->Translator->normalize(
				array(
					'Typetitrecreancierannulationreduction.nom',
					'Titresuiviannulationreduction.dtaction' => array('type' => 'date', 'dateFormat' => 'DMY'),
					'Titresuiviannulationreduction.mtreduit',
					'Titresuiviannulationreduction.mtavantacte',
					'Titresuiviannulationreduction.mtapresacte'
				)+ WebrsaAccess::links(
					array(
						'/Titressuivisannulationsreductions/view/#Titresuiviannulationreduction.id#/#Titresuiviannulationreduction.titrescreanciers_id#/',
						'/Titressuivisannulationsreductions/edit/#Titresuiviannulationreduction.id#/#Titresuiviannulationreduction.titrescreanciers_id#/',
						'/Titressuivisannulationsreductions/cancel/#Titresuiviannulationreduction.id#/#Titresuiviannulationreduction.titrescreanciers_id#/' => array('confirm' => true),
						'/Titressuivisannulationsreductions/delete/#Titresuiviannulationreduction.id#/#Titresuiviannulationreduction.titrescreanciers_id#/' => array('confirm' => true),
						'/Titressuivisannulationsreductions/print/#Titresuiviannulationreduction.id#/#Titresuiviannulationreduction.titrescreanciers_id#/',
						'/Titressuivisannulationsreductions/filelink/#Titresuiviannulationreduction.id#/#Titresuiviannulationreduction.titrescreanciers_id#/',
					)
				)
			),
			array(
				'paginate' => false,
			)
		);
	}
	echo '<br>';

	// ******************* Partie Infos payeurs****************
	echo "<h2>". __m('Titressuivisannulationsreductions::index::titleTitresuivi')."</h2>";

	// Activation du bouton Ajouter
	echo $this->Default3->actions(array(
		'/Titressuivisinfospayeurs/add/' . $titresCreanciers[0]['Titrecreancier']['id'] => array( 'disabled' => false )
	));

	//Visualisation des informations payeurs
	if( empty( $titresInfosPayeurs ) ) {
		echo '<p class="notice">'. __m('Titressuivisannulationsreductions::index::emptyTitresuivit').'</p>';
	}else{
		echo $this->Default3->index(
			$titresInfosPayeurs,
			$this->Translator->normalize(
				array(
					'Typetitrecreancierinfopayeur.nom',
					'Titresuiviinfopayeur.commentaire',
					'Titresuiviinfopayeur.dtenvoipayeur' => array('type' => 'date', 'dateFormat' => 'DMY'),
					'Titresuiviinfopayeur.retourpayeur',
				)+ WebrsaAccess::links(
					array(
						'/Titressuivisinfospayeurs/view/#Titresuiviinfopayeur.id#/#Titresuiviinfopayeur.titrescreanciers_id#/',
						'/Titressuivisinfospayeurs/edit/#Titresuiviinfopayeur.id#/#Titresuiviinfopayeur.titrescreanciers_id#/',
						'/Titressuivisinfospayeurs/answer/#Titresuiviinfopayeur.id#/#Titresuiviinfopayeur.titrescreanciers_id#/' => array('class' => 'button add'),
						'/Titressuivisinfospayeurs/delete/#Titresuiviinfopayeur.id#/#Titresuiviinfopayeur.titrescreanciers_id#/' => array('confirm' => true),
					)
				)
			),
			array(
				'paginate' => false,
			)
		);
	}

	echo $this->Default3->actions(array( $backUrl => array( 'class' => 'back' )));
}
