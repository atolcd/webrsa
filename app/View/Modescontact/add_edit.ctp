<?php
    if( $this->action == 'add' ) {
        $this->pageTitle = 'Ajout d\'un mode de contact';
    }
    else {
        $this->pageTitle = 'Édition des modes de contact';
        $foyer_id = $this->request->data['Modecontact']['foyer_id'];
    }

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Modecontact', array( 'type' => 'post' ));
	}
	else {
		echo $this->Form->create( 'Modecontact', array( 'type' => 'post' ) );
		echo '<div>';
		echo $this->Form->input( 'Modecontact.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
?>
<div>
	<?php echo $this->Form->input( 'Modecontact.foyer_id', array( 'type' => 'hidden', 'div' => 'div', 'value' => $foyer_id ) );?>
</div>
<?php echo $this->Form->input( 'Modecontact.numtel', array( 'label' =>  __( 'numtel' ) ) );?>
<?php echo $this->Form->input( 'Modecontact.numposte', array( 'label' => __d( 'modecontact', 'Modecontact.numposte' ), 'maxlength' => 4 ) );?>
<?php echo $this->Form->input( 'Modecontact.nattel', array( 'label' =>  __d( 'modecontact', 'Modecontact.nattel' ), 'type' => 'select', 'options' => $options['Modecontact']['nattel'], 'empty' => true  ) );?>
<?php echo $this->Form->input( 'Modecontact.matetel', array( 'label' => __d( 'modecontact', 'Modecontact.matetel' ), 'type' => 'select', 'options' => $options['Modecontact']['matetel'], 'empty' => true  ) );?>
<?php echo $this->Form->input( 'Modecontact.autorutitel', array( 'label' => __d( 'modecontact', 'Modecontact.autorutitel' ), 'type' => 'select', 'options' => $options['Modecontact']['autorutitel'], 'empty' => true  ) );?>
<?php echo $this->Form->input( 'Modecontact.adrelec', array( 'label' => __d( 'modecontact', 'Modecontact.adrelec' ) ) );?>
<?php echo $this->Form->input( 'Modecontact.autorutiadrelec', array( 'label' => __d( 'modecontact', 'Modecontact.autorutiadrelec' ), 'type' => 'select', 'options' => $options['Modecontact']['autorutiadrelec'], 'empty' => true  ) );?>

<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>