<?php

App::uses('WebrsaAccess', 'Utility');
WebrsaAccess::init($dossierMenu);

echo $this->element('default_index', array('addLink' => false));

//Visualisation du titre lié
if( empty( $titresCreanciers ) ) {
	echo '<p class="notice">'. __m('Titresuiviinfopayeur::index::emptyTitrecreancier').'</p>';
}else{
	$titreEnCours[0] = $titresCreanciers;
	echo $this->Default3->index(
		$titreEnCours,
		$this->Translator->normalize(
			array(
				'Titrecreancier.dtemissiontitre' => array('label' => __d('titrescreanciers', 'Titrecreancier.dtemissiontitre') ),
				'Titrecreancier.numtitr' => array('label' => __d('titrescreanciers', 'Titrecreancier.numtitr') ),
				'Titrecreancier.mnttitr' => array('label' => __d('titrescreanciers', 'Titrecreancier.mnttitr') ),
				'Titrecreancier.type' => array( 'type' => 'select', 'label' => __d('titrescreanciers', 'Titrecreancier.type') ),
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
			'options' => $options,
			'empty_label' => __m('Titrecreancier::index::emptyLabel'),
		)
	);
	echo '<br>';

	// ******************* Partie Retours payeurs ****************
	echo $this->Default3->form(
		array(
			'Titresuiviinfopayeur.id' => array( 'type' => 'hidden', 'value' => $titresInfosEnCours['Titresuiviinfopayeur']['id']),
			'Titresuiviinfopayeur.titrecreancier_id' => array( 'type' => 'hidden', 'value' => $titresCreanciers['Titrecreancier']['id']),
			'Titresuiviinfopayeur.retourpayeur' => array('type' => 'textarea', 'value' => $titresInfosEnCours['Titresuiviinfopayeur']['retourpayeur'])
		),
		array(
			'options' => $options
		)
	);
}
