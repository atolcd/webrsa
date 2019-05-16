<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default3->titleForLayout( $this->request->data );

	echo $this->Default3->DefaultForm->create();
?>

<fieldset>
	<legend>Identification</legend>
	<?php
		echo $this->Default3->subform(
			$this->Translator->normalize(
				array(
					'Structurereferente.id',
					'Zonegeographique.id',
					'Structurereferente.lib_struc',
					'Structurereferente.lib_struc_mini',
					'Structurereferente.num_voie',
					'Structurereferente.type_voie' => array( 'empty' => true ),
					'Structurereferente.nom_voie',
					'Structurereferente.code_postal',
					'Structurereferente.ville',
					'Structurereferente.code_insee',
					'Structurereferente.numtel',
					'Structurereferente.numfax'
				)
			),
			array(
				'options' => $options
			)
		);
	?>
</fieldset>
<div><?php echo $this->Form->input( 'Structurereferente.filtre_zone_geo', array( 'label' => 'Restreindre les zones géographiques', 'type' => 'checkbox' ) );?></div>
<fieldset class="col2" id="filtres_zone_geo">
	<legend>Zones géographiques</legend>
	<script type="text/javascript">
		document.observe( "dom:loaded", function() {
			observeDisableFieldsetOnCheckbox( 'StructurereferenteFiltreZoneGeo', 'filtres_zone_geo', false );
		} );
	</script>
	<?php echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => "return toutCocherZonesgeographiques();" ) );?>
	<?php echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => "return toutDecocherZonesgeographiques();" ) );?>

	<?php echo $this->Form->input( 'Zonegeographique.Zonegeographique', array( 'label' => false, 'multiple' => 'checkbox' , 'options' => $options['Zonegeographique']['Zonegeographique'] ) );?>
</fieldset>
<fieldset>
	<legend>Type de structure référente</legend>
<?php
	echo $this->Default3->subform(
		$this->Translator->normalize(
			array(
				'Structurereferente.typeorient_id' => array( 'empty' => true ),
				'Structurereferente.typestructure' => array( 'empty' => true ),
				'Structurereferente.actif' => array( 'empty' => true ),
				'Structurereferente.actif_cohorte' => array( 'empty' => false ),
				'Structurereferente.dreesorganisme_id' => array( 'empty' => true ),
			)
		),
		array(
			'options' => $options
		)
	);
?>
</fieldset>
<fieldset>
	<legend>Géré par la structure référente</legend>
<?php
	echo $this->Default3->subform(
		$this->Translator->normalize(
			array(
				'Structurereferente.contratengagement' => array( 'empty' => true ),
				'Structurereferente.apre' => array( 'empty' => true ),
				'Structurereferente.orientation' => array( 'empty' => true ),
				'Structurereferente.pdo' => array( 'empty' => true ),
				'Structurereferente.cui' => array( 'empty' => true )
			)
		),
		array(
			'options' => $options
		)
	);
?>
</fieldset>
<?php
	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit();
?>
<script type="text/javascript">
	function toutCocherZonesgeographiques() {
		return toutCocher( 'input[name="data[Zonegeographique][Zonegeographique][]"]' );
	}
	function toutDecocherZonesgeographiques() {
		return toutDecocher( 'input[name="data[Zonegeographique][Zonegeographique][]"]' );
	}
</script>