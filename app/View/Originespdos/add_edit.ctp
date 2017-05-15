<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'originepdo', "Originespdos::{$this->action}" )
	)
?>
<?php
	$fields = array(
		'Originepdo.libelle'
	);

	if ( Configure::read( 'Cg.departement' ) == 66 ) {
		$fields = array_merge(
			$fields,
			array( 'Originepdo.originepcg' => array( 'type' => 'radio' ) ),
			array( 'Originepdo.cerparticulier' => array( 'type' => 'radio' ) )
		);
	}
	else {
		$fields = array_merge(
			$fields,
			array( 'Originepdo.originepcg' => array( 'type' => 'hidden', 'value' => 'N' ) )
		);
	}

	echo $this->Default->form(
		$fields,
		array(
			'options' => $options,
			'actions' => array(
				'Originepdo.save',
				'Originepdo.cancel'
			)
		)
	);
?>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'originespdos',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>