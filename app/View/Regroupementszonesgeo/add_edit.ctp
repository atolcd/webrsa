<?php
	$this->pageTitle = 'Regroupements en région';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

	<?php
		if( $this->action == 'add' ) {
			echo $this->Form->create( 'Regroupementzonegeo', array( 'type' => 'post' ) );
			echo $this->Form->input( 'Regroupementzonegeo.id', array( 'type' => 'hidden', 'value' => null ) );
		}
		else {
			echo $this->Form->create( 'Regroupementzonegeo', array( 'type' => 'post' ) );
			echo $this->Form->input( 'Regroupementzonegeo.id', array( 'type' => 'hidden' ) );
		}
	?>
	<fieldset>
		<?php echo $this->Form->input( 'Regroupementzonegeo.lib_rgpt', array( 'label' =>  required( __( 'lib_rgpt' ) ), 'type' => 'text' ) );?>
	</fieldset>

	<fieldset class="col2">
		<legend>Zones géographiques</legend>
		<script type="text/javascript">
			document.observe( "dom:loaded", function() {
			} );
		</script>
		<?php echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => "return toutCocher();" ) );?>
		<?php echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => "return toutDecocher();" ) );?>

		<?php echo $this->Form->input( 'Zonegeographique.Zonegeographique', array( 'label' => false, 'multiple' => 'checkbox' , 'options' => $zglist ) );?>
	</fieldset>

	<?php echo $this->Form->submit( 'Enregistrer' );?>
<?php echo $this->Form->end();?>