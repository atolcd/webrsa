<?php
	$this->pageTitle = 'Types de notification PDO';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Typenotifpdo', array( 'type' => 'post' ) );
		echo $this->Form->input( 'Typenotifpdo.id', array( 'type' => 'hidden', 'value' => '' ) );
	}
	else {
		echo $this->Form->create( 'Typenotifpdo', array( 'type' => 'post' ) );
		echo $this->Form->input( 'Typenotifpdo.id', array( 'type' => 'hidden' ) );
	}
?>

<fieldset>
	<?php echo $this->Form->input( 'Typenotifpdo.libelle', array( 'label' => required( __( 'Type de notification' ) ), 'type' => 'text' ) );?>
	<?php echo $this->Form->input( 'Typenotifpdo.modelenotifpdo', array( 'label' => required( __( 'Modèle de notification' ) ), 'type' => 'text' ) );?>
</fieldset>

	<?php echo $this->Form->submit( 'Enregistrer' );?>
<?php echo $this->Form->end();?>

<div class="clearer"><hr /></div>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'typesnotifspdos',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>