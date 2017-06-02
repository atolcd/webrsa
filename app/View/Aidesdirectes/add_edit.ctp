<?php
	$this->pageTitle = 'Aides pour un contrat';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>


<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'une aide';
	}
	else {
		$this->pageTitle = 'Aides d\'insertion ';
	}
?>

<h1><?php echo 'Ajout d\'une aide pour le contrat ';?></h1><!-- FIXME -->

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Aidedirecte', array( 'type' => 'post', 'novalidate' => true ) );
		echo '<div>';
		echo $this->Form->input( 'Aidedirecte.id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Aidedirecte.actioninsertion_id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
	else {
		echo $this->Form->create( 'Aidedirecte', array( 'type' => 'post', 'novalidate' => true ) );
		echo '<div>';
		echo $this->Form->input( 'Aidedirecte.id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Aidedirecte.actioninsertion_id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
?>

<fieldset>
	<?php echo $this->Form->input( 'Aidedirecte.typo_aide', array( 'label' => required( __d( 'action', 'Action.typo_aide' ) ), 'type' => 'select', 'options' => $typo_aide, 'empty' => true )  ); ?>
	<?php echo $this->Form->input( 'Aidedirecte.lib_aide', array( 'label' => required( __d( 'action', 'Action.lib_aide' ) ), 'type' => 'select', 'options' => $actions, 'empty' => true )  ); ?>
	<?php echo $this->Form->input( 'Aidedirecte.date_aide', array( 'label' => required( __d( 'action', 'Action.date_aide' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+10, 'minYear'=>date('Y')-10 , 'empty' => true)  ); ?>
</fieldset>

<?php echo $this->Form->submit( 'Enregistrer' );?>

<?php echo $this->Form->end();?>