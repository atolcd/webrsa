<?php
	$this->pageTitle = 'Décisions PDOs';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<fieldset>
	<?php
		echo $this->Form->create( 'Decisionpcg66', array( 'type' => 'post' ) );

		echo $this->Default2->subform(
			array(
				'Decisionpcg66.id' => array( 'type'=>'hidden' ),
				'Decisionpcg66.name' => array( 'required' => true ),
				'Decisionpcg66.nbmoisecheance' => array( 'required' => true ),
				'Decisionpcg66.courriernotif'
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