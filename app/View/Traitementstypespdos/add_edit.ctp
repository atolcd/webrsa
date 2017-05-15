<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'traitementtypepdo', "Traitementstypespdos::{$this->action}" )
	)
?>

<?php
	echo $this->Default->form(
		array(
			'Traitementtypepdo.name' => array( 'type' => 'text', 'required' => true )
		),
		array(
			'actions' => array(
				'Traitementtypepdo.save',
				'Traitementtypepdo.cancel'
			)
		)
	);
?>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'traitementstypespdos',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>
