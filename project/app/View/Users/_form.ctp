<?php
	$departement = Configure::read( 'Cg.departement' );

	echo $this->Form->input( 'User.filtre_zone_geo', array( 'type' => 'hidden', 'value' => '', 'id' => false ) );
	echo $this->Form->input( 'User.communautesr_id', array( 'type' => 'hidden', 'value' => '', 'id' => false ) );
	echo $this->Form->input( 'User.structurereferente_id', array( 'type' => 'hidden', 'value' => '', 'id' => false ) );
	echo $this->Form->input( 'User.referent_id', array( 'type' => 'hidden', 'value' => '', 'id' => false ) );
	echo $this->Form->input( 'User.type', array( 'type' => 'hidden', 'value' => 'cg', 'id' => false ) );
?>
<fieldset>
	<?php echo $this->Form->input( 'User.nom', array( 'label' =>  required( __d( 'personne', 'Personne.nom' ) ), 'type' => 'text' ) );?>
	<?php echo $this->Form->input( 'User.prenom', array( 'label' =>  required( __d( 'personne', 'Personne.prenom' ) ), 'type' => 'text' ) );?>
	<?php echo $this->Form->input( 'User.username', array( 'label' =>  required( __( 'username' ) ), 'type' => 'text' ) );?>
	<?php echo $this->Form->input( 'User.passwd', array( 'label' =>  required( __( 'password' ) ), 'type' => 'password', 'value' => '' ) );?>
	<?php

		echo $this->Form->input( __m('User.date_password'), array( 'type' => 'text', 'disabled' => 'disabled', 'value' => $dateExpiration ) );
		echo $this->Form->input( __m('User.nb_error_password'), array( 'type' => 'text', 'disabled' => 'disabled', 'value' => $nbPasswordFailed ) );

		$disabledButtonResetpass = '';
		if($nbPasswordFailed == 0 || $nbPasswordFailed == '') {
			$disabledButtonResetpass = 'disabled';
		}
		echo $this->Form->submit( __m('User::Password::InitError'), array( 'name' => 'InitError', 'disabled' => $disabledButtonResetpass) );

		echo $this->Form->input( 'User.numtel', array( 'label' =>  required( __( 'numtel' ) ), 'type' => 'text', 'maxlength' => 15 ) );
		echo $this->Form->input('User.email', array('label' => __('email'), 'type' => 'text', 'maxlength' => 150));

		if( Configure::read( 'User.adresse' ) ) {
			echo $this->Form->input( 'User.numvoie', array( 'label' =>  __d( 'adresse', 'Adresse.numvoie' ), 'type' => 'text' ) );
			echo $this->Form->input( 'User.typevoie', array( 'label' =>  __d( 'adresse', 'Adresse.libtypevoie' ), 'type' => 'select', 'options' => $options['User']['typevoie'], 'empty' => true  ) );
			echo $this->Form->input( 'User.nomvoie', array( 'label' =>  __d( 'adresse', 'Adresse.nomvoie' ), 'type' => 'text' ) );
			echo $this->Form->input( 'User.compladr', array( 'label' =>  __d( 'adresse', 'Adresse.compladr' ), 'type' => 'text' ) );
			echo $this->Form->input( 'User.codepos', array( 'label' =>  __d( 'adresse', 'Adresse.codepos' ), 'type' => 'text', 'maxlength' => 5 ) );
			echo $this->Form->input( 'User.ville', array( 'label' =>  __( 'ville' ), 'type' => 'text' ) );
		}
	?>
	<?php echo $this->Form->input( 'User.date_naissance', array( 'label' =>  __( 'date_naissance' ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y'), 'minYear'=>date('Y') - 80 , 'empty' => true ) ) ;?>
	<?php echo $this->Form->input( 'User.date_deb_hab', array( 'label' => required(  __( 'date_deb_hab' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y') + 10, 'minYear'=>date('Y') - 10 , 'empty' => true ) );?>
	<?php echo $this->Form->input( 'User.date_fin_hab', array( 'label' => required(  __( 'date_fin_hab' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y') + 10, 'minYear'=>date('Y') - 10, 'empty' => true ) ) ;?>
</fieldset>
<div><?php echo $this->Form->input( 'User.filtre_zone_geo', array( 'label' => 'Restreindre les zones géographiques', 'type' => 'checkbox' ) );?></div>
<script type="text/javascript">
	function toutCocherZonesgeographiques() {
		return toutCocher( 'input[name="data[Zonegeographique][Zonegeographique][]"]' );
	}
	function toutDecocherZonesgeographiques() {
		return toutDecocher( 'input[name="data[Zonegeographique][Zonegeographique][]"]' );
	}
</script>
<fieldset class="col2" id="filtres_zone_geo">
	<legend>Zones géographiques</legend>
	<?php echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => "return toutCocherZonesgeographiques();" ) );?>
	<?php echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => "return toutDecocherZonesgeographiques();" ) );?>

	<?php echo $this->Form->input( 'Zonegeographique.Zonegeographique', array( 'label' => false, 'multiple' => 'checkbox' , 'options' => $zglist ) );?>
</fieldset>

<?php if($choix_referent_sectorisation_actif) {
	$refChecked = isset($this->request->data['Referent'][0]) ? 'checked' : '';
?>
<div class="input checkbox">
	<input type="checkbox" value="1" id="checkbox_referents_sectorisation" <?php echo $refChecked ?>  />
	<label for="checkbox_referents_sectorisation"><?php echo __m("User::Referent::Checkbox") ?></label>
</div>
<fieldset class="col2" id="referents_sectorisation">
	<legend><?php echo __m('User::Referent::Title') ?></legend>
	<?php echo $this->Form->input( 'Referent.Referent', array( 'label' => false, 'multiple' => 'checkbox' , 'options' => $referents_sectorisation ) );?>
</fieldset>
<?php } ?>
<fieldset class="col2">
	<?php
		echo $this->Form->input( 'User.group_id', array( 'label' => required( 'Groupe d\'utilisateur' ), 'type' => 'select' , 'options' => $gp, 'empty' => true ) );
		echo $this->Form->input( 'User.serviceinstructeur_id', array( 'label' => required( 'Service instructeur' ), 'type' => 'select' , 'options' => $si, 'empty' => true ) );
	?>
</fieldset>
<?php if( $departement == 93 ):?>
<fieldset class="col2">
	<legend>Type d'utilisateur</legend>
	<?php
		echo $this->Form->input( 'User.type', array( 'type' => 'select' , 'options' => $options['User']['type'], 'empty' => true, 'label' => required( __d( 'user', 'User.type' ) ) ) );
		echo $this->Form->input( 'User.communautesr_id', array( 'label' => __d( 'users', 'User.communautesr_id' ), 'type' => 'select' , 'options' => $communautessrs, 'empty' => true ) );
		echo $this->Form->input( 'User.structurereferente_id', array( 'label' => 'Structure référente liée au CPIE ou secrétaire PIE', 'type' => 'select' , 'options' => $structuresreferentes, 'empty' => true ) );
		echo $this->Form->input( 'User.referent_id', array( 'label' => 'Référent lié au chargé d\'insertion PIE', 'type' => 'select' , 'options' => $referents, 'empty' => true ) );
	?>
</fieldset>
<?php elseif( $departement == 66 ):?>
<fieldset class="col2">
	<legend>Service de l'utilisateur</legend>
	<?php echo $this->Form->input('User.service66_id',
		array(
			'type' => 'select',
			'options' => $services66,
			'empty' => true,
			'label' => __d('user', 'User.service66_id')
		)
	);?>
</fieldset>
<fieldset class="col2">
	<legend>Type d'utilisateur</legend>
	<?php
		echo $this->Form->input( 'User.type', array( 'type' => 'select' , 'options' => $options['User']['type'], 'empty' => true, 'label' => required( __d( 'user', 'User.type' ) ) ) );
		echo $this->Form->input( 'User.referent_id', array( 'label' => 'Référent lié à l\'OA', 'type' => 'select' , 'options' => $referents, 'empty' => true ) );
	?>
</fieldset>
<?php endif; ?>
<fieldset class="col2">
	<legend><?php echo __m('User::Categorie_utilisateur::Title') ?></legend>
	<?php
		echo $this->Form->input( 'User.categorieutilisateur_id', array( 'type' => 'select' , 'options' => $categories_utilisateurs, 'empty' => true, 'label' => __m('User::Categorie_utilisateur::Label')) );
	?>
</fieldset>
<fieldset class="col2">
	<legend><?php echo required( 'Est-il gestionnaire, notamment pour les PDOs ? ' );?></legend>
	<?php
		echo '<fieldset class="noborder">';
        echo $this->Xform->input( 'User.isgestionnaire', array( 'legend' => false, 'type' => 'radio', 'options' => $options['User']['isgestionnaire'] ) );
		echo '</fieldset>';
		echo $this->Xform->input( 'User.poledossierpcg66_id', array( 'type' => 'hidden', 'value' => '', 'id' => false ) );
        if( 66 == $departement ) {
			echo $this->Xform->input( 'User.poledossierpcg66_id', array( 'label' => 'Pôle lié au gestionnaire', 'type' => 'select', 'options' => $polesdossierspcgs66, 'empty' => true ) );
        }
    ?>
</fieldset>
<fieldset class="col2">
	<legend><?php echo (__d( 'user', 'User.titre_bloc_accueil' )); ?></legend>
	<?php
		echo $this->Form->input( 'User.accueil_referent_id', array( 'label' => __d( 'user', 'User.accueil_referent_id' ), 'type' => 'select' , 'options' => $referents, 'empty' => true ) );
		echo $this->Form->input( 'User.accueil_reference_affichage', array( 'label' => __d( 'user', 'User.accueil_reference_affichage' ), 'type' => 'select' , 'options' => $options['User']['accueil_reference_affichage'] ) );
	?>
</fieldset>
<?php
	if( 66 == $departement ) {
		echo $this->Form->input('Ancienpoledossierpcg66.Ancienpoledossierpcg66', array( 'type' => 'hidden', 'value' => '', 'id' => false ) );
		echo $this->Html->tag(
			'fieldset',
			$this->Html->tag( 'legend', 'Pôle(s) PCG anciennement lié(s) à l\'utilisateur' )
			.$this->Form->input(
				'Ancienpoledossierpcg66.Ancienpoledossierpcg66',
				array(
					'label' => false,
					'type' => 'select',
					'multiple' => 'checkbox',
					'options' => $polesdossierspcgs66,
					'class' => 'col3'
				)
			)
		);
	}
?>
<fieldset class="col2">
	<legend><?php echo required( 'Peut-il accéder aux données sensibles ? ' );?></legend>
	<?php echo $this->Xform->input( 'User.sensibilite', array( 'legend' => false, 'type' => 'radio', 'options' => $options['User']['sensibilite'] ) );?>
</fieldset>
<?php
	echo $this->Observer->disableFieldsetOnCheckbox(
		'User.filtre_zone_geo',
		'filtres_zone_geo',
		false
	);

	if( $departement == 93 ) {
		echo $this->Observer->disableFieldsetOnValue(
			'User.type',
			'filtres_zone_geo',
			array( 'cg', '' ),
			false
		);
		echo $this->Observer->disableFieldsOnValue(
			'User.type',
			'User.filtre_zone_geo',
			array( 'externe_cpdvcom', 'externe_cpdv', 'externe_secretaire', 'externe_ci' ),
			true
		);
	}
	echo $this->Observer->disableFieldsOnValue(
		'User.type',
		'User.structurereferente_id',
		array( 'externe_cpdv', 'externe_secretaire' ),
		false
	);
	echo $this->Observer->disableFieldsOnValue(
		'User.type',
		'User.referent_id',
		array( 'externe_ci' ),
		false
	);
	echo $this->Observer->disableFieldsOnValue(
		'User.type',
		'User.communautesr_id',
		array( 'externe_cpdvcom' ),
		false
	);
	if( $departement == 66 ) {
		echo $this->Observer->disableFieldsOnRadioValue(
			$formId,
			'User.isgestionnaire',
			'User.poledossierpcg66_id',
			array( 'N', '', null ),
			false,
			true
		);
	}
?>
<script type="text/javascript">
	//<![CDATA[
	document.observe( 'dom:loaded', function() { try {
		observeDisableFieldsetOnCheckbox( 'checkbox_referents_sectorisation', 'referents_sectorisation', false, true );
	} catch( e ) {
		console.error( e );
	} } );
	//]]>
	</script>