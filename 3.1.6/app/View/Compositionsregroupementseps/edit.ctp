<?php
	$this->pageTitle = "Gestion de la composition du regroupement d'E.P. : {$this->request->data['Regroupementep']['name']}.";

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if ( isset( $prioritaireExist ) && !empty( $prioritaireExist ) ) {
		echo "<p class='error'>Merci de mettre au moins un membre prioritaire (les mettre tous prioritaires si aucune gestion).</p>";
	}

	echo $this->Xform->create( null );
	echo $this->Xform->input( 'Regroupementep.name', array( 'type' => 'hidden' ) );

	foreach( $fonctionsmembreseps as $functionId => $functionName ) {
		echo "<fieldset><legend>{$functionName}</legend>";
		echo $this->Xhtml->tag(
			'div',
			$this->Default->subform(
				array(
					"Compositionregroupementep.{$functionId}.id" => array( 'type' => 'hidden' ),
					"Compositionregroupementep.{$functionId}.prioritaire" => array( 'type' => 'radio' ),
					"Compositionregroupementep.{$functionId}.obligatoire" => array( 'type' => 'radio' )
				),
				array(
					'options' => $options
				)
			)
		);
		echo '</fieldset>';
	}

	echo $this->Xform->end( __( 'Save' ) );

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'compositionsregroupementseps',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>