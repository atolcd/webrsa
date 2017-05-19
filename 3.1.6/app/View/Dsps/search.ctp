<?php $departement = Configure::read( 'Cg.departement' );?>
<?php $this->start( 'custom_search_filters' );?>
<fieldset>
	<legend>Données socio-professionnelles</legend>
	<?php
		if( $departement == 93 ) {
			echo $this->Form->input( 'Search.Detaildifsoc.difsoc', array( 'label' => 'Difficultés sociales', 'type' => 'select', 'options' => $options['Detaildifsoc']['difsoc'], 'empty' => true ) );
			echo $this->Form->input( 'Search.Detailaccosocindi.nataccosocindi', array( 'label' => 'Domaine d\'accompagnement individuel', 'type' => 'select', 'options' => $options['Detailaccosocindi']['nataccosocindi'], 'empty' => true ) );
			echo $this->Form->input( 'Search.Detaildifdisp.difdisp', array( 'label' => 'Obstacles à la recherche d\'emploi', 'type' => 'select', 'options' => $options['Detaildifdisp']['difdisp'], 'empty' => true ) );
		}
	?>

	<fieldset>
		<legend>Situation professionnelle</legend>
		<?php
			echo $this->Form->input( 'Search.Dsp.nivetu', array( 'label' => "Quelle est votre niveau d'étude ? ", 'type' => 'select', 'options' => $options['Donnees']['nivetu'], 'empty' => true ) );
			echo $this->Form->input( 'Search.Dsp.hispro', array( 'label' => "Passé professionnel ", 'type' => 'select', 'options' => $options['Donnees']['hispro'], 'empty' => true ) );

			echo $this->Romev3->fieldset( 'Deractromev3', array( 'options' => $options, 'prefix' => 'Search' ) );
			echo $this->Form->input( 'Search.Dsp.libsecactderact', array( 'label' => __d( 'dsp', 'Dsp.libsecactderact' ) ) );
			echo $this->Form->input( 'Search.Dsp.libderact', array( 'label' => __d( 'dsp', 'Dsp.libderact' ) ) );

			if( $departement == 66 ) {
				echo '<fieldset><legend>Dernière activité (ROME V2)</legend>';
					echo $this->Form->input( 'Search.Dsp.libsecactderact66_secteur_id' , array( 'label' => "Dans quel secteur d'activité avez-vous exercé votre activité professionnelle ? ", 'type' => 'select', 'options' => $options['Coderomesecteurdsp66'], 'empty' => true ) );
					echo $this->Form->input( 'Search.Dsp.libderact66_metier_id' , array( 'label' => "Précisez quelle a été l'activité professionnelle ? ", 'type' => 'select', 'options' => $options['Coderomemetierdsp66'], 'empty' => true ) );
				echo '</fieldset>';
			}

			if( $departement != 93 ) {
				echo $this->Romev3->fieldset( 'Deractdomiromev3', array( 'options' => $options, 'prefix' => 'Search' ) );
			}

			echo $this->Form->input( 'Search.Dsp.libsecactdomi', array( 'label' => __d( 'dsp', 'Dsp.libsecactdomi' ) ) );
			echo $this->Form->input( 'Search.Dsp.libactdomi', array( 'label' => __d( 'dsp', 'Dsp.libactdomi' ) ) );

			if( $departement == 66 ) {
				echo '<fieldset><legend>Dernière activité dominante (ROME V2)</legend>';
					echo $this->Form->input( 'Search.Dsp.libsecactdomi66_secteur_id' , array( 'label' => "Dans quel secteur d'activité avez-vous exercé votre activité professionnelle dominante ? ", 'type' => 'select', 'options' => $options['Coderomesecteurdsp66'], 'empty' => true ) );
					echo $this->Form->input( 'Search.Dsp.libactdomi66_metier_id' , array( 'label' => "Précisez quelle a été l'activité professionnelle dominante ? ", 'type' => 'select', 'options' => $options['Coderomemetierdsp66'], 'empty' => true ) );
				echo '</fieldset>';
			}

			echo $this->Romev3->fieldset( 'Actrechromev3', array( 'options' => $options, 'prefix' => 'Search' ) );
			echo $this->Form->input( 'Search.Dsp.libsecactrech', array( 'label' => __d( 'dsp', 'Dsp.libsecactrech' ) ) );
			echo $this->Form->input( 'Search.Dsp.libemploirech', array( 'label' => __d( 'dsp', 'Dsp.libemploirech' ) ) );

			if( $departement == 66 ) {
				echo '<fieldset><legend>Emploi recherché (ROME V2)</legend>';
					echo $this->Form->input( 'Search.Dsp.libsecactrech66_secteur_id' , array('label' => "Quel est le secteur d'activité recherché ? ",  'type' => 'select', 'options' => $options['Coderomesecteurdsp66'], 'empty' => true ) );
					echo $this->Form->input( 'Search.Dsp.libemploirech66_metier_id' , array( 'label' => "Quel est l'emploi recherché ? ", 'type' => 'select', 'options' => $options['Coderomemetierdsp66'], 'empty' => true ) );
				echo '</fieldset>';
			}
		?>
	</fieldset>
</fieldset>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'modelName' => 'Personne',
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);
?>
<script type="text/javascript">
<?php if ( $departement == 66 ):?>
	document.observe("dom:loaded", function() {
 		dependantSelect( 'SearchDspLibderact66MetierId', 'SearchDspLibsecactderact66SecteurId' );
 		try { $( 'SearchDspLibderact66MetierId' ).onchange(); } catch(id) { }

		dependantSelect( 'SearchDspLibactdomi66MetierId', 'SearchDspLibsecactdomi66SecteurId' );
		try { $( 'SearchDspLibactdomi66MetierId' ).onchange(); } catch(id) { }

		dependantSelect( 'SearchDspLibemploirech66MetierId', 'SearchDspLibsecactrech66SecteurId' );
		try { $( 'SearchDspLibemploirech66MetierId' ).onchange(); } catch(id) { }
	} );
<?php endif;?>
</script>