<?php
App::uses('WebrsaAccess', 'Utility');
WebrsaAccess::init($dossierMenu);

echo $this->element('default_index', array('addLink' => false));

echo '<br>';
echo "<h2>". __m('Titresuiviinfopayeur::index::titleTitreRecette')."</h2>";
echo $this->Default3->view(
		$titresCreanciers,
		$this->Translator->normalize(
			array(
                'Titrecreancier.dtemissiontitre' => array('label' => __d('titrescreanciers', 'Titrecreancier.dtemissiontitre')),
                'Titrecreancier.qual' => array('label' => __d('titrescreanciers', 'Titrecreancier.qual') ),
				'Titrecreancier.nom' => array('label' => __d('titrescreanciers', 'Titrecreancier.nom') ),
				'Titrecreancier.mntinit' => array('label' => __d('titrescreanciers', 'Titrecreancier.mntinit') ),
				'Titrecreancier.etat' => array('label' => __d('titrecreancier', 'Titrecreancier.etat') ),
			)
		)
	);
echo '<br>';
echo "<h2>". __m('Titresuiviinfopayeur::index::titleTitreInfoPayeur')."</h2>";
echo $this->Default3->view(
    $titresInfosEnCours,
    array(
        'Titresuiviinfopayeur.dtenvoipayeur' => array('type' => 'date', 'dateFormat' => 'DMY'),
        'Typetitrecreancierinfopayeur.nom',
        'Titresuiviinfopayeur.commentaire'
    )
);

echo '<br>';
echo "<h2>". __m('Titresuiviinfopayeur::index::titleRetourPayeur')."</h2>";

if($titresInfosEnCours['Titresuiviinfopayeur']['retourpayeur'] !== 'null') {
    echo $this->Default3->view(
        $titresInfosEnCours,
        array(
            'Titresuiviinfopayeur.retourpayeur'
        )
    );
} else {
	echo '<p class="notice">'. __m('Titresuiviinfopayeur::index::emptyRetourPayeur').'</p>';
}


$backUrl = '/titressuivis/index/' . $titresInfosEnCours['Titresuiviinfopayeur']['titrescreanciers_id'];
echo $this->Default3->actions(array( $backUrl => array( 'class' => 'back' )));
