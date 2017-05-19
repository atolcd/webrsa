<?php
	$title_for_layout = 'Annulation du CER';
	$this->set( 'title_for_layout', $title_for_layout );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}

	echo $this->Html->tag( 'h1', $title_for_layout );
?>
<br />
<div id="tabbedWrapper" class="tabs">
	<div id="annulation">
		<h2 class="title"><?php echo $title_for_layout;?></h2>
		<?php
			echo $this->Xform->getExtraValidationErrorMessages();

			echo $this->Xform->create( null, array( 'inputDefaults' => array( 'domain' => 'contratinsertion' ), 'id' => 'Cer93CancelForm' ) );

			echo $this->Xform->inputs(
				array(
					'fieldset' => false,
					'legend' => false,
					'Contratinsertion.id',
					'Cer93.id',
					'Cer93.date_annulation' => array( 'domain' => 'cer93', 'type' => 'date', 'dateFormat' => 'DMY', 'empty' => true, 'required' => true ),
					'Contratinsertion.motifannulation' => array( 'domain' => 'contratinsertion', 'required' => true )
				)
			);

			echo $this->Html->tag(
				'div',
				 $this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
				.$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
				array( 'class' => 'submit noprint' )
			);

			echo $this->Xform->end();
		?>
	</div>
	<div id="cerview">
		<h2 class="title">Visualisation du CER</h2>
		<?php include( dirname( __FILE__ ).'/_view.ctp' ); ?>
	</div>
</div>

<script type="text/javascript">
    document.observe( "dom:loaded", function() {
		makeTabbed( 'tabbedWrapper', 2 );
	} );
</script>
<?php echo $this->Observer->disableFormOnSubmit( 'Cer93CancelForm' );?>