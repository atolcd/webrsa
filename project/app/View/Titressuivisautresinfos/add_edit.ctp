<?php

App::uses('WebrsaAccess', 'Utility');
WebrsaAccess::init($dossierMenu);

echo $this->element('default_index', array('addLink' => false));

//Visualisation du titre lié
if( empty( $titresCreanciers ) ) {
	echo '<p class="notice">'. __m('Titresuiviautreinfo::index::emptyTitrecreancier').'</p>';
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
					'options' => $options['Typetitrecreancier']['type_actif'],
				),
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

	// ******************* Partie Autres Infos ****************
	echo "<h2>". __m('Titresuiviautreinfo::index::titleTitreAutreInfo')."</h2>";

	echo $this->Xform->create( 'Titresuiviautreinfo', array( 'id' => 'TitresuiviautreinfoAddEditForm' ) );

	echo $this->Default3->subform(
		array(
			'Titresuiviautreinfo.id' => array( 'type' => 'hidden', 'value' => $titreAutreInfo['Titresuiviautreinfo']['id']),
			'Titresuiviautreinfo.titrecreancier_id' => array( 'type' => 'hidden', 'value' => $titresCreanciers['Titrecreancier']['id']),
			'Titresuiviautreinfo.typesautresinfos_id' => array(
				'type' => 'select',
				'required' => true,
				'empty' => false,
				'options' => $options['type'],
				'value' => $titreAutreInfo['Titresuiviautreinfo']['typesautresinfos_id']
			),
			'Titresuiviautreinfo.dtautreinfo' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Titresuiviautreinfo.commentaire' => array('type' => 'textarea', 'value' => $titreAutreInfo['Titresuiviautreinfo']['commentaire'])
		),
		array(
			'options' => $options
		)
	);
?>
<fieldset>
<legend>Pièce Jointe</legend>
<div style='display: none;'>
<?php echo $this->Form->input( 'Titresuiviautreinfo.haspiecejointe', array( 'type' => 'radio', 'options' => $options['Titresuiviautreinfo']['haspiecejointe'], 'legend' => false, 'fieldset' => false, 'value' => 1 ) );?>
</div>
<fieldset id="filecontainer-piece" class="noborder invisible">
	<?php
		echo $this->Fileuploader->create(
			isset($fichiers) ? $fichiers : array(),
			array( 'action' => 'ajaxfileupload' )
		);

		if (!isset ($fichiersEnBase)) {
			$fichiersEnBase = array ();
		}
		echo $this->Fileuploader->results(
			$fichiersEnBase
		);
	?>
</fieldset>
<?php echo $this->Fileuploader->validation( 'TitresuiviautreinfoAddEditForm', 'Titresuiviautreinfo', 'Pièce jointe' );?>
</fieldset>
<?php
	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
}