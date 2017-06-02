<?php
	$this->pageTitle = 'Structures référentes';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Structurereferente', array( 'type' => 'post' ) );
		echo '<div>';
		echo $this->Form->input( 'Structurereferente.id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Zonegeographique.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
	else {
		echo $this->Form->create( 'Structurereferente', array( 'type' => 'post' ) );
		echo '<div>';
		echo $this->Form->input( 'Structurereferente.id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Zonegeographique.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
?>

<fieldset>
	<?php echo $this->Form->input( 'Structurereferente.lib_struc', array( 'label' => required( __m( 'Structurereferente.lib_struc' ) ), 'type' => 'text' ) );?>
	<?php echo $this->Form->input( 'Structurereferente.num_voie', array( 'label' => required( __( 'num_voie' ) ), 'type' => 'text', 'maxlength' => 15 ) );?>
	<?php echo $this->Form->input( 'Structurereferente.type_voie', array( 'label' => required( __( 'type_voie' ) ), 'type' => 'select', 'options' => $typevoie, 'empty' => true ) );?>
	<?php echo $this->Form->input( 'Structurereferente.nom_voie', array( 'label' => required(  __( 'nom_voie' ) ), 'type' => 'text', 'maxlength' => 50 ) );?>
	<?php echo $this->Form->input( 'Structurereferente.code_postal', array( 'label' => required( __( 'code_postal' ) ), 'type' => 'text', 'maxlength' => 5 ) );?>
	<?php echo $this->Form->input( 'Structurereferente.ville', array( 'label' => required( __( 'ville' ) ), 'type' => 'text' ) );?>
	<?php echo $this->Form->input( 'Structurereferente.code_insee', array( 'label' => required( __( 'code_insee' ) ), 'type' => 'text', 'maxlength' => 5 ) );?>
	<?php echo $this->Form->input( 'Structurereferente.numtel', array( 'label' => __( 'numtel' ), 'type' => 'text', 'maxlength' => 19 ) );?>
	<?php echo $this->Form->input( 'Structurereferente.numfax', array( 'label' => __( 'numfax' ), 'type' => 'text', 'maxlength' => 19 ) );?>
</fieldset>
<div><?php echo $this->Form->input( 'Structurereferente.filtre_zone_geo', array( 'label' => 'Restreindre les zones géographiques', 'type' => 'checkbox' ) );?></div>


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
	<script type="text/javascript">
		document.observe( "dom:loaded", function() {
			observeDisableFieldsetOnCheckbox( 'StructurereferenteFiltreZoneGeo', 'filtres_zone_geo', false );
		} );
	</script>
	<?php echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => "return toutCocherZonesgeographiques();" ) );?>
	<?php echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => "return toutDecocherZonesgeographiques();" ) );?>

	<?php echo $this->Form->input( 'Zonegeographique.Zonegeographique', array( 'label' => false, 'multiple' => 'checkbox' , 'options' => $zglist ) );?>
</fieldset>
	<?php
		echo $this->Xform->input( 'Structurereferente.typeorient_id', array( 'label' => required( 'Type d\'orientation' ), 'type' => 'select' , 'options' => $options['Structurereferente']['typeorient_id'], 'empty' => true ) );

		echo $this->Xform->inputs(
			array(
				'fieldset' => true,
				'legend' => 'Gère les CERs ?',
				'Structurereferente.contratengagement' => array( 'label' => required( __d( 'structurereferente', 'Structurereferente.contratengagement' ) ), 'type' => 'select', 'options' => $options['Structurereferente']['contratengagement'], 'empty' => true )
			)
		);
		echo $this->Xform->inputs(
			array(
				'fieldset' => true,
				'legend' => 'Gère les APREs ?',
				'Structurereferente.apre' => array( 'label' => required( __d( 'structurereferente', 'Structurereferente.apre' ) ), 'type' => 'select', 'options' => $options['Structurereferente']['apre'], 'empty' => true )
			)
		);
		echo $this->Xform->inputs(
			array(
				'fieldset' => true,
				'legend' => 'Gère les Orientations ?',
				'Structurereferente.orientation' => array( 'label' => required( 'Gestion des orientations' ), 'type' => 'select', 'options' => $options['Structurereferente']['orientation'], 'empty' => true )
			)
		);
		echo $this->Xform->inputs(
			array(
				'fieldset' => true,
				'legend' => 'Gère les PDOs ?',
				'Structurereferente.pdo' => array( 'label' => required( 'Gestion des PDOs' ), 'type' => 'select', 'options' => $options['Structurereferente']['pdo'], 'empty' => true )
			)
		);
		echo $this->Xform->inputs(
			array(
				'fieldset' => true,
				'legend' => 'Active ?',
				'Structurereferente.actif' => array( 'label' => required( 'Structure active' ), 'type' => 'select', 'options' => $options['Structurereferente']['actif'], 'empty' => true )
			)
		);
		echo $this->Xform->inputs(
			array(
				'fieldset' => true,
				'legend' => 'Type de structure',
				'Structurereferente.typestructure' => array( 'label' => required( 'Type de structure' ), 'type' => 'select', 'options' => $options['Structurereferente']['typestructure'], 'empty' => true )
			)
		);
		echo $this->Xform->inputs(
			array(
				'fieldset' => true,
				'legend' => 'Gère les CUIs ?',
				'Structurereferente.cui' => array( 'label' => required( __d( 'structurereferente', 'Structurereferente.cui' ) ), 'type' => 'select', 'options' => $options['Structurereferente']['cui'], 'empty' => true )
			)
		);
	?>

	<div class="submit">
		<?php
			echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
			echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>
<?php echo $this->Form->end();?>