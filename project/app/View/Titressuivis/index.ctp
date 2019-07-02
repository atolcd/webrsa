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
	$titreEnCours = array();
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
	echo "<h2>". __m('Titressuivisannulationsreductions::index::titleTitresuivi')."</h2>";

	// Activation du bouton Ajouter
	echo $this->Default3->actions(array(
		'/Titressuivisannulationsreductions/add/' . $titresCreanciers['Titrecreancier']['id'] => array( 'disabled' => $options['annreduc_ajoutDisabled'] )
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
						'/Titressuivisannulationsreductions/view/#Titresuiviannulationreduction.id#/#Titresuiviannulationreduction.titrecreancier_id#/',
						'/Titressuivisannulationsreductions/edit/#Titresuiviannulationreduction.id#/#Titresuiviannulationreduction.titrecreancier_id#/',
						'/Titressuivisannulationsreductions/cancel/#Titresuiviannulationreduction.id#/#Titresuiviannulationreduction.titrecreancier_id#/' => array('confirm' => true),
						'/Titressuivisannulationsreductions/delete/#Titresuiviannulationreduction.id#/#Titresuiviannulationreduction.titrecreancier_id#/' => array('confirm' => true),
						'/Titressuivisannulationsreductions/impression/#Titresuiviannulationreduction.id#/#Titresuiviannulationreduction.titrecreancier_id#/',
						'/Titressuivisannulationsreductions/filelink/#Titresuiviannulationreduction.id#/#Titresuiviannulationreduction.titrecreancier_id#/',
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
	echo "<h2>". __m('Titresuiviinfopayeur::index::titleTitreInfoPayeur')."</h2>";

	// Activation du bouton Ajouter
	echo $this->Default3->actions(array(
		'/Titressuivisinfospayeurs/add/' . $titresCreanciers['Titrecreancier']['id'] => array( 'disabled' => false )
	));

	//Visualisation des informations payeurs
	if( empty( $titresInfosPayeurs ) ) {
		echo '<p class="notice">'. __m('Titressuivisinfospayeurs::index::emptyTitresuivi').'</p>';
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
						'/Titressuivisinfospayeurs/view/#Titresuiviinfopayeur.id#/#Titresuiviinfopayeur.titrecreancier_id#/',
						'/Titressuivisinfospayeurs/edit/#Titresuiviinfopayeur.id#/#Titresuiviinfopayeur.titrecreancier_id#/',
						'/Titressuivisinfospayeurs/answer/#Titresuiviinfopayeur.id#/#Titresuiviinfopayeur.titrecreancier_id#/' => array('class' => 'button add'),
						'/Titressuivisinfospayeurs/delete/#Titresuiviinfopayeur.id#/#Titresuiviinfopayeur.titrecreancier_id#/' => array('confirm' => true),
					)
				)
			),
			array(
				'paginate' => false,
			)
		);
	}

	echo '<br>';

	// ******************* Partie Autres infos ****************
	echo '<h2>' . __m('Titressuivisautresinfo::index:titleTitresuivi') . '</h2>';

	// Activation du bouton Ajouter
	echo $this->Default3->actions(array(
		'/Titressuivisautresinfos/add/' . $titresCreanciers['Titrecreancier']['id'] => array( 'disabled' => false )
	));

	//Visualisation des autres informations
	if( empty( $titresAutres ) ) {
		echo '<p class="notice">' . __m('Titressuivisautresinfo::index::emptyTitresuivi') . '</p>';
	}else{
		echo $this->Default3->index(
			$titresAutres,
			$this->Translator->normalize(
				array(
					'Typetitrecreancierautreinfo.nom',
					'Titresuiviautreinfo.dtautreinfo' => array('type' => 'date', 'dateFormat' => 'DMY'),
					'Titresuiviautreinfo.commentaire',
				)+ WebrsaAccess::links(
					array(
						'/Titressuivisautresinfos/view/#Titresuiviautreinfo.id#/#Titresuiviautreinfo.titrecreancier_id#/',
						'/Titressuivisautresinfos/edit/#Titresuiviautreinfo.id#/#Titresuiviautreinfo.titrecreancier_id#/',
						'/Titressuivisautresinfos/cancel/#Titresuiviautreinfo.id#/#Titresuiviautreinfo.titrecreancier_id#/' => array('confirm' => true),
						'/Titressuivisautresinfos/delete/#Titresuiviautreinfo.id#/#Titresuiviautreinfo.titrecreancier_id#/' => array('confirm' => true),
						'/Titressuivisautresinfos/filelink/#Titresuiviautreinfo.id#/#Titresuiviautreinfo.titrecreancier_id#/',
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