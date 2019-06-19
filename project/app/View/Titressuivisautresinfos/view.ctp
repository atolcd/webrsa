<?php
App::uses('WebrsaAccess', 'Utility');
WebrsaAccess::init($dossierMenu);

echo $this->element('default_index', array('addLink' => false));

echo '<br>';
echo "<h2>". __m('Titresuiviautreinfo::index::titleTitreRecette')."</h2>";
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
echo "<h2>". __m('Titresuiviautreinfo::index::titleTitreAutreInfo')."</h2>";
echo $this->Default3->view(
    $titreAutreInfo,
    array(
        'Typetitrecreancierautreinfo.nom',
        'Titresuiviautreinfo.dtautreinfo' => array('type' => 'date', 'dateFormat' => 'DMY'),
        'Titresuiviautreinfo.commentaire'
    )
);

echo '<br>';
echo "<h2> ". __m('Titresuiviautreinfo::index::titleFileupload')."</h2>";

echo $this->Fileuploader->results(Set::classicExtract($titreAutreInfo, 'Fichiermodule'));

$backUrl = '/titressuivis/index/' . $titreAutreInfo['Titresuiviautreinfo']['titrecreancier_id'];
echo $this->Default3->actions(array( $backUrl => array( 'class' => 'back' )));
