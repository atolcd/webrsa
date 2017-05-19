<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'typepdo', "Typespdos::{$this->action}" )
	)
?>

<?php
	$fields = array(
		'Typepdo.libelle'
	);

	if ( Configure::read( 'Cg.departement' ) == 66 ) {
		$fields = array_merge(
			$fields,
			array( 'Typepdo.originepcg' => array( 'type' => 'radio' ) ),
			array( 'Typepdo.cerparticulier' => array( 'type' => 'radio' ) )
		);
	}
	else {
		$fields = array_merge(
			$fields,
			array( 'Typepdo.originepcg' => array( 'type' => 'hidden', 'value' => 'N' ) )
		);
	}

	echo $this->Default->form(
		$fields,
		array(
			'options' => $options,
			'actions' => array(
				'Typepdo.save',
				'Typepdo.cancel'
			)
		)
	);
?>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'typespdos',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>