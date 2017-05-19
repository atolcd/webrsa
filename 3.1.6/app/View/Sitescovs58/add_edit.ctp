<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'sitecov58', "Sitescovs58::{$this->action}" )
	);

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Sitecov58', array( 'type' => 'post' ) );
		echo $this->Form->input( 'Sitecov58.id', array( 'type' => 'hidden', 'value' => null ) );
	}
	else {
		echo $this->Form->create( 'Sitecov58', array( 'type' => 'post' ) );
		echo $this->Form->input( 'Sitecov58.id', array( 'type' => 'hidden' ) );
	}

	echo $this->Default2->subform(
		array(
			'Sitecov58.name' => array( 'required' => true, 'type' => 'text' )
		)
	);
	?>

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
	<?php echo $this->Form->submit( 'Enregistrer' );?>

		<?php echo $this->Default->button(
		'back',
		array(
			'controller' => 'sitescovs58',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>
<?php echo $this->Form->end();?>
