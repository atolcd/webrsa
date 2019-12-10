<?php

	if ( empty ($Emails) ) {
		if ( !empty ($Email) ){
			$Emails[0] = $Email;
		}else{
			$Emails =array();
		}
	}
	foreach ( $Emails AS $Email ){
		if ( empty ($Email) ){
			echo '<H2 id="formulaireEmail">'.__d('email', 'Email.titre_aucunemail').'</H2>' ; 
		}else{
			/***********************************************************************************
			 * Formulaire E-mail
			/***********************************************************************************/
			echo '<H2 id="formulaireEmail">'.__d('email', 'Email.titre_view_email').' '.$Email['Email']['modele_action'].'</H2>' ; 
			echo '<fieldset><legend id="EmailChoixformulaire">' . __d('email', 'Email.entete_email') . '</legend>'
				. $this->Default3->view(
					$Email,
					$this->Translator->normalize(
						array(
							'Email.etat',
							'Email.modele_action',
							'Email.emailredacteur',
							'Email.emaildestinataire',
							'Email.insertiondate' => array( 'dateFormat' => 'DMY', 'type' => 'date', 'view' => true ),
							'Email.dateenvoi' => array( 'dateFormat' => 'DMY', 'type' => 'date', 'view' => true ),
							'Email.commentaire' => array ( 'type' => 'textarea' ),
						)
					),
					array( 'options' => $optionsEmail )
				)
				. '</fieldset><fieldset><legend>' . __d('email', 'Email.email') . '</legend>'
				. $this->Default3->view(
					$Email,
					$this->Translator->normalize(
						array(
							'Email.titre',
							'Email.message',
							'Email.pj'
						)
					)
				);
				if  ( !empty ($Email['Email']['sendButton']) ){
					$enabled = $Email['Email']['sendButton']['activate'];
					$controler = $Email['Email']['sendButton']['controller'];
					$action = $Email['Email']['sendButton']['action'];
					$params = $Email['Email']['sendButton']['params'];
					if ( $Email['Email']['etat'] == 'CREE' ) {
						$label =__d('Email', 'Send');
					}else{
						$label =__d('Email', 'Resend');
					}
					echo '</br>'. $this->Xhtml->link(
						$label,
						array_merge(
							array('controller' => $controler, 'action' => $action),
							$params
						),
						array(
							'class' => 'button email',
							'enabled' => $enabled
						)
					);
				}
				echo  '</fieldset>';
		}
	}
?>