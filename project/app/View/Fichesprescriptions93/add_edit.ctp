<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

	echo $this->Default3->DefaultForm->create( 'Ficheprescription93' );

	echo $this->Default3->subform(
		array(
			'Ficheprescription93.id' => array( 'type' => 'hidden' ),
			'Ficheprescription93.personne_id' => array( 'type' => 'hidden' ),
			'Ficheprescription93.action' => array( 'type' => 'hidden', 'value' => $this->request->params['action'] ),
		)
	);
// Cadre Origine Positionnement
 if ( $this->request->data['Ficheprescription93']['frsa_datetransmi']!= NULL ) {
		echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __d( $this->request->params['controller'], 'Ficheprescription93.OriginePositionnement' ) )
		.$this->Default3->subform(
			array(
				'Ficheprescription93.posorigine' => array('view' => true,	'empty' => true ),
				'Ficheprescription93.frsa_datetransmi' => array('view' => true, 'empty' => true ),
				'Ficheprescription93.frsa_decouverteaction' => array('view' => true, 'empty' => true  ),
				'Ficheprescription93.frsa_motivation' => array('view' => true, 'empty' => true ),
			),
			array(
				'options' => $options,
			)
		)
	);
 }else{
	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __d( $this->request->params['controller'], 'Ficheprescription93.OriginePositionnement' ) ).
		$this->Html->tag( 'text', __d( $this->request->params['controller'], 'Ficheprescription93.AucunFRSA' ) )
	);
 }


	// Cadre prescripteur / référent
	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __d( $this->request->params['controller'], 'Ficheprescription93.Prescripteur' ) )
		.$this->Default3->subform(
			array(
				'Ficheprescription93.structurereferente_id' => array( 'empty' => true ),
				'Ficheprescription93.referent_id' => array( 'empty' => true ),
			),
			array(
				'options' => $options,
			)
		)
		.$this->Html->tag( 'div', ' ', array( 'id' => 'CoordonneesPrescripteur' ) )
		.$this->Default3->subform(
			array(
				'Ficheprescription93.objet',
			),
			array(
				'options' => $options,
			)
		)
	);

	// Cadre bénéficiaire
	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __d( $this->request->params['controller'], 'Ficheprescription93.Beneficiaire' ) )
		.$this->Default3->subform(
			array(
				'Instantanedonneesfp93.benef_qual' => array(
					'view' => true,
					'type' => 'text',
					'hidden' => true,
					'options' =>(array) Hash::get( $options ,'Personne.qual' )
				),
				'Instantanedonneesfp93.benef_nom' => array(
					'view' => true,
					'type' => 'text',
					'hidden' => true,
				),
				'Instantanedonneesfp93.benef_prenom' => array(
					'view' => true,
					'type' => 'text',
					'hidden' => true,
				),
				'Instantanedonneesfp93.benef_dtnai' => array(
					'view' => true,
					'type' => 'date',
					'hidden' => true,
				),
				'Instantanedonneesfp93.benef_adresse' => array(
					'view' => true,
					'type' => 'text',
					'hidden' => true,
				),
				'Instantanedonneesfp93.benef_codepos' => array(
					'view' => true,
					'type' => 'text',
					'hidden' => true,
				),
				'Instantanedonneesfp93.benef_nomcom' => array(
					'view' => true,
					'type' => 'text',
					'hidden' => true,
				),
				// TODO: adresse
				'Instantanedonneesfp93.benef_tel_fixe',
				'Instantanedonneesfp93.benef_tel_port',
				'Instantanedonneesfp93.benef_email',
				'Instantanedonneesfp93.benef_natpf' => array(
					'view' => true,
					'type' => 'text',
					'hidden' => true,
					'options' => $options['Instantanedonneesfp93']['benef_natpf'],
					'hidden' => true,
				),
				'Instantanedonneesfp93.benef_natpf_3mois' => array(
					'empty' => true,
					'options' => $options['Instantanedonneesfp93']['benef_natpf'],
				),
				'Instantanedonneesfp93.benef_matricule' => array(
					'view' => true,
					'type' => 'text',
					'hidden' => true,
				),
				'Instantanedonneesfp93.benef_inscritpe' => array(
					'view' => true,
					'type' => 'text',
					'hidden' => true,
				),
				'Instantanedonneesfp93.benef_identifiantpe' => array(
					'view' => true,
					'type' => 'text',
					'hidden' => true,
				),
				'Instantanedonneesfp93.benef_nivetu' => array( 'empty' => true ),
				'Instantanedonneesfp93.benef_dernier_dip',
				'Instantanedonneesfp93.benef_dip_ce' => array( 'empty' => true ),
				'Instantanedonneesfp93.benef_positioncer' => array(
					'view' => true,
					'type' => 'text',
					'hidden' => true,
				),
				'Instantanedonneesfp93.benef_dd_ci' => array(
					'view' => true,
					'type' => 'date',
					'hidden' => true,
				),
				'Instantanedonneesfp93.benef_df_ci' => array(
					'view' => true,
					'type' => 'date',
					'hidden' => true,
				),
			),
			array(
				'options' => $options
			)
		)
	);

	// Liens du catalogue
	$links = '';
	foreach( (array)Configure::read( 'Cataloguepdifp93.urls' ) as $text => $url ) {
		$links .= $this->Html->tag( 'li', $this->Html->link( $text, $url, array( 'class' => 'external' ) ) );
	}
	if( !empty( $links ) ) {
		$links = $this->Html->tag( 'ul', $links );
	}

	// Cadre prestataire / partenaire
	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __d( $this->request->params['controller'], 'Ficheprescription93.Prestataire' ) )
		.$links
		.$this->Default3->subform(
			array(
				'Ficheprescription93.numconvention' => array( 'type' => 'text' ),
				'Ficheprescription93.typethematiquefp93_id' => array( 'empty' => true ),
				'Ficheprescription93.yearthematiquefp93_id' => array( 'empty' => true, 'required' => true ),
				'Ficheprescription93.thematiquefp93_id' => array( 'empty' => true ),
				'Ficheprescription93.categoriefp93_id' => array( 'empty' => true ),
				'Ficheprescription93.filierefp93_id' => array( 'empty' => true ),
				'Ficheprescription93.prestatairefp93_id' => array( 'empty' => true ),
				'Ficheprescription93.actionfp93_id' => array( 'empty' => true ),
				'Ficheprescription93.actionfp93',
				'Ficheprescription93.adresseprestatairefp93_id' => array( 'empty' => true ),
			),
			array(
				'options' => $options,
			)
		)
		.$this->Default3->subform(
			array(
				'Prestatairehorspdifp93.name',
				'Ficheprescription93.selection_adresse_prestataire' => array( 'type' => 'select', 'options' => array(), 'empty' => true ),
				'Prestatairehorspdifp93.id',
				'Prestatairehorspdifp93.adresse',
				'Prestatairehorspdifp93.codepos',
				'Prestatairehorspdifp93.localite',
				'Prestatairehorspdifp93.tel' => array( 'maxlength' => 14 ),
				'Prestatairehorspdifp93.fax' => array( 'maxlength' => 14 ),
				'Prestatairehorspdifp93.email',
			),
			array(
				'options' => $options,
			)
		)
		.$this->Html->tag( 'div', ' ', array( 'id' => 'CoordonneesPrestataire' ) )
		.$this->Default3->subform(
			array(
				'Ficheprescription93.rdvprestataire_adresse_check' => array( 'type' => 'checkbox' ),
				'Ficheprescription93.rdvprestataire_adresse' => array( 'label' => false ),
				'Ficheprescription93.statut' => array( 'type' => 'hidden' ),
				'Ficheprescription93.dd_action' => array( 'empty' => true, 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1 ),
				'Ficheprescription93.df_action' => array( 'empty' => true, 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1 ),
			),
			array(
				'options' => $options,
			)
		)
		.$this->Html->tag(
			'div',
			$this->Form->input( 'Ficheprescription93.duree_action', array( 'type' => 'hidden', 'id' => false ) )
			.$this->Default3->DefaultForm->fieldValue( 'Ficheprescription93.duree_action' ),
			array( 'id' => 'DureeActionPdi' )
		)
		.$this->Default3->subform(
			array(
				'Ficheprescription93.duree_action',
				'Ficheprescription93.rdvprestataire_date' => array( 'empty' => true, 'dateFormat' => 'DMY', 'timeFormat' => 24, 'maxYear' => date( 'Y' ) + 1 ),
				'Ficheprescription93.motifcontactfp93_id' => array( 'empty' => true ),
				'Documentbeneffp93.Documentbeneffp93' => array( 'multiple' => 'checkbox' ),
				'Ficheprescription93.documentbeneffp93_autre',
			),
			array(
				'options' => $options,
			)
		)
	);

	// Cadre "Engagement"
	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __d( $this->request->params['controller'], 'Ficheprescription93.Engagement' ) )
		.$this->Html->tag( 'p', __d( $this->request->params['controller'], 'Ficheprescription93.texte_engagement' ) )
		.$this->Default3->subform(
			array(
				'Ficheprescription93.date_signature' => array( 'dateFormat' => 'DMY',  'timeFormat' => 24,'maxYear' => date( 'Y' ) + 1  ),// , 'empty' => true
			),
			array(
				'options' => $options,
			)
		)
	);

	// Cadre "Modalités de transmission"
	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __d( $this->request->params['controller'], 'Ficheprescription93.Transmission' ) )
		.$this->Default3->subform(
			array(
				'Ficheprescription93.date_transmission' => array('dateFormat' => 'DMY', 'timeFormat' => 24, 'maxYear' => date( 'Y' ) + 1 ),// 'empty' => true,
				'Modtransmfp93.Modtransmfp93' => array( 'multiple' => 'checkbox' ),
			),
			array(
				'options' => $options,
			)
		)
	);

	// Cadre "Résultat de l'effectivité du positionnement"
	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __d( $this->request->params['controller'], 'Ficheprescription93.Effectivite' ) )
		.$this->Default3->subform(
			array(
				'Ficheprescription93.benef_retour_presente' => array( 'empty' => true, 'required' => true ),
				'Ficheprescription93.date_retour' => array( 'empty' => true, 'dateFormat' => 'DMY', 'timeFormat' => 24, 'maxYear' => date( 'Y' ) + 1 )
			),
			array(
				'options' => $options,
			)
		)
	);

	// Cadre "Suivi du positionnement
	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __d( $this->request->params['controller'], 'Ficheprescription93.Suivi' ) )
		.$this->Default3->subform(
			array(
				'Ficheprescription93.personne_retenue' => array( 'empty' => true ),
				'Ficheprescription93.motifnonretenuefp93_id' => array( 'empty' => true ),
				'Ficheprescription93.personne_nonretenue_autre',

				'Ficheprescription93.personne_a_integre' => array( 'empty' => true ),
				'Ficheprescription93.personne_date_integration' => array( 'dateFormat' => 'DMY', 'empty'=>true ),
				'Ficheprescription93.motifnonintegrationfp93_id' => array( 'empty' => true ),
				'Ficheprescription93.personne_nonintegre_autre',

				'Ficheprescription93.personne_acheve' => array( 'empty' => true ),
				'Ficheprescription93.motifactionachevefp93_id' => array(  'empty' => true ),
				'Ficheprescription93.motifnonactionachevefp93_id' => array( 'empty' => true ),
				'Ficheprescription93.personne_acheve_autre',

				'Ficheprescription93.date_bilan_mi_parcours' => array( 'empty' => true, 'dateFormat' => 'DMY', 'timeFormat' => 24, 'maxYear' => date( 'Y' ) + 1 ),
				'Ficheprescription93.date_bilan_final' => array( 'empty' => true, 'dateFormat' => 'DMY', 'timeFormat' => 24, 'maxYear' => date( 'Y' ) + 1 ),
			),
			array(
				'options' => $options,
			)
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Validate', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
?>
<?php
	// Début du javascript

	// Catalogue PDI
	echo $this->Observer->disableFieldsOnValue(
		'Ficheprescription93.typethematiquefp93_id',
		array(
			'Ficheprescription93.prestatairehorspdifp93_id',
			'Ficheprescription93.actionfp93',
			'Ficheprescription93.selection_prestataire',
			'Ficheprescription93.selection_adresse_prestataire',
			'Prestatairehorspdifp93.name',
			'Prestatairehorspdifp93.adresse',
			'Prestatairehorspdifp93.codepos',
			'Prestatairehorspdifp93.localite',
			'Prestatairehorspdifp93.tel',
			'Prestatairehorspdifp93.fax',
			'Prestatairehorspdifp93.email',
			'Ficheprescription93.duree_action'
		),
		array( null, '', 'pdi' ),
		true,
		true
	);

	// Catalogue Hors PDI
	echo $this->Observer->disableFieldsOnValue(
		'Ficheprescription93.typethematiquefp93_id',
		array(
			'Ficheprescription93.numconvention',
			'Ficheprescription93.prestatairefp93_id',
			'Ficheprescription93.actionfp93_id',
			'Ficheprescription93.adresseprestatairefp93_id'
		),
		array( 'horspdi' ),
		true,
		true
	);

	// Le bénéficiaire est invité à se munir de...
	foreach( (array)Hash::get( $options, 'Autre.Ficheprescription93.documentbeneffp93_id' ) as $documentbeneffp93_id ) {
		echo $this->Observer->disableFieldsOnCheckbox(
			"Documentbeneffp93.Documentbeneffp93.{$documentbeneffp93_id}",
			'Ficheprescription93.documentbeneffp93_autre',
			false,
			false,
			true
		);
	}

	echo $this->Observer->dependantSelect(
		array(
			'Ficheprescription93.structurereferente_id' => 'Ficheprescription93.referent_id',
		)
	);

	// Personne retenue
	echo $this->Observer->disableFieldsOnValue(
		'Ficheprescription93.personne_retenue',
		array(
			'Ficheprescription93.motifnonretenuefp93_id',
			'Ficheprescription93.personne_nonretenue_autre',
		),
		array( null, '', '1' ),
		true,
		true
	);

	echo $this->Observer->disableFieldsOnValue(
		'Ficheprescription93.motifnonretenuefp93_id',
		array(
			'Ficheprescription93.personne_nonretenue_autre'
		),
		(array)Hash::get( $options, 'Autre.Ficheprescription93.motifnonretenuefp93_id' ),
		false,
		true
	);

	// Personne a intégré
	echo $this->Observer->disableFieldsOnValue(
		'Ficheprescription93.personne_a_integre',
		array(
			'Ficheprescription93.motifnonintegrationfp93_id',
			'Ficheprescription93.personne_nonintegre_autre',
		),
		array( null, '', '1' ),
		true,
		true
	);
    //date pour "le bénéficiaire a intégré l'action"
	echo $this->Observer->disableFieldsOnValue(
		'Ficheprescription93.personne_a_integre',
		array(
			'Ficheprescription93.personne_date_integration.day',
			'Ficheprescription93.personne_date_integration.month',
			'Ficheprescription93.personne_date_integration.year',
		),
		array( null, '', '0' ),
		true,
		true
	);
	echo $this->Observer->disableFieldsOnValue(
		'Ficheprescription93.motifnonintegrationfp93_id',
		array(
			'Ficheprescription93.personne_nonintegre_autre'
		),
		(array)Hash::get( $options, 'Autre.Ficheprescription93.motifnonintegrationfp93_id' ),
		false,
		true
	);

	// Personne a acheve l'action
	echo $this->Observer->disableFieldsOnValue(
		'Ficheprescription93.personne_acheve',
		array(
			'Ficheprescription93.motifactionachevefp93_id',
			'Ficheprescription93.personne_acheve_autre'
		),
		array( '1' ),
		false,
		true
	);
	echo $this->Observer->disableFieldsOnValue(
		'Ficheprescription93.personne_acheve',
		array(
			'Ficheprescription93.motifnonactionachevefp93_id',
			'Ficheprescription93.personne_acheve_autre'
		),
		array( '0' ),
		false,
		true
	);
	echo $this->Observer->disableFieldsOnValue(
		'Ficheprescription93.personne_acheve',
		array(
			'Ficheprescription93.personne_acheve_autre'
		),
		array( null,'' ),
		true,
		true
	);
	echo $this->Observer->disableFieldsOnValue(
		'Ficheprescription93.motifactionachevefp93_id',
		array(
			'Ficheprescription93.personne_acheve_autre'
		),
		(array)Hash::get( $options, 'Autre.Ficheprescription93.motifactionachevefp93_id' ),
		false,
		true
	);
	echo $this->Observer->disableFieldsOnValue(
		'Ficheprescription93.motifnonactionachevefp93_id',
		array(
			'Ficheprescription93.personne_acheve_autre'
		),
		(array)Hash::get( $options, 'Autre.Ficheprescription93.motifnonactionachevefp93_id' ),
		false,
		false
	);

//fiche prescritpteur
	echo $this->Ajax2->updateDivOnFieldsChange(
		'CoordonneesPrescripteur',
		array( 'action' => 'ajax_prescripteur' ),
		array(
			'Ficheprescription93.structurereferente_id',
			'Ficheprescription93.referent_id',
		)
	);

	echo $this->Ajax2->observe(
		array(
			'Ficheprescription93.numconvention' => array( 'event' => 'keyup' ),
			'Ficheprescription93.typethematiquefp93_id',
			'Ficheprescription93.yearthematiquefp93_id',
			'Ficheprescription93.thematiquefp93_id',
			'Ficheprescription93.categoriefp93_id',
			'Ficheprescription93.filierefp93_id',
			'Ficheprescription93.prestatairefp93_id',
			'Ficheprescription93.actionfp93_id',
			'Ficheprescription93.action',
			'Ficheprescription93.id',
			'Ficheprescription93.adresseprestatairefp93_id'
		),
		array(
			'url' => array( 'action' => 'ajax_action' ),
			'onload' => !empty( $this->request->data )
		)
	);

	echo $this->Ajax2->observe(
		array(
			'Prestatairehorspdifp93.name' => array( 'event' => 'keyup' ),
			'Ficheprescription93.selection_adresse_prestataire',
			'Prestatairehorspdifp93.id' => array( 'event' => false ),
			'Prestatairehorspdifp93.adresse' => array( 'event' => false ),
			'Prestatairehorspdifp93.codepos' => array( 'event' => false ),
			'Prestatairehorspdifp93.localite' => array( 'event' => false ),
			'Prestatairehorspdifp93.tel' => array( 'event' => false ),
			'Prestatairehorspdifp93.fax' => array( 'event' => false ),
			'Prestatairehorspdifp93.email' => array( 'event' => false )
		),
		array(
			'url' => array( 'action' => 'ajax_prestataire_horspdi' ),
			'onload' => !empty( $this->request->data )
		)
	);

	echo $this->Observer->disableFieldsOnCheckbox(
		'Ficheprescription93.rdvprestataire_adresse_check',
		array(
			'Ficheprescription93.rdvprestataire_adresse'
		),
		false,
		true
	);

	echo $this->Observer->disableFormOnSubmit();
?>

<script type="text/javascript">
	Element.observe( document, 'changed:Ficheprescription93.actionfp93_id', function() {
		new Ajax.AbortableUpdater(
			'DureeActionPdi',
			'<?php echo Router::url( array( 'action' => 'ajax_duree_pdi' ) );?>',
			{
				parameters: {
					'data[Ficheprescription93][typethematiquefp93_id]': $F( '<?php echo $this->Html->domId( 'Ficheprescription93.typethematiquefp93_id' );?>' ),
					'data[Ficheprescription93][actionfp93_id]': $F( '<?php echo $this->Html->domId( 'Ficheprescription93.actionfp93_id' );?>' )
				}
			}
		);

		cakeDateTimeSeparator( '<?php echo $this->Html->domId( 'Ficheprescription93.rdvprestataire_date' );?>' );
	} );

	Element.observe( document, 'changed:Ficheprescription93.adresseprestatairefp93_id', function() {
		new Ajax.AbortableUpdater(
			'CoordonneesPrestataire',
			'<?php echo Router::url( array( 'action' => 'ajax_prestataire' ) );?>',
			{
				parameters: {
					'data[Ficheprescription93][adresseprestatairefp93_id]': $F( '<?php echo $this->Html->domId( 'Ficheprescription93.adresseprestatairefp93_id' );?>' )
				}
			}
		);
	} );

	// Suppression du contenu du champ "Adresse du lieu de rendez-vous" lorsqu'on décoche la case "Adresse du lieu de rendez-vous si différente de l'adresse rapatriée"
	Element.observe( $( 'Ficheprescription93RdvprestataireAdresseCheck' ), 'click', function( event ) {
		if( ( $F( 'Ficheprescription93RdvprestataireAdresseCheck' ) == null ) ) {
			$( 'Ficheprescription93RdvprestataireAdresse' ).value = '';
		}
	} );

	// Suppression du contenu du champ Year au changement de Type
	Element.observe( $( 'Ficheprescription93Typethematiquefp93Id' ), 'click', function( event ) {
			$( 'Ficheprescription93Yearthematiquefp93Id' ).value = '';
	} );
	// Suppression du contenu du champ Prescription au changement de Year
	Element.observe( $( 'Ficheprescription93Yearthematiquefp93Id' ), 'click', function( event ) {
			$( 'Ficheprescription93Thematiquefp93Id' ).value = '';
	} );
	//cache les éléments "suivi du positionnement" si "Le bénéficiaire a été retenu par la structure" est à non
	Element.observe( $( 'Ficheprescription93PersonneRetenue' ), 'change', function( event ) {
	    if($( 'Ficheprescription93PersonneRetenue' ).value=='0'){
	        disabledFromRetenuStructure(true);
	    }
	    else {
	        disabledFromRetenuStructure(false);
	    }
	} );

	//bloque d'office les champs date si le choix "Le bénéficiaire s'est présenté n'est pas renseigné à "OUI"
	disabledFromBeneficiairePresente(true);
	Element.observe( $( 'Ficheprescription93BenefRetourPresente' ), 'change', function( event ) {
	    if($( 'Ficheprescription93BenefRetourPresente' ).value=='oui'){
	        disabledFromBeneficiairePresente(false);
	    }
	    else {
	        disabledFromBeneficiairePresente(true);
	        $('Ficheprescription93DateRetourDay').value='';
	        $('Ficheprescription93DateRetourMonth').value='';
	        $('Ficheprescription93DateRetourYear').value='';
	    }
	} );

	//vide la date d'entrée dans l'action dans le cas où cette dernière est rempli puis le select changé à "non" ou "vide"
	Element.observe( $( 'Ficheprescription93PersonneAIntegre' ), 'change', function( event ) {
	    if($( 'Ficheprescription93PersonneAIntegre' ).value!='1'){
	        $('Ficheprescription93PersonneDateIntegrationDay').value='';
	        $('Ficheprescription93PersonneDateIntegrationMonth').value='';
	        $('Ficheprescription93PersonneDateIntegrationYear').value='';
	    }
	} );

	function disabledFromBeneficiairePresente(statut){
	    $('Ficheprescription93DateRetourDay').disabled=statut;
	    $('Ficheprescription93DateRetourMonth').disabled=statut;
	    $('Ficheprescription93DateRetourYear').disabled=statut;
	}

	function disabledFromRetenuStructure(statut) {
	    $('Ficheprescription93PersonneAIntegre').disabled=statut;
	    $('Ficheprescription93PersonneAcheve').disabled=statut;
	    $('Ficheprescription93DateBilanMiParcoursDay').disabled=statut;
	    $('Ficheprescription93DateBilanMiParcoursMonth').disabled=statut;
	    $('Ficheprescription93DateBilanMiParcoursYear').disabled=statut;
	    $('Ficheprescription93DateBilanFinalDay').disabled=statut;
	    $('Ficheprescription93DateBilanFinalMonth').disabled=statut;
	    $('Ficheprescription93DateBilanFinalYear').disabled=statut;
	}

	function clearFicheprescription93FormField( fieldId ) {
		try {
			var elmt = $( fieldId );
			//console.log( elmt );
			if( $(elmt) ) {
				if( $(elmt).type === 'checkbox' ) {
					if( $(elmt).checked ) {
						$(elmt).simulate( 'click' );
					}
				}
				else {
					$(elmt).value = '';
				}
			}
		} catch( Exception ) {
			console.log( Exception );
		}
	}

	Element.observe( document, 'reset:Ficheprescription93.actionprestataire', function() {
		<?php
			$fields = array(
				'Ficheprescription93.rdvprestataire_adresse_check',
				'Ficheprescription93.rdvprestataire_adresse',
				'Ficheprescription93.dd_action.day',
				'Ficheprescription93.dd_action.month',
				'Ficheprescription93.dd_action.year',
				'Ficheprescription93.df_action.day',
				'Ficheprescription93.df_action.month',
				'Ficheprescription93.df_action.year',
				'Ficheprescription93.duree_action',
				'Ficheprescription93.rdvprestataire_date.day',
				'Ficheprescription93.rdvprestataire_date.month',
				'Ficheprescription93.rdvprestataire_date.year',
				'Ficheprescription93.rdvprestataire_date.hour',
				'Ficheprescription93.rdvprestataire_date.min'
			);
		?>
		<?php foreach( $fields as $field ): ?>
			clearFicheprescription93FormField( '<?php echo $this->Html->domId( $field );?>' );
		<?php endforeach; ?>
	} );

	document.addEventListener('DOMContentLoaded', function() {
    if( ( $F( 'Ficheprescription93Motifactionachevefp93Id' ) != null ) ) {
			var strjson = '<?php echo json_encode ( Hash::get( $options, 'Autre.Ficheprescription93.motifactionachevefp93_id' ) ) ; ?>';
			var objjson = JSON.parse(strjson) ;
			var value = $( 'Ficheprescription93Motifactionachevefp93Id' ).value;
			var keys = [];
			for(var k in objjson) keys.push(k);
			var index = keys.indexOf( value );
			if (index != null){
				 var field = $( 'Ficheprescription93PersonneAcheveAutre' );
				  field.enable();
					field.show();
			}
		}
}, false);

</script>