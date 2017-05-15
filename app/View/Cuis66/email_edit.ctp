<?php
	echo $this->FormValidator->generateJavascript();
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
		
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate', 'id' => 'CuiEmailAddEditForm' ) );

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
	
	// Ajoute un textmailcui66_id au select si le textmailcui66_id stocké en base n'est plus actif
	$id_textmailcui66_id = !empty( $this->request->data['Emailcui']['textmailcui66_id'] ) ? $this->request->data['Emailcui']['textmailcui66_id'] : null;
	if ( $id_textmailcui66_id !== null && !isset( $options['Emailcui']['textmailcui66_id_actif'][$id_textmailcui66_id] ) ){
		$options['Emailcui']['textmailcui66_id_actif'][$id_textmailcui66_id] = $options['Emailcui']['textmailcui66_id'][$id_textmailcui66_id];
	}
	
	echo '<fieldset><legend id="Cui66Choixformulaire">' . __d('cuis66', 'Emailcui.entete_email') . '</legend>'
		. $this->Default3->subform(
			array(
				'Emailcui.id' => array( 'type' => 'hidden' ),
				'Emailcui.cui_id' => array( 'type' => 'hidden' ),
				'Emailcui.cui66_id' => array( 'type' => 'hidden' ),
				'Emailcui.personne_id' => array( 'type' => 'hidden' ),
				'Emailcui.partenairecui_id' => array( 'type' => 'hidden' ),
				'Emailcui.partenairecui66_id' => array( 'type' => 'hidden' ),
				'Emailcui.adressecui_id' => array( 'type' => 'hidden' ),
				'Emailcui.decisioncui66_id' => array( 'type' => 'hidden' ),
				'Emailcui.emailredacteur',
				'Emailcui.emailemployeur',
				'Emailcui.insertiondate' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+1 ),
				'Emailcui.commentaire',
				'Emailcui.piecesmanquantes' => array( 'type' => 'select', 'multiple' => 'checkbox', 'options' => $options['Emailcui']['piecesmanquantes_actif'] ),
			),
			array( 'options' => $options )
		)
		. '<fieldset><legend>' . __d( 'cuis66', 'Emailcui.chargermodel' ) . '</legend>'
		. $this->Default3->subform(
			array(
				'Emailcui.textmailcui66_id' => array( 'options' => $options['Emailcui']['textmailcui66_id_actif'] )
			),
			array( 'options' => $options )
		)
		. '<div class="submit"><input type="button" id="LoadEmailModel" value="Générer l\'e-mail" /></div></fieldset></fieldset><fieldset><legend>' . __d('cuis66', 'Emailcui.email') . '</legend>'
		. $this->Default3->subform(
			array(
				'Emailcui.titre',
				'Emailcui.message',
				'Emailcui.pj' => array( 'type' => 'select', 'multiple' => 'checkbox', 'options' => $options['Emailcui']['pj_actif'] ),
			),
			array( 'options' => $options )
		) . '</fieldset>'
	;

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit( 'CuiEmailAddEditForm' );
	
?>
<script>
	/**
	 * Bouton Charger de Textmaicui66.id
	 * @returns {void}
	 */
	$('LoadEmailModel').onclick = function(){
		var insertDate = $F('EmailcuiInsertiondateYear') + '-' + $F('EmailcuiInsertiondateMonth') + '-' + $F('EmailcuiInsertiondateDay');
		
		var piecesmanquantes = [];
		$$('input[type="checkbox"][name="data[Emailcui][piecesmanquantes][]"]').each(function( input ){
			if ( input.checked ){
				piecesmanquantes.push( input.value );
			}
		});
		piecesmanquantes = piecesmanquantes.join('_');
		
		new Ajax.Request('<?php echo Router::url( array( 'controller' => 'cuis66', 'action' => 'ajax_generate_email' ) ); ?>/', {
			asynchronous:true, 
			evalScripts:true, 
			parameters: {
				'Emailcui.id': $F('EmailcuiId'),
				'Cui.id': $F('EmailcuiCuiId'),
				'Cui66.id': $F('EmailcuiCui66Id'),
				'Personne.id': $F('EmailcuiPersonneId'),
				'Partenairecui.id': $F('EmailcuiPartenairecuiId'),
				'Partenairecui66.id': $F('EmailcuiPartenairecui66Id'),
				'Adressecui.id': $F('EmailcuiAdressecuiId'),
				'Decisioncui66.id': $F('EmailcuiDecisioncui66Id'),
				'Emailcui.textmailcui66_id': $F('EmailcuiTextmailcui66Id'),
				'Emailcui.insertiondate': insertDate,
				'Emailcui.commentaire': $F('EmailcuiCommentaire'),
				'Emailcui.piecesmanquantes': piecesmanquantes
			}, 
			requestHeaders: {Accept: 'application/json'},
			onComplete:function(request, json) {
				$('EmailcuiTitre').value = json.EmailcuiTitre;
				$('EmailcuiMessage').value = json.EmailcuiMessage;
				if ( json.EmailcuiMessage.indexOf('[[[----------ERREURS----------]]]') >= 0 ){
					$$('input[type="submit"][name="Save"]').each(function( button ){
						button.disabled = true;
					});
				}
				else{
					$$('input[type="submit"][name="Save"]').each(function( button ){
						button.disabled = false;
					});
				}
			}
		});
	};
	
</script>