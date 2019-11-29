<?php
	echo $this->FormValidator->generateJavascript();

	echo '<H2 id="formulaireEmail">' . __d('email', 'Email.titre_email') . '</H2>' ; 
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

/***********************************************************************************
 * Formulaire E-mail
/***********************************************************************************/

	// Ajoute une checkbox si le fichier lié stocké en base n'est plus actif
	$id_piecemail = isset( $this->request->data['Email']['pj'] ) ? $this->request->data['Email']['pj'] : null;
	if ( $id_piecemail !== null ) {
		foreach ( $id_piecemail as $id ){
			if ( $id !== '' && !isset( $options['Email']['pj_actif'][$id] ) ){
				$optionsEmail['Email']['pj_actif'][$id] = $optionsEmail['Email']['pj'][$id];
			}
		}
	}
	
	// Ajoute un textemail_id au select si le textemail_id stocké en base n'est plus actif
	$id_textemail_id = !empty( $this->request->data['Email']['textemail_id'] ) ? $this->request->data['Email']['textemail_id'] : null;
	if ( $id_textemail_id !== null && !isset( $options['Email']['textemail_id_actif'][$id_textemail_id] ) ){
		$optionsEmail['Email']['textemail_id_actif'][$id_textemail_id] = $optionsEmail['Email']['textemail_id'][$id_textemail_id];
	}
	
	echo '<fieldset><legend id="Choixformulaire">' . __d('email', 'Email.entete_email') . '</legend>'
		. $this->Default3->subform(
			$this->Translator->normalize( 
				array(
					'Email.id' => array( 'type' => 'hidden' ),
					'Email.etat' => array( 'type' => 'hidden' ),
					'Email.foyer_id' => array( 'type' => 'hidden' ),
					'Email.user_id' => array( 'type' => 'hidden' ),
					'Email.modele' => array( 'type' => 'hidden' ),
					'Email.modele_id' => array( 'type' => 'hidden' ),
					'Email.modele_action' => array( 'type' => 'hidden' ),
					'Email.modeleparent' => array( 'type' => 'hidden' ),
					'Email.modeleparent_id' => array( 'type' => 'hidden' ),
					'Email.emailredacteur',
					'Email.emaildestinataire' => array(
						'type' => 'select',
						'options' => $optionsEmail['Email']['emaildestinataire_id_actif']
					),
					'Email.insertiondate' => array(
						'dateFormat' => 'DMY',
						'minYear' => '2009',
						'maxYear' => date('Y')+1
					),
					'Email.commentaire',
				)
			),
			array( 'options' => $optionsEmail )
		)
		. '<fieldset><legend>' . __d( 'email', 'Email.chargermodel' ) . '</legend>'
		. $this->Default3->subform(
			$this->Translator->normalize( 
				array(
					'Email.textemail_id' => array( 'options' => $optionsEmail['Email']['textemail_id_actif'] )
				)
			),
			array( 'options' => $optionsEmail )
		)
		. '<div class="submit"><input type="button" id="LoadEmailModel" value="Générer l\'e-mail" /></div></fieldset></fieldset><fieldset><legend>' . __d('email', 'Email.email') . '</legend>'
		. $this->Default3->subform(		
			$this->Translator->normalize( 
				array(
					'Email.titre',
					'Email.message',
					'Email.pj' => array( 'type' => 'select', 'multiple' => 'checkbox', 'options' => $optionsEmail['Email']['pj_actif'] ),
				)
			),
			array( 'options' => $optionsEmail )
		) . '</fieldset>'
	;	
?>
<script>
	/**
	 * Bouton Charger de Textmailcui.id
	 * @returns {void}
	 */
	$('LoadEmailModel').onclick = function(){
		var insertDate = $F('EmailInsertiondateYear') + '-' + $F('EmailInsertiondateMonth') + '-' + $F('EmailInsertiondateDay');
		new Ajax.Request('<?php echo Router::url( array( 'controller' => 'emails', 'action' => 'ajax_generate_email' ) ); ?>/', {
			asynchronous:true, 
			evalScripts:true, 
			parameters: {
				'Email.id': $F('EmailId'),
				'Email.foyer_id': $F('EmailFoyerId'),
				'Email.user_id': $F('EmailUserId'),
				'Email.modele': $F('EmailModele'),
				'Email.modele_id': $F('EmailModeleId'),
				'Email.modeleparent': $F('EmailModeleparent'),
				'Email.modeleparent_id': $F('EmailModeleparentId'),
				'Email.textemail_id': $F('EmailTextemailId'),
				'Email.insertiondate': insertDate,
				'Email.commentaire': $F('EmailCommentaire')
			}, 
			requestHeaders: {Accept: 'application/json'},
			onComplete:function(request, json) {
				$('EmailTitre').value = json.EmailTitre;
				$('EmailMessage').value = json.EmailMessage;
				if ( json.EmailMessage.indexOf('[[[----------ERREURS----------]]]') >= 0 ){
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