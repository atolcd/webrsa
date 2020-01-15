<?php

App::uses('WebrsaAccess', 'Utility');
WebrsaAccess::init($dossierMenu);

echo $this->element('default_index', array('addLink' => false));

echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate', 'id' => 'AddEditForm' ) );

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
				'Titrecreancier.typetitrecreancier_id' => array(
					'type' => 'select',
					'label' => __d('titrescreanciers', 'Titrecreancier.typetitrecreancier_id'),
					'options' => $options['Typetitrecreancier']['type_actif'], ),
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

	// ******************* Partie Informations payeurs ****************
	echo "<h2>". __m('Titresuiviinfopayeur::index::titleTitreInfoPayeur')."</h2>";

	echo $this->Default3->subform(
		array(
			'Titresuiviinfopayeur.id' => array( 'type' => 'hidden', 'value' => $titresInfosEnCours['Titresuiviinfopayeur']['id']),
			'Titresuiviinfopayeur.titrecreancier_id' => array( 'type' => 'hidden', 'value' => $titresCreanciers['Titrecreancier']['id']),
			'Titresuiviinfopayeur.typesinfopayeur_id' => array(
				'type' => 'select',
				'required' => true,
				'empty' => false,
				'options' => $options['Typetitrecreancierinfopayeur' ],
				'value' => $titresInfosEnCours['Titresuiviinfopayeur']['typesinfopayeur_id']
				),
			'Titresuiviinfopayeur.dtenvoipayeur' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Titresuiviinfopayeur.commentaire' => array('type' => 'textarea', 'value' => $titresInfosEnCours['Titresuiviinfopayeur']['commentaire'])
		),
		array(
			'options' => $options
		)
	);

	// ******************* Partie Email ****************
	echo $this->element( 'Email/edit' );

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit( 'AddEditForm' );

}