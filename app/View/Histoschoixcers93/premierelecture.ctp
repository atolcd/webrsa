<?php
	$title_for_layout = '1ère lecture';
	$this->set( 'title_for_layout', $title_for_layout );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>

<?php
	// FIXME: liste de titres depuis le contrôleur
	echo $this->Html->tag( 'h1', $title_for_layout );
?>
<br />
<div id="tabbedWrapper" class="tabs">
	<?php if( !$consultation ):?>
	<div id="premierelecture">
		<h2 class="title">Première lecture</h2>
		<?php
			if( Hash::get( $contratinsertion, 'Personne.Foyer.Adressefoyer.0.NvTransfertpdv93.encoursvalidation' ) ) {
				echo $this->Html->tag( 'p', 'Le dossier de l\'allocataire a été transféré après la saisie du CER.', array( 'class' => 'notice' ) );
			}

			echo $this->Xform->create( null, array( 'inputDefaults' => array( 'domain' => 'histochoixcer93' ) ) );

			// FIXME: affichage du CER et des étapes précédentes de l'historique

			echo $this->Xform->inputs(
				array(
					'fieldset' => false,
					'legend' => false,
					'Histochoixcer93.id' => array( 'type' => 'hidden' ),
					'Histochoixcer93.cer93_id' => array( 'type' => 'hidden' ),
					'Histochoixcer93.user_id' => array( 'type' => 'hidden' ),
					'Histochoixcer93.formeci' => array( 'type' => 'radio', 'options' => $options['Cer93']['formeci'] ),
					'Histochoixcer93.commentaire' => array( 'type' => 'textarea' ),
					'Histochoixcer93.datechoix' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
					'Histochoixcer93.prevalide' => array( 'type' => 'radio', 'options' => $options['Histochoixcer93']['prevalide'] ),
					'Histochoixcer93.etape' => array( 'type' => 'hidden' )
				)
			);
		?>

		<?php
			echo $this->Html->tag(
				'div',
				$this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
				.$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
				array( 'class' => 'submit noprint' )
			);

			echo $this->Xform->end();
		?>
	</div>
	<?php endif;?>
	<div id="historique">
		<h2 class="title">Décisions précédentes</h2>
			<?php
			$histo = $contratinsertion['Cer93']['Histochoixcer93'];

			foreach( $histo as $i => $h ) {
				$etapeValue = Set::classicExtract( $h, 'etape');
				$etape = Set::enum( Set::classicExtract( $h, 'etape'), $options['Histochoixcer93']['etape'] );
				if( $etapeValue != '02attdecisioncpdv' ) {
					echo "<fieldset><legend>$etape</legend>";
					echo $this->Xform->fieldValue( 'Cer93.formeci', Set::enum( Set::classicExtract( $h, 'formeci'), $options['Cer93']['formeci'] ) );
					echo $this->Xform->fieldValue( 'Histochoixcer93.datechoix', date_short( Set::classicExtract( $h, 'datechoix') ) );

					if( !empty( $h['commentaire'] ) ) {
						echo $this->Xform->fieldValue( 'Histochoixcer93.commentaire', Set::classicExtract( $h, 'commentaire') );
					}

					echo '<fieldset><legend>Commentaires</legend>';
					$commentaires = '';
					foreach( $h['Commentairenormecer93'] as $key => $commentaire ) {
						if( !empty( $commentaire ) ) {
							if( $commentaire['isautre'] == '1' ) {
								$commentaires .= "<li>{$commentaire['name']}: <em>{$commentaire['Commentairenormecer93Histochoixcer93']['commentaireautre']}</em></li>";
							}
							else {
								$commentaires .= '<li>'.$commentaire['name'].'</li>';
							}

						}
					}

					if( !empty( $commentaires ) ) {
						echo "<ul>{$commentaires}</ul>";
					}

					echo '</fieldset>';

					echo $this->Xform->fieldValue( 'Histochoixcer93.user_id', Set::classicExtract( $h, 'User.nom_complet') );

					if( $h['etape'] == '03attdecisioncg' ) {
						echo $this->Xform->fieldValue( 'Histochoixcer93.isrejet', Set::enum( Set::classicExtract( $h, 'isrejet'), $options['Histochoixcer93']['isrejet'] ) );
					}
					else if( $h['etape'] == '04premierelecture' ) {
						echo $this->Xform->fieldValue( 'Histochoixcer93.prevalide', Set::enum( Set::classicExtract( $h, 'prevalide'), $options['Histochoixcer93']['prevalide'] ) );
					}
					else if( $h['etape'] == '05secondelecture' ) {
						echo $this->Xform->fieldValue( 'Histochoixcer93.decisioncs', Set::enum( Set::classicExtract( $h, 'decisioncs'), $options['Histochoixcer93']['decisioncs'] ) );
					}
					else if( $h['etape'] == '06attaviscadre' ) {
						echo $this->Xform->fieldValue( 'Histochoixcer93.decisioncadre', Set::enum( Set::classicExtract( $h, 'decisioncadre'), $options['Histochoixcer93']['decisioncadre'] ) );
					}
					echo '</fieldset>';
				}
			}
		?>
	</div>
	<div id="cerview">
		<h2 class="title">Visualisation du CER</h2>
		<?php
			include( dirname( __FILE__ ).'/../Cers93/_view.ctp' );
		?>
	</div>
</div>
<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
</script>