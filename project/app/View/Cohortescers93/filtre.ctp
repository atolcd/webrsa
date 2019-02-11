<!-- Début du filtre-->
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "var form = $$( 'form' ); form = form[0]; $( form ).toggle(); return false;" )
	).'</li></ul>';
?>
<?php echo $this->Xform->create( null, array( 'type' => 'post', 'url' => array( 'action' => $this->action ), 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) && isset( $this->request->data['Search']['active'] ) ) ? 'folded' : 'unfolded' ), 'novalidate' => true ) );?>
	<?php
		echo $this->Xform->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) );

		echo $this->Allocataires->communautesrSelect( 'PersonneReferent', array( 'options' => array( 'Search' => $options ), 'hide' => false, 'label' => 'Projet insertion emploi territorial d\'affectation' ) );
		echo $this->Form->input( 'Search.PersonneReferent.referent_id', array( 'label' => 'Affectation', 'type' => 'select', 'options' => $options['referents'], 'empty' => true ) );
		echo $this->Search->date( 'Search.PersonneReferent.dddesignation', 'Date d\'affectation' );

		echo $this->Allocataires->communautesrScriptReferent( 'PersonneReferent', array( 'options' => array( 'Search' => $options ), 'hide' => false ) );

		echo $this->Search->blocAllocataire( array(), array(), 'Search' );
		echo $this->Search->toppersdrodevorsa( $options['toppersdrodevorsa'], 'Search.Calculdroitrsa.toppersdrodevorsa' );
		echo $this->Form->input( 'Search.Dsp.exists', array( 'label' => 'Possède une DSP ?', 'type' => 'select', 'options' => $options['exists'], 'empty' => true ) );
		echo $this->Form->input( 'Search.Contratinsertion.exists', array( 'label' => 'Possède un CER ?', 'type' => 'select', 'options' => $options['exists'], 'empty' => true ) );
		if( $this->action == 'visualisation' ) {
			echo $this->Form->input( 'Search.Contratinsertion.dernier', array( 'label' => 'Uniquement le dernier CER en cours pour un même allocataire', 'type' => 'checkbox' ) );
		}

		if( in_array( $this->action, array( 'validationcs', 'validationcadre' ) ) ) {
			echo '<fieldset><legend>Mode d\'opération</legend>';
			echo $this->Form->input( 'Search.Cer93.mode_operation', array( 'type' => 'radio', 'options' => array( 'traitement' => 'Traitement', 'impression' => 'Impression' ), 'default' => 'traitement', 'legend' => false ) );
			// Traitement
			echo '<div id="SearchCer93PositioncerTraitement">'.$this->Search->statutCER93( $options['Cer93']['positioncer'], 'Search.Cer93.positioncer' ).'</div>';
			// Impression
			echo '<div id="SearchCer93PositioncerImpression">';
			echo $this->Form->input( 'Search.Cer93.positioncer', array( 'type' => 'select', 'label' => 'Statut du CER', 'options' => (array)Hash::get( $options, 'Search.Cer93.positioncer' ) ) );
			echo $this->Form->input( 'Search.Cer93.limit', array( 'type' => 'select', 'label' => 'Nombre de résultats par page', 'options' => (array)Hash::get( $options, 'Search.Cer93.limit' ) ) );
			echo $this->Search->date( 'Search.Contratinsertion.datedecision' );
			echo $this->Form->input( 'Search.Cer93.hasdateimpression', array( 'label' => 'Filtrer par impression', 'type' => 'select', 'options' => (array)Hash::get( $options, 'Search.Cer93.hasdateimpression' ), 'empty' => true ) );
			echo $this->Search->date( 'Search.Cer93.dateimpressiondecision' );
			echo '</div>';
			echo '</fieldset>';
		}
		else {
			echo $this->Search->statutCER93( $options['Cer93']['positioncer'], 'Search.Cer93.positioncer' );
		}

		if( in_array( $this->action, array( 'premierelecture', 'validationcs', 'validationcadre' ) ) ) {
			echo $this->Search->date( 'Search.Contratinsertion.created' );
		}

		if( in_array( $this->action, array( 'avalidercpdv', 'premierelecture', 'validationcs', 'validationcadre' ) ) ) {
			echo $this->Search->date( 'Search.Cer93.datesignature' );
		}


		echo $this->Search->date( 'Search.Orientstruct.date_valid' );

		echo $this->Allocataires->blocAdresse( array( 'options' => $options ) );

		echo $this->Search->blocDossier( $options['etatdosrsa'], 'Search' );


		echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );
		echo $this->Search->observeDisableFormOnSubmit( 'Search' );
	?>
	<div class="submit noprint">
		<?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Xform->end();?>
<?php if( in_array( $this->action, array( 'validationcs', 'validationcadre' ) ) ):?>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		observeDisableFieldsetOnRadioValue(
			'Search',
			'data[Search][Cer93][mode_operation]',
			$( 'SearchCer93PositioncerTraitement' ),
			'traitement',
			true,
			true
		);

		observeDisableFieldsetOnRadioValue(
			'Search',
			'data[Search][Cer93][mode_operation]',
			$( 'SearchCer93PositioncerImpression' ),
			'impression',
			true,
			true
		);
	} );
</script>
<?php endif;?>
<!-- Fin du filtre-->