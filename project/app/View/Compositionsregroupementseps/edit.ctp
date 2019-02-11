<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	};

	echo $this->Default3->titleForLayout( $this->request->data );

	echo $this->Default3->DefaultForm->create();
	echo $this->Default3->DefaultForm->input( 'Regroupementep.id', array( 'type' => 'hidden' ) );
	echo $this->Default3->DefaultForm->input( 'Regroupementep.name', array( 'type' => 'hidden' ) );

	foreach( $fonctionsmembreseps as $id => $fonctionmembreep ) {
		$subform =
			$this->Default3->DefaultForm->input( "Compositionregroupementep.{$id}.id", array( 'type' => 'hidden' ) )
			.$this->Default3->DefaultForm->input( "Compositionregroupementep.{$id}.regroupementep_id", array( 'type' => 'hidden' ) )
			.$this->Default3->DefaultForm->input( "Compositionregroupementep.{$id}.fonctionmembreep_id", array( 'type' => 'hidden' ) )
			.$this->Default3->DefaultForm->input( "Compositionregroupementep.{$id}.prioritaire", array( 'type' => 'radio', 'options' => $options['Compositionregroupementep']['prioritaire'], 'legend' => __m( 'Compositionregroupementep.prioritaire' ) ) )
			.$this->Default3->DefaultForm->input( "Compositionregroupementep.{$id}.obligatoire", array( 'type' => 'radio', 'options' => $options['Compositionregroupementep']['obligatoire'], 'legend' => __m( 'Compositionregroupementep.obligatoire' ) ) );

		echo $this->Html->tag(
			'fieldset',
			$this->Html->tag( 'legend', $fonctionmembreep )
			.$subform
		);
	}

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit();
?>