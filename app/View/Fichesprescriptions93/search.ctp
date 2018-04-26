<?php $this->start( 'custom_search_filters' );?>
<?php
	// Début spécificités fiche de prescription
	echo $this->Xform->input( 'Search.Ficheprescription93.exists', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Ficheprescription93.exists' ), 'domain' => 'fichesprescriptions93', 'empty' => true ) );
	echo '<fieldset id="SpecificitesFichesprescriptions93"><legend>'.__d( 'fichesprescriptions93', 'Search.Ficheprescription93' ).'</legend>';
	echo $this->Xform->input( 'Search.Ficheprescription93.numconvention', array( 'domain' => 'fichesprescriptions93' ) );
	echo $this->Xform->input( 'Search.Ficheprescription93.typethematiquefp93_id', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Ficheprescription93.typethematiquefp93_id' ), 'empty' => true, 'domain' => 'fichesprescriptions93' ) );
	echo $this->Xform->input( 'Search.Ficheprescription93.yearthematiquefp93_id', array( 'type' => 'select', 'options' => array(), 'empty' => true , 'domain' => 'fichesprescriptions93' ) );
	echo $this->Xform->input( 'Search.Ficheprescription93.thematiquefp93_id', array( 'type' => 'select', 'options' => array(), 'empty' => true, 'domain' => 'fichesprescriptions93' ) );
	echo $this->Xform->input( 'Search.Ficheprescription93.categoriefp93_id', array( 'type' => 'select', 'options' => array(), 'empty' => true, 'domain' => 'fichesprescriptions93' ) );
	echo $this->Xform->input( 'Search.Ficheprescription93.filierefp93_id', array( 'type' => 'select', 'options' => array(), 'empty' => true, 'domain' => 'fichesprescriptions93' ) );
	echo $this->Xform->input( 'Search.Ficheprescription93.prestatairefp93_id', array( 'type' => 'select', 'options' => array(), 'empty' => true, 'domain' => 'fichesprescriptions93' ) );
	echo $this->Xform->input( 'Search.Ficheprescription93.actionfp93_id', array( 'type' => 'select', 'options' => array(), 'empty' => true, 'domain' => 'fichesprescriptions93' ) );
	echo $this->Xform->input( 'Search.Ficheprescription93.actionfp93', array( 'type' => 'text', 'domain' => 'fichesprescriptions93' ) );

	// TODO: fieldset
	echo '<fieldset><legend>'.__d( 'fichesprescriptions93', 'Search.Ficheprescription93.Referent' ).'</legend>';
	echo $this->Allocataires->communautesr( 'Ficheprescription93', array( 'options' => array( 'Search' => $options ), 'hide' => false ) );
	echo $this->Xform->input( 'Search.Ficheprescription93.structurereferente_id', array( 'type' => 'select', 'empty' => true, 'options' => $options['PersonneReferent']['structurereferente_id'], 'domain' => 'fichesprescriptions93' ) );
	echo $this->Xform->input( 'Search.Ficheprescription93.referent_id', array( 'type' => 'select', 'empty' => true, 'options' => $options['PersonneReferent']['referent_id'], 'domain' => 'fichesprescriptions93' ) );
	echo '</fieldset>';

	echo $this->SearchForm->dateRange( 'Search.Ficheprescription93.created', array( 'domain' => 'fichesprescriptions93', 'hide' => true ) );
	echo $this->SearchForm->dateRange( 'Search.Ficheprescription93.date_signature', array( 'domain' => 'fichesprescriptions93', 'hide' => true ) );
	echo $this->SearchForm->dateRange( 'Search.Ficheprescription93.rdvprestataire_date', array( 'domain' => 'fichesprescriptions93', 'hide' => true ) );
	echo $this->SearchForm->dateRange( 'Search.Ficheprescription93.date_transmission', array( 'domain' => 'fichesprescriptions93', 'hide' => true ) );
	echo $this->SearchForm->dateRange( 'Search.Ficheprescription93.date_retour', array( 'domain' => 'fichesprescriptions93', 'hide' => true ) );

	echo $this->Xform->input( 'Search.Ficheprescription93.statut', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Ficheprescription93.statut' ), 'empty' => true, 'domain' => 'fichesprescriptions93' ) );

	echo $this->Xform->input( 'Search.Ficheprescription93.has_date_retour', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Ficheprescription93.exists' ), 'domain' => 'fichesprescriptions93', 'empty' => true ) );

	echo $this->SearchForm->dateRange( 'Search.Ficheprescription93.df_action', array( 'domain' => 'fichesprescriptions93', 'hide' => true ) );

	$paths = array(
		'Ficheprescription93.benef_retour_presente',
		'Ficheprescription93.personne_recue',
		'Ficheprescription93.personne_retenue',
		'Ficheprescription93.personne_a_integre',
	);
	foreach( $paths as $path ) {
		echo $this->Xform->input( "Search.{$path}", array( 'type' => 'select', 'options' => (array)Hash::get( $options, $path ), 'domain' => 'fichesprescriptions93', 'empty' => true ) );
	}

	echo $this->Xform->input( 'Search.Ficheprescription93.has_date_bilan_mi_parcours', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Ficheprescription93.exists' ), 'domain' => 'fichesprescriptions93', 'empty' => true ) );
	echo $this->Xform->input( 'Search.Ficheprescription93.has_date_bilan_final', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Ficheprescription93.exists' ), 'domain' => 'fichesprescriptions93', 'empty' => true ) );

	echo '</fieldset>';
?>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);

	echo $this->Observer->disableFieldsetOnValue(
		'Search.Ficheprescription93.exists',
		'SpecificitesFichesprescriptions93',
		array( null, '1' ),
		true,
		true
	);

	// Catalogue PDI
	echo $this->Observer->disableFieldsOnValue(
		'Search.Ficheprescription93.typethematiquefp93_id',
		array(
			'Search.Ficheprescription93.prestatairehorspdifp93_id',
			'Search.Ficheprescription93.actionfp93',
		),
		array( null, '', 'pdi' ),
		true,
		true
	);

	// Catalogue Hors PDI
	echo $this->Observer->disableFieldsOnValue(
		'Search.Ficheprescription93.typethematiquefp93_id',
		array(
			'Search.Ficheprescription93.numconvention',
			'Search.Ficheprescription93.prestatairefp93_id',
			'Search.Ficheprescription93.actionfp93_id',
		),
		array( 'horspdi' ),
		true,
		true
	);

	echo $this->Ajax2->observe(
		array(
			'Search.Ficheprescription93.numconvention' => array( 'event' => 'keyup' ),
			'Search.Ficheprescription93.typethematiquefp93_id',
			'Search.Ficheprescription93.yearthematiquefp93_id',
			'Search.Ficheprescription93.thematiquefp93_id',
			'Search.Ficheprescription93.categoriefp93_id',
			'Search.Ficheprescription93.filierefp93_id',
			'Search.Ficheprescription93.prestatairefp93_id',
			'Search.Ficheprescription93.actionfp93_id',
		),
		array(
			'url' => array( 'action' => 'ajax_action' ),
			'prefix' => 'Search',
			'onload' => !empty( $this->request->data )
		)
	);

	echo $this->Observer->dependantSelect( array( 'Search.Ficheprescription93.structurereferente_id' => 'Search.Ficheprescription93.referent_id' ) );
?>

<script type="text/javascript">
// -------------------------------------------------------------------------
	// Initialisation des filtres à appliquer sur la table d'impressions, observation
	// des champs de formulaire.
	// @fixme
	//	- filtre par date
	// -------------------------------------------------------------------------
	$('SearchFicheprescription93CreatedFromYear').observe( 'change', function() {
		checkDateCorrespondance( );
		return false;
	} );
	$('SearchFicheprescription93CreatedToYear').observe( 'change', function() {
		checkDateCorrespondance( );
		return false;
	} );
	$('SearchFicheprescription93DateSignatureFromYear').observe( 'change', function() {
		checkDateCorrespondance( );
		return false;
	} );
	$('SearchFicheprescription93DateSignatureToYear').observe( 'change', function() {
		checkDateCorrespondance( );
		return false;
	} );


	// Suppression du contenu du champ Year au changement de Type
	Element.observe( $( 'SearchFicheprescription93Typethematiquefp93Id' ), 'click', function( event ) {
			$( 'SearchFicheprescription93Yearthematiquefp93Id' ).value = '';
	} );
	// Suppression du contenu du champ Prescription au changement de Year
	Element.observe( $( 'SearchFicheprescription93Yearthematiquefp93Id' ), 'click', function( event ) {
			$( 'SearchFicheprescription93Thematiquefp93Id' ).value = '';
	} );

	/**
	 * Filtre des lignes de la table d'actions par type d'action suivant la valeur
	 * du champ de liste déroulante (et de la plage de dates le cas échéant).
	 *
	 * @param {String} table L'id de la table à traiter
	 * @returns {undefined}
	 */
		function checkDateCorrespondance( ) {

		var SelectYearthematique = $('SearchFicheprescription93Yearthematiquefp93Id');
		var Yearthematique = SelectYearthematique[SelectYearthematique.selectedIndex].value;

		//var Yearthematique = '2016';
		//console.log('start');
		// $(SelectCreatedToYear).up( 'div' ).removeClassName( 'error' );
		if (Yearthematique != '') {
			var SelectCreatedFromYear = $('SearchFicheprescription93CreatedFromYear');
		    var CreatedFromYearVal = SelectCreatedFromYear[SelectCreatedFromYear.selectedIndex].value;

		    var SelectCreatedToYear = $('SearchFicheprescription93CreatedToYear');
		    var CreatedToYearVal = SelectCreatedToYear[SelectCreatedToYear.selectedIndex].value;

			if ( (CreatedFromYearVal <= Yearthematique ) && (Yearthematique <= CreatedToYearVal) ) {
				var SelectCreatedFrom = $('SearchFicheprescription93Created_from_to');
				$(SelectCreatedFrom).removeClassName( 'error' );
			}else{
				var SelectCreatedFrom = $('SearchFicheprescription93Created_from_to');
				$(SelectCreatedFrom).addClassName( 'error' );
			}

			var SelectSignatureFromYear = $('SearchFicheprescription93DateSignatureFromYear');
			var SignatureFromYearVal = SelectSignatureFromYear[SelectSignatureFromYear.selectedIndex].value;

			var SelectSignatureToYear = $('SearchFicheprescription93DateSignatureToYear');
			var SignatureToYearVal = SelectSignatureToYear[SelectSignatureToYear.selectedIndex].value;

			if ( (SignatureFromYearVal <= Yearthematique ) && (Yearthematique <= SignatureToYearVal) ) {
				var SelectCreatedFrom = $('SearchFicheprescription93DateSignature_from_to');
				$(SelectCreatedFrom).removeClassName( 'error' );
			}else{
				var SelectCreatedFrom = $('SearchFicheprescription93DateSignature_from_to');
				$(SelectCreatedFrom).addClassName( 'error' );
			}
		}
	}
</script>