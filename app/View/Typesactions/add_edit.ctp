<?php
	$this->pageTitle = 'Type d\'actions d\'insertion';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Typeaction', array( 'type' => 'post' ) );
	}
	else {
		echo $this->Form->create( 'Typeaction', array( 'type' => 'post' ) );
		echo $this->Form->input( 'Typeaction.id', array( 'type' => 'hidden' ) );
	}
?>

<fieldset>
	<?php echo $this->Form->input( 'Typeaction.libelle', array( 'label' =>  required( __d( 'action', 'Action.lib_action' ) ), 'type' => 'text' ) );?>
</fieldset>

	<div class="submit">
		<?php
			echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
			echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>
<?php echo $this->Form->end();?>
