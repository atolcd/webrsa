<?php

App::uses('WebrsaAccess', 'Utility');
WebrsaAccess::init($dossierMenu);

echo $this->element('default_index', array('addLink' => false));

//Visualisation du titre lié
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
				'Titrecreancier.mnttitr' => array('label' => __d('titrescreanciers', 'Titrecreancier.mnttitr'), 'id' => 'mnttitr' ),
				'Titrecreancier.typetitrecreancier_id' => array(
					'label' => __d('titrescreanciers', 'Titrecreancier.typetitrecreancier_id'),
					'type' => 'select',
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

	// ******************* Partie Annulation / réduction ****************
	echo "<h2>". __m('Titressuivisannulationsreductions::index::titleAnnulation')."</h2>";

	echo $this->Xform->create( 'Titresuiviannulationreduction', array( 'id' => 'TitresuiviannulationreductionAddEditForm' ) );

	echo $this->Default3->subform(
		array(
			'Titresuiviannulationreduction.id' => array( 'type' => 'hidden', 'value' => $titresAnnRedEnCours['Titresuiviannulationreduction']['id']),
			'Titresuiviannulationreduction.titrecreancier_id' => array( 'type' => 'hidden', 'value' => $titresCreanciers['Titrecreancier']['id']),
			'Titresuiviannulationreduction.mtactuel' => array(
				'type' => 'text',
				'disabled' => 'true',
				'value' => $options['montant']['total'],
				'style' => 'background-color: #F0F0F0',
			),
			'Titresuiviannulationreduction.dtaction' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Titresuiviannulationreduction.typeannulationreduction_id' => array(
				'type' => 'select',
				'required' => true,
				'empty' => false,
				'options' => $options['type'],
				'id' => 'typeAnnReduc',
				'onchange' => 'changeMontantAccess()',
				'value' => $titresAnnRedEnCours['Titresuiviannulationreduction']['typeannulationreduction_id']
				),
			'Titresuiviannulationreduction.mtreduit' => array(
				'type' => 'text',
				'required' => true,
				'id' => 'mtreduit',
				'style' => 'background-color: #F0F0F0',
				'disabled' => $options['montant']['disabled'],
				'value' => $titresAnnRedEnCours['Titresuiviannulationreduction']['mtreduit']),
			'Titresuiviannulationreduction.commentaire' => array('type' => 'textarea', 'value' => $titresAnnRedEnCours['Titresuiviannulationreduction']['commentaire'])
		),
		array(
			'options' => $options
		)
	);
?>
<fieldset>
<legend> <?php echo __m('Titressuivisannulationsreductions::index::legendePJ'); ?> </legend>
<div style='display: none;'>
<?php echo $this->Form->input( 'Titresuiviannulationreduction.haspiecejointe', array( 'type' => 'radio', 'options' => $options['Titresuiviannulationreduction']['haspiecejointe'], 'legend' => false, 'fieldset' => false, 'value' => 1 ) );?>
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
<?php echo $this->Fileuploader->validation( 'TitresuiviannulationreductionAddEditForm', 'Titresuiviannulationreduction', __m('Titressuivisannulationsreductions::index::legendePJ') );?>
</fieldset>
<?php
	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
}
?>
<script>
	changeMontantAccess();

	function changeMontantAccess(){
		var type = document.getElementById('typeAnnReduc');
		var choix = type.options[type.selectedIndex].text;
		var montant = document.getElementById('mtreduit');
		var valMontant = document.getElementById('mnttitr').innerHTML;

		if(choix === 'annulation'){
			montant.disabled = true;
			montant.style.background = "#F0F0F0";
			montant.value = valMontant;
		}else{
			montant.disabled = false;
			montant.style.background = 'white';
		}
	}
</script>