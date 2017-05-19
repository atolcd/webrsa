<?php
	$this->pageTitle = '4. Décision CG - 4.1 Première lecture';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js' ) );
	}
	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	require_once( dirname( __FILE__ ).DS.'filtre.ctp' );

	if( isset( $cers93 ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		if( empty( $cers93 ) ) {
			echo $this->Xhtml->tag( 'p', 'Aucun résultat', array( 'class' => 'notice' ) );
		}
		else {
			$pagination = $this->Xpaginator->paginationBlock( 'Personne', $this->passedArgs );
			echo $pagination;

			echo $this->Xform->create( null, array( 'id' => 'Personne' ) );

			echo '<table id="searchResults" class="tooltips">';
			echo '<thead>
					<tr>
						<th class="action">N° dossier RSA</th>
						<th>Nom/Prénom</th>
						<th>N° CAF</th>
						<th>Commune</th>
						<th>Date d\'envoi CER</th>
						<th>Date de début CER</th>
						<th>Déménagement ?</th>
						<th>Forme du CER (Responsable)</th>
						<th class="action">Action</th>
						<th class="action">Forme du CER (CG)</th>
						<th class="action">Commentaire (CG-Gestionnaire)</th>
						<th class="action">Décision</th>
						<th class="action">Date de décision</th>
						<th class="action">Détails</th>
					</tr>
				</thead>';
			echo '<tbody>';
			foreach( $cers93 as $index => $cer93 ) {
				$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
					<tbody>
						<tr>
							<th>Date de demande RSA</th>
							<td>'.date_short( $cer93['Dossier']['dtdemrsa'] ).'</td>
						</tr>
						<tr>
							<th>Date de naissance</th>
							<td>'.date_short( $cer93['Personne']['dtnai'] ).'</td>
						</tr>
						<tr>
							<th>N° CAF</th>
							<td>'.$cer93['Dossier']['matricule'].'</td>
						</tr>
						<tr>
							<th>NIR</th>
							<td>'.$cer93['Personne']['nir'].'</td>
						</tr>
						<tr>
							<th>Code postal</th>
							<td>'.$cer93['Adresse']['codepos'].'</td>
						</tr>
						<tr>
							<th>Date de fin de droit</th>
							<td>'.$cer93['Situationdossierrsa']['dtclorsa'].'</td>
						</tr>
						<tr>
							<th>Motif de fin de droit</th>
							<td>'.Set::enum( $cer93['Situationdossierrsa']['moticlorsa'], $options['moticlorsa'] ).'</td>
						</tr>
						<tr>
							<th>Rôle</th>
							<td>'.Set::enum( $cer93['Prestation']['rolepers'], $options['rolepers'] ).'</td>
						</tr>
						<tr>
							<th>Etat du dossier</th>
							<td>'.Set::classicExtract( $options['etatdosrsa'], $cer93['Situationdossierrsa']['etatdosrsa'] ).'</td>
						</tr>
						<tr>
							<th>Présence DSP</th>
							<td>'.$this->Xhtml->boolean( $cer93['Dsp']['exists'] ).'</td>
						</tr>
						<tr>
							<th>Adresse</th>
							<td>'.$cer93['Adresse']['numvoie'].' '.$cer93['Adresse']['libtypevoie'].' '.$cer93['Adresse']['nomvoie'].' '.$cer93['Adresse']['codepos'].' '.$cer93['Adresse']['nomcom'].'</td>
						</tr>
					</tbody>
				</table>';

				echo $this->Html->tableCells(
					array(
						$this->Xhtml->link( $cer93['Dossier']['numdemrsa'], array( 'controller' => 'dossiers', 'action' => 'view', $cer93['Dossier']['id'] ), array( 'class' => 'external' ) ),
						$cer93['Personne']['nom_complet_court'],
						$cer93['Dossier']['matricule'],
						$cer93['Adresse']['nomcom'],
						date_short( $cer93['Contratinsertion']['created'] ),
						date_short( $cer93['Contratinsertion']['dd_ci'] ),
						$this->Xhtml->boolean( $cer93['NvTransfertpdv93']['encoursvalidation'] ),
						Set::enum( $cer93['Histochoixcer93']['formeci'], $options['formeci'] ),
						// Choix du Responsable
						array(
							$this->Form->input( "Histochoixcer93.{$index}.dossier_id", array( 'type' => 'hidden' ) )
							.$this->Form->input( "Histochoixcer93.{$index}.cer93_id", array( 'type' => 'hidden' ) )
							.$this->Form->input( "Histochoixcer93.{$index}.user_id", array( 'type' => 'hidden' ) )
							.$this->Form->input( "Histochoixcer93.{$index}.etape", array( 'type' => 'hidden') )
							.$this->Form->input( "Histochoixcer93.{$index}.action", array( 'div' => false, 'legend' => false, 'type' => 'radio', 'options' => $options['actions'], 'separator' => '<br />' ) ),
							array( 'class' => ( isset( $this->validationErrors['Histochoixcer93'][$index]['action'] ) ? 'error' : null ) )
						),
						array(
							$this->Form->input( "Histochoixcer93.{$index}.formeci", array( 'div' => false, 'legend' => false, 'type' => 'radio', 'options' => $options['formeci'], 'separator' => '<br />' ) ),
							array( 'class' => ( isset( $this->validationErrors['Histochoixcer93'][$index]['formeci'] ) ? 'error' : null ) )
						),
						array(
							$this->Form->input( "Histochoixcer93.{$index}.commentaire", array( 'label' => false, 'legend' => false, 'type' => 'textarea' ) ),
							array( 'class' => ( isset( $this->validationErrors['Histochoixcer93'][$index]['commentaire'] ) ? 'error' : null ) )
						),
						array(
							$this->Form->input( "Histochoixcer93.{$index}.prevalide", array( 'label' => false, 'type' => 'select', 'options' => $options['Histochoixcer93']['prevalide'], 'empty' => true ) ),
							array( 'class' => ( isset( $this->validationErrors['Histochoixcer93'][$index]['prevalide'] ) ? 'error' : null ) )
						),
						array(
							$this->Form->input( "Histochoixcer93.{$index}.datechoix", array( 'label' => false, 'type' => 'date', 'dateFormat' => 'DMY', 'empty' => false ) ),
							array( 'class' => ( isset( $this->validationErrors['Histochoixcer93'][$index]['datechoix'] ) ? 'error' : null ) )
						),
						// Détails
						$this->Xhtml->viewLink( 'Voir', array( 'controller' => 'histoschoixcers93', 'action' => 'premierelecture_consultation', $cer93['Contratinsertion']['id'], '#' => 'cerview' ), true, true ),
						array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
					),
					array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
					array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
				);
			}
			echo '</tbody>';
			echo '</table>';
			echo $this->Xform->submit( 'Validation de la liste' );
			echo $this->Xform->end();

			echo $pagination;
			echo $this->Form->button( 'Tout Activer', array( 'type' => 'button', 'onclick' => "return toutChoisir( $( 'Personne' ).getInputs( 'radio' ), 'Activer', true );" ) );
			echo $this->Form->button( 'Tout Désactiver', array( 'type' => 'button', 'onclick' => "return toutChoisir( $( 'Personne' ).getInputs( 'radio' ), 'Desactiver', true );" ) );
		}

	}
?>
<?php if( isset( $cers93 ) && !empty( $cers93 ) ):?>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		// On désactive le select du référent si on ne choisit pas de valider
		<?php foreach( array_keys( $cers93 ) as $index ):?>
		observeDisableFieldsOnRadioValue(
			'Personne',
			'data[Histochoixcer93][<?php echo $index;?>][action]',
			[
				'Histochoixcer93<?php echo $index;?>FormeciS',
				'Histochoixcer93<?php echo $index;?>FormeciC',
				'Histochoixcer93<?php echo $index;?>Prevalide',
				'Histochoixcer93<?php echo $index;?>DatechoixDay',
				'Histochoixcer93<?php echo $index;?>DatechoixMonth',
				'Histochoixcer93<?php echo $index;?>DatechoixYear',
				'Histochoixcer93<?php echo $index;?>Commentaire'
			],
			[ 'Activer' ],
			true
		);
		<?php endforeach;?>
	} );
</script>
<?php echo $this->Observer->disableFormOnSubmit( 'Personne' );?>
<?php endif;?>