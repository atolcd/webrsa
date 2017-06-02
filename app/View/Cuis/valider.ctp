<?php
	$this->pageTitle = 'Validation du CUI';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>

<?php  echo $this->Form->create( 'Cui',array( 'novalidate' => true ) ); ?>
	<fieldset>
		<?php echo $this->Xform->input( 'Cui.id', array( 'type' => 'hidden'/*, 'value' => $personne_id*/ ) );?>
		<?php echo $this->Xform->input( 'Cui.personne_id', array( 'type' => 'hidden', 'value' => $personne_id ) );?>
		<?php echo $this->Xform->input( 'Cui.structurereferente_id', array( 'type' => 'hidden' ) );?>

		<?php echo $this->Xform->input( 'Cui.observcui', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.observ_ci' ), 'type' => 'textarea', 'rows' => 6)  ); ?>
		<?php echo $this->Xform->input( 'Cui.decisioncui', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.decision_ci' ), 'type' => 'select', 'options' => $options['Cui']['decisioncui'], 'empty' => true ) ); ?>
		<?php echo $this->Xform->input( 'Cui.datevalidationcui', array( 'label' => '', 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+2, 'minYear'=>date('Y')-2 , 'empty' => true)  ); ?>
	</fieldset>
<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>