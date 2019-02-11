<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
		
	echo '<div class="Cui66AddEdit">';

/***********************************************************************************
 * Formulaire E-mail
/***********************************************************************************/
	
	// Ajoute une checkbox si la piece manquante stocké en base n'est plus active
	$id_piecemanquante = isset( $this->request->data['Emailcui']['piecesmanquantes'] ) ? $this->request->data['Emailcui']['piecesmanquantes'] : null;
	if ( $id_piecemanquante !== null ) {
		foreach ( $id_piecemanquante as $id ){
			if ( $id !== '' && !isset( $options['Emailcui']['piecesmanquantes_actif'][$id] ) ){
				$options['Emailcui']['piecesmanquantes_actif'][$id] = $options['Emailcui']['piecesmanquantes'][$id];
			}
		}
	}
	
	// Ajoute une checkbox si le fichier lié stocké en base n'est plus actif
	$id_piecemail = isset( $this->request->data['Emailcui']['pj'] ) ? $this->request->data['Emailcui']['pj'] : null;
	if ( $id_piecemail !== null ) {
		foreach ( $id_piecemail as $id ){
			if ( $id !== '' && !isset( $options['Emailcui']['pj_actif'][$id] ) ){
				$options['Emailcui']['pj_actif'][$id] = $options['Emailcui']['pj'][$id];
			}
		}
	}
	
	echo '<fieldset><legend id="Cui66Choixformulaire">' . __d('cuis66', 'Emailcui.entete_email') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Emailcui.emailredacteur',
				'Emailcui.emailemployeur',
				'Emailcui.insertiondate' => array( 'dateFormat' => 'DMY', 'type' => 'date', 'view' => true ),
				'Emailcui.commentaire' => array ( 'type' => 'textarea' ),
			) ,
			array( 'options' => $options )
		)
		. $this->Default3->subform(
			array( 'Emailcui.piecesmanquantes' => array( 'type' => 'select', 'multiple' => 'checkbox', 'options' => $options['Emailcui']['piecesmanquantes_actif'] ) )
		)
		. '</fieldset><fieldset><legend>' . __d('cuis66', 'Emailcui.email') . '</legend>'
		. $this->Default3->subform( array( 'Emailcui.titre' => array( 'view' => true ) ) )
			
		. '<div class="input value textarea"><span class="label">' .  __d( 'cuis66', 'Emailcui.message' ) . '</span>'
		. preg_replace('/[\n\r]{2,2}/', '<br />', $this->request->data['Emailcui']['message']) . '</div><hr>'
		. $this->Default3->subform(
			array( 'Emailcui.pj' => array( 'type' => 'select', 'multiple' => 'checkbox', 'options' => $options['Emailcui']['pj_actif'] ) )
		) . '</fieldset>'
	;
	
	echo '<br />' . $this->Default->button(
		'back',
		array(
			'controller' => 'cuis66',
			'action'     => 'email',
			$personne_id, $this->request->data['Emailcui']['cui_id']
		),
		array(
			'id' => 'Back',
			'class' => 'aere'
		)
	);
	
	echo '</div>';
?>