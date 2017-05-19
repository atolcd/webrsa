<?php
	$this->pageTitle = 'Actions d\'insertion';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>

<?php
	echo $this->Form->create( 'Action', array( 'type' => 'post' ) );
	if( $this->action == 'edit' ) {
		echo $this->Form->input( 'Action.id', array( 'type' => 'hidden' ) );
	}
?>

<fieldset>
	<?php echo $this->Form->input( 'Action.code', array( 'label' =>  required( __d( 'action', 'Action.code_action' ) ), 'type' => 'text', 'maxlength' => 2 ) );?>
	<?php echo $this->Form->input( 'Action.libelle', array( 'label' =>  required( __d( 'action', 'Action.lib_action' ) ), 'type' => 'text' ) );?>
	<?php echo $this->Form->input( 'Action.typeaction_id', array( 'label' =>  required( __d( 'action', 'Action.type_action' ) ), 'type' => 'select', 'options' => $libtypaction, 'empty' => true ) );?>
</fieldset>

	<?php echo $this->Form->submit( 'Enregistrer' );?>
<?php echo $this->Form->end();?>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'actions',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>