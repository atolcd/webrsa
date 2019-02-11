<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'une prestation';
	}
	else {
		$this->pageTitle = 'Prestations d\'insertion ';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo 'Ajout d\'une prestation pour le contrat ';?></h1>
<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Prestform',array( 'type' => 'post', 'novalidate' => true ) );
		echo '<div>';
		echo $this->Form->input( 'Prestform.id', array( 'type' => 'hidden') );
		echo $this->Form->input( 'Prestform.actioninsertion_id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Prestform.refpresta_id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Refpresta.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
	else {
		echo $this->Form->create( 'Prestform',array( 'type' => 'post', 'novalidate' => true ) );
		echo '<div>';
		echo $this->Form->input( 'Prestform.id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Prestform.actioninsertion_id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Prestform.refpresta_id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Refpresta.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
?>

<fieldset>
	<?php echo $this->Form->input( 'Prestform.lib_presta', array( 'label' => required( __d( 'action', 'Action.lib_presta' ) ), 'type' => 'select', 'options' => $actions, 'empty' => true )  ); ?>
	<?php echo $this->Form->input( 'Refpresta.nomrefpresta', array( 'label' => required( __d( 'action', 'Action.nomrefpresta' ) ), 'type' => 'text')); ?>
	<?php echo $this->Form->input( 'Prestform.date_presta', array( 'label' => required( __d( 'action', 'Action.date_presta' ) ), 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear'=>date('Y')+10, 'minYear'=>date('Y')-10 , 'empty' => true )  ); ?>
</fieldset>

<?php echo $this->Form->submit( 'Enregistrer' );?>
<?php echo $this->Form->end();?>