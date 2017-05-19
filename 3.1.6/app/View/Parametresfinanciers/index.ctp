<?php
	$this->pageTitle = 'Paramètres financiers pour la gestion de l\'APRE';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	echo $this->Xform->create( 'ParametreFinancier' );

	if( $this->Permissions->check( 'parametresfinanciers', 'edit' ) ) {
		echo $this->Xhtml->tag(
			'ul',
			$this->Xhtml->tag(
				'li',
				$this->Xhtml->editLink(
					'Modifier les paramètres',
					array( 'controller' => 'parametresfinanciers', 'action' => 'edit' )
				)
			),
			array( 'class' => 'actionMenu' )
		);
	}

	if( !empty( $parametrefinancier ) ) {
		$rows = array();
		foreach( $parametrefinancier['Parametrefinancier'] as $field => $value ) {
			if( $field != 'id' ) {
				$rows[] = array( __d( 'apre', "Parametrefinancier.{$field}" ), $value );
			}
		}
		echo $this->Xhtml->details( $rows );
	}

	echo '<div class="submit">';
	echo $this->Xform->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
	echo '</div>';
	echo $this->Xform->end();
?>