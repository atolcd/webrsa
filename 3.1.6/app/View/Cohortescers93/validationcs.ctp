<?php
	$this->pageTitle = '4. Décision CG - 4.2 Validation CS';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js' ) );
	}

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	require_once( dirname( __FILE__ ).DS.'filtre.ctp' );

	if( isset( $cers93 ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );
		$mode_impression = ( Hash::get( $this->request->data, 'Search.Cer93.mode_operation' ) == 'impression' );

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
						<th>Statut du CER</th>
						<th>Forme du CER (Responsable)</th>
						<th>Prévalidé</th>
						<th class="action">Action</th>
						<th class="action">Forme du CER (CG)</th>
						<th class="action">Commentaire (CG-CS)</th>
						<th class="action">Décision CS</th>
						<th class="action" style="min-width:6em;">Durée CER</th>
						<th class="action">Date de décision</th>
						<th class="action">Observation décision</th>
						<th class="action" colspan="2">Détails</th>
					</tr>
				</thead>';
			echo '<tbody>';
			foreach( $cers93 as $index => $cer93 ) {
				$rowId = "innerTableTrigger{$index}";
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

				$emetteurResponsable = '';
				if( !empty( $cer93['User']['nom_complet'] ) ) {
					$emetteurResponsable = ' (émis par '.$cer93['User']['nom_complet'].' )';
				}

				$cells = array(
					$this->Xhtml->link( $cer93['Dossier']['numdemrsa'], array( 'controller' => 'dossiers', 'action' => 'view', $cer93['Dossier']['id'] ), array( 'class' => 'external' ) ),// ALAQTASH
					$cer93['Personne']['nom_complet_court'],
					$cer93['Dossier']['matricule'],
					$cer93['Adresse']['nomcom'],
					date_short( $cer93['Contratinsertion']['created'] ),
					date_short( $cer93['Contratinsertion']['dd_ci'] ),
					$this->Xhtml->boolean( $cer93['NvTransfertpdv93']['encoursvalidation'] ),
					Set::enum( $cer93['Cer93']['positioncer'], $options['Cer93']['positioncer'] ),
					Set::enum( $cer93['Histochoixcer93']['formeci'], $options['formeci'] ),
					Set::enum( $cer93['Histochoixcer93']['prevalide'], $options['Histochoixcer93']['prevalide'] ),
				);

				$affichage = false;
				if( $cer93['Cer93']['positioncer'] == '04premierelecture' ) {
					$cells = array_merge(
						$cells,
						array(
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
								$this->Form->input( "Histochoixcer93.{$index}.decisioncs", array( 'empty' => 'En attente', 'label' => false, 'type' => 'select', 'options' => $options['Histochoixcer93']['decisioncs'] ) ),
								array( 'class' => ( isset( $this->validationErrors['Histochoixcer93'][$index]['decisioncs'] ) ? 'error' : null ) )
							),
							array(
								$this->Form->input( "Histochoixcer93.{$index}.duree", array( 'div' => false, 'legend' => false, 'type' => 'radio', 'options' => $options['Cer93']['duree'], 'separator' => '<br />' ) ),
								array( 'class' => ( isset( $this->validationErrors['Histochoixcer93'][$index]['duree'] ) ? 'error' : null ) )
							),
							array(
								$this->Form->input( "Histochoixcer93.{$index}.datechoix", array( 'label' => false, 'type' => 'date', 'dateFormat' => 'DMY', 'empty' => false ) ),
								array( 'class' => ( isset( $this->validationErrors['Histochoixcer93'][$index]['datechoix'] ) ? 'error' : null ) )
							),
							array(
								$this->Form->input( "Histochoixcer93.{$index}.observationdecision", array( 'label' => false, 'legend' => false, 'type' => 'textarea' ) ),
								array( 'class' => ( isset( $this->validationErrors['Histochoixcer93'][$index]['observationdecision'] ) ? 'error' : null ) )
							)
						)
					);
				}
				else {
					$affichage = true;
					$cells = array_merge(
						$cells,
						array(
							'',
							Set::enum( $cer93['Histochoixcer93']['formeci'], $options['formeci'] ),
							$cer93['Histochoixcer93']['commentaire'],
							Set::enum( $cer93['Histochoixcer93']['decisioncs'], $options['Histochoixcer93']['decisioncs'] ),
							Set::enum( $cer93['Histochoixcer93']['duree'], $options['Cer93']['duree'] ),
							date_short( $cer93['Histochoixcer93']['datechoix'] ),
							$cer93['Cer93']['observationdecision']
						)
					);
				}

				$cells = array_merge(
					$cells,
					array(
						// Détails
						$this->Xhtml->printLink(
							'Décision',
							array( 'controller' => 'cers93', 'action' => 'impressionDecision', $cer93['Contratinsertion']['id'] ),
							( $this->Permissions->check( 'cers93', 'impressionDecision' ) && $affichage && $mode_impression )
						),
						$this->Xhtml->viewLink( 'Voir', array( 'controller' => 'histoschoixcers93', 'action' => 'secondelecture_consultation', $cer93['Contratinsertion']['id'], '#' => 'cerview' ), true, true ),
						array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
					)
				);

				echo $this->Html->tableCells(
					$cells,
					array( 'class' => 'odd', 'id' => $rowId ),
					array( 'class' => 'even', 'id' => $rowId )
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

		echo '<ul class="actionMenu"><li>';
		echo $this->Xhtml->printCohorteLink(
			'Imprimer la cohorte',
			Set::merge(
				array(
					'controller' => 'cohortescers93',
					'action'     => 'impressionsDecisions',
					'validationcs'
				),
				Hash::flatten( Hash::remove( $this->request->data, 'Histochoixcer93' ), '__' )
			),
			$this->Permissions->check( 'cohortescers93', 'impressionsDecisions' ) && $mode_impression && !empty( $cers93 )
		);
		echo '</li></ul>';

	}
?>

<?php if( isset( $cers93 ) && !empty( $cers93 ) ):?>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		// On désactive le select du référent si on ne choisit pas de valider
		<?php foreach( $cers93 as $index => $cer93 ):?>
			<?php if( $cer93['Cer93']['positioncer'] == '04premierelecture' ):?>

				observeDisableFieldsOnValue(
					'Histochoixcer93<?php echo $index;?>Decisioncs',
					[ 'Histochoixcer93<?php echo $index;?>Observationdecision' ],
					[ 'valide' ],
					false
				);

				observeDisableFieldsOnRadioValue(
					'Personne',
					'data[Histochoixcer93][<?php echo $index;?>][action]',
					[
						'Histochoixcer93<?php echo $index;?>FormeciS',
						'Histochoixcer93<?php echo $index;?>FormeciC',
						'Histochoixcer93<?php echo $index;?>Decisioncs',
						'Histochoixcer93<?php echo $index;?>Duree3',
						'Histochoixcer93<?php echo $index;?>Duree6',
						'Histochoixcer93<?php echo $index;?>Duree9',
						'Histochoixcer93<?php echo $index;?>Duree12',
						'Histochoixcer93<?php echo $index;?>DatechoixDay',
						'Histochoixcer93<?php echo $index;?>DatechoixMonth',
						'Histochoixcer93<?php echo $index;?>DatechoixYear',
						'Histochoixcer93<?php echo $index;?>Commentaire'
					],
					[ 'Activer' ],
					true
				);

				observeFilterSelectOptionsFromRadioValue(
					'Personne',
					'data[Histochoixcer93][<?php echo $index;?>][formeci]',
					'Histochoixcer93<?php echo $index;?>Decisioncs',
					{
						'S': ['valide', 'aviscadre'],
						'C': ['aviscadre', 'passageep']
					}
				);

			<?php endif;?>
		<?php endforeach;?>
	} );
</script>
<?php echo $this->Observer->disableFormOnSubmit( 'Personne' );?>
<?php endif;?>