<?php
	$this->pageTitle = 'Question PDO';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>

<fieldset>
<?php
	echo $this->Form->create( 'Questionpcg66', array( 'type' => 'post' ) );

	echo $this->Default2->subform(
		array(
			'Questionpcg66.id' => array( 'type'=>'hidden' ),
			'Questionpcg66.defautinsertion' => array( 'required' => true, 'type' => 'select', 'empty' => true ),
			'Questionpcg66.compofoyerpcg66_id' => array( 'required' => true, 'type' => 'select', 'empty' => true, 'options' => $options['Compofoyerpcg66'] ),
			'Questionpcg66.recidive' => array( 'required' => true, 'type' => 'radio' ),
			'Questionpcg66.phase' => array( 'required' => true, 'type' => 'select', 'empty' => true ),
			'Questionpcg66.decisionpcg66_id' => array( 'required' => true, 'type' => 'select', 'empty' => true, 'options' => $options['Decisionpcg66'] )
		),
		array(
			'options' => $options
		)
	);
?>
<div class="submit">
	<?php
		echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
		echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
	?>
</div>
</fieldset>