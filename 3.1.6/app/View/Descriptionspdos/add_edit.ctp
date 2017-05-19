<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'descriptionpdo', "Descriptionspdos::{$this->action}" )
	)
?>
<?php
	$sensibilite = Set::classicExtract( $this->request->data, 'Descriptionpdo.sensibilite' );
	if( empty( $sensibilite ) ) {
		$sensibilite = 'N';
	}
	echo $this->Xform->create( );
	
	echo $this->Default2->subform(
		array(
			'Descriptionpdo.id' => array( 'type' => 'hidden' ),
			'Descriptionpdo.name' => array( 'required' => true ),
			'Descriptionpdo.modelenotification',
			'Descriptionpdo.sensibilite' => array( 'type' => 'radio', 'required' => true ),
			'Descriptionpdo.decisionpcg' => array( 'type' => 'radio' , 'required' => true ),
			'Descriptionpdo.nbmoisecheance' => array( 'type' => 'text', 'required' => true ),
			'Descriptionpdo.dateactive' => array( 'type' => 'select' , 'required' => true )
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
	<?php echo $this->Xform->end();?>
