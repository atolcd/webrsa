<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'ajoutdossiercomplet', "Ajoutdossierscomplets::{$this->action}" )
	);

	echo $this->Form->create( 'Ajoutdossiercomplet', array( 'id' => 'dossiercomplet', 'novalidate' => true ) );

	echo '<fieldset><legend>Ajout d\'un nouveau dossier</legend>';
		//On masque les prestations et on leur met des valeurs par défaut
		echo $this->Form->input( 'Prestation.natprest', array( 'type' => 'hidden', 'value' => 'RSA' ) );
		echo $this->Form->input( 'Prestation.rolepers', array( 'type' => 'hidden', 'value' => 'DEM' ) );

// 		echo $this->Form->input( 'Dernierdossierallocataire.dossier_id', array( 'type' => 'hidden' ) );
// 		echo $this->Form->input( 'Dernierdossierallocataire.personne_id', array( 'type' => 'hidden' ) );


		// Données de la personne
		echo '<fieldset><legend>Données de la personne</legend>';
			echo $this->Default2->subform(
				array(
					'Personne.qual' => array( 'required' => true, 'type' => 'select', 'options' => $options['qual'] ),
					'Personne.nom' => array( 'required' => true ),
					'Personne.prenom' => array( 'required' => true ),
					'Personne.dtnai' => array( 'required' => false, 'type' => 'date', 'minYear' => date('Y')-100, 'maxYear' => date('Y') ),
					'Personne.nir'
				)
			);
		echo '</fieldset>';

		// Adresse de la personne
		echo $this->Form->input( 'Adressefoyer.rgadr', array( 'type' => 'hidden', 'value' => '01' ) );
		echo '<fieldset><legend>Adresse de la personne</legend>';
			echo $this->Default2->subform(
				array(
					'Adresse.presence' => array( 'label' => 'Adresse présente ?', 'type' => 'checkbox', 'checked' => true )
				)
			);
			echo $this->Default2->subform(
				array(
					 'Adresse.numvoie',
					 'Adresse.libtypevoie' => array( 'required' => ValidateAllowEmptyUtility::isRequired('Adresse.libtypevoie'), 'type' => 'select', 'options' => $options['Adresse']['libtypevoie'], 'empty' => true ),
					 'Adresse.nomvoie'  => array( 'required' => ValidateAllowEmptyUtility::isRequired('Adresse.nomvoie') ),
					 'Adresse.complideadr',
					 'Adresse.codepos' => array( 'required' => true),
					 'Adresse.nomcom' => array( 'required' => true ),
					 'Adresse.canton'
				)
			);
		echo '</fieldset>';

		// Informations du dossier
		echo '<fieldset><legend>Données dossier</legend>';
			echo $this->Default2->subform(
				array(
					 'Dossier.numdemrsatemp' => array( 'label' => 'Génération automatique d\'un N° de demande RSA temporaire', 'type' => 'checkbox' ),
					 'Dossier.numdemrsa' => array( 'required' => true ),
					 'Dossier.dtdemrsa' => array( 'type' => 'date', 'format' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+1, 'required' => ValidateAllowEmptyUtility::isRequired('Dossier.dtdemrsa') ),
					 'Dossier.matricule',
                    'Serviceinstructeur.id' => array( 'label'=> 'Service instructeur', 'options' => $services ),
				)
			);
		echo '</fieldset>';
	echo '</fieldset>';
	echo $this->Xform->end( 'Save' );
?>
<script type="text/javascript">
	observeDisableFieldsOnCheckbox(
		'DossierNumdemrsatemp',
		[
			'DossierNumdemrsa'
		],
		true
	);

	// On masque les champs adresses si la case est cochée
	observeDisableFieldsOnCheckbox(
		'AdressePresence',
		[
			'AdresseNumvoie',
			'AdresseLibtypevoie',
			'AdresseNomvoie',
			'AdresseCompladr',
			'AdresseComplideadr',
			'AdresseCodepos',
			'AdresseNomcom',
			'AdresseCanton'
		],
		false
	);
</script>