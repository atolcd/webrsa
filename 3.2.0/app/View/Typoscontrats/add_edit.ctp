<?php
	$this->pageTitle = 'Type de contrats d\'insertion';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Typocontrat', array( 'type' => 'post', 'novalidate' => true ) );
	}
	else {
		echo $this->Form->create( 'Typocontrat', array( 'type' => 'post', 'novalidate' => true ) );
		echo $this->Form->input( 'Typocontrat.id', array( 'type' => 'hidden' ) );
	}
?>

	<fieldset>
		<?php echo $this->Form->input( 'Typocontrat.lib_typo', array( 'label' => required( __( 'lib_typo' ) ), 'type' => 'text' ) );?>
	</fieldset>

	<?php echo $this->Form->submit( 'Enregistrer' );?>
<?php echo $this->Form->end();?>
