<?php
	$this->pageTitle = '5. Tableau de suivi';
	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js' ) );
	}

	require_once( dirname( __FILE__ ).DS.'filtre.ctp' );

	if( isset( $cers93 ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		if( empty( $cers93 ) ) {
			echo $this->Xhtml->tag( 'p', 'Aucun résultat', array( 'class' => 'notice' ) );
		}
		else {
			$pagination = $this->Xpaginator->paginationBlock( 'Personne', $this->passedArgs );
			echo $pagination;
// - Non orienté PDV : avec comme domaine de valeurs ( visible uniquement des profils CG)
//
// 	- si allocataire non orienté alors : lien hypertexte Orientation vers le dossier allocataire rubrique Orientation
// 	- si allocataire est orienté PE ou Social : lien hypertexte Réorientation vers le dossier allocataire rubrique Orientation
// 	- si allocataire orienté vers PDV : vide
//

			echo '<table id="searchResults" class="tooltips">';
			echo '<colgroup />
					<colgroup />
					<colgroup />
					<colgroup />
					<colgroup />
					<colgroup />
					<colgroup />
					<colgroup span="4" style="border-right: 5px solid #235F7D;border-left: 5px solid #235F7D;" />
					<colgroup span="5" style="border-right: 5px solid #235F7D;border-left: 5px solid #235F7D;" />
					<colgroup />
					<colgroup />
					<colgroup />
					<colgroup />
				<thead>
					<tr>
						<th rowspan="2">Commune</th>
 						<th rowspan="2">Non orienté PDV</th>
						<th rowspan="2">Nom/Prénom</th>
						<th rowspan="2">Structure référente</th>
						<th rowspan="2">Référent</th>
						<th rowspan="2">Saisie du CER</th>
						<th rowspan="2">Déménagement ?</th>
						<th colspan="4">Etape du Responsable</th>

						<th colspan="5">Etape du CG</th>
						<th class="action" rowspan="2">Actions</th>
					</tr>
					<tr>
						<th>Validation Responsable</th>
						<th>Forme du CER</th>
						<th>Commentaire du Responsable</th>
						<th>Date de transfert au CG</th>

						<th>Validation CG (1ère lecture)</th>
						<th>Validation CS</th>
						<th>Validation Cadre</th>
						<th>Forme CER</th>
						<th>Commentaire du CG</th>
					</tr>
				</thead>';
			echo '<tbody>';
			foreach( $cers93 as $index => $cer93 ) {
				$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
					<tbody>
						<tr>
							<th>N° de dossier</th>
							<td>'.$cer93['Dossier']['numdemrsa'].'</td>
						</tr>
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

				if( isset( $cer93['Histochoixcer93etape03']['isrejet'] ) && ( $cer93['Histochoixcer93etape03']['isrejet'] == '1' ) ) {
					$validationcpdv = 'Rejeté';
				}
				else if( isset( $cer93['Histochoixcer93etape03']['isrejet'] ) && ( $cer93['Histochoixcer93etape03']['isrejet'] == '0' ) ) {
					$validationcpdv = 'Oui';
				}
				else {
					$validationcpdv = '';
				}

				$emetteurResponsable = array();
                foreach( array( 'Histochoixcer93etape03', 'Histochoixcer93etape04', 'Histochoixcer93etape05', 'Histochoixcer93etape06' ) as $etape ) {
                    if( !empty( $cer93[$etape]['User']['nom_complet'] ) ) {
                        $emetteurResponsable[$etape] = ' (émis par '.$cer93[$etape]['User']['nom_complet'].' )';
                    }
                }

				// TODO: on a le même genre de choses dans d'autres vues (secondelecture.ctp)
				$etape03 = Hash::extract( $cer93, 'Histochoixcer93etape03' );
				$commentairesEtape03 = '';
				if( isset( $etape03['id'] ) && isset( $etape03['Commentairenormecer93'] ) && !empty( $etape03['Commentairenormecer93'] ) ) {
					foreach( $etape03['Commentairenormecer93'] as $key => $commentairenorme ) {
						$commentaire = $commentairenorme['name'];
						if( $commentairenorme['isautre'] ) {
							$commentaire = "{$commentaire}: {$etape03['Commentairenormecer93Histochoixcer93'][$key]['commentaireautre']}";
						}
						$commentairesEtape03 .= "<li>{$commentaire}</li>";
					}
					$commentairesEtape03 = "<ul>{$commentairesEtape03}</ul>";
				}

				echo $this->Html->tableCells(
					array(
						$cer93['Adresse']['nomcom'],
						$this->Xhtml->link( $cer93['Cer93']['nonorientepdv'], array( 'controller' => 'orientsstructs', 'action' => 'index', $cer93['Contratinsertion']['personne_id'] ), array( 'class' => 'external' ) ),
						$cer93['Personne']['nom_complet_court'],
						$cer93['Structurereferente']['lib_struc'],
						$cer93['Referent']['nom_complet'],
						Set::enum( $cer93['Cer93']['positioncer_avantcg'], $options['Cer93']['positioncer'] ), //Saisie du CER
						$this->Xhtml->boolean( $cer93['NvTransfertpdv93']['encoursvalidation'] ),
						Set::enum( $cer93['Cer93']['validationcpdv'], $options['Cer93']['positioncer'] ).Hash::get( $emetteurResponsable, 'Histochoixcer93etape03' ), //Validation CPDV
						Set::enum( $cer93['Histochoixcer93etape03']['formeci'], $options['formeci'] ),
//						$cer93['Histochoixcer93etape03']['commentaire'],
						$commentairesEtape03,
						( $validationcpdv == 'Oui' ) ? date_short( $cer93['Histochoixcer93etape03']['datechoix'] ) : '',
						Set::enum( $cer93['Histochoixcer93etape04']['prevalide'], $options['Histochoixcer93']['prevalide'] ).Hash::get( $emetteurResponsable, 'Histochoixcer93etape04' ),
						Set::enum( $cer93['Histochoixcer93etape05']['decisioncs'], $options['Histochoixcer93']['decisioncs'] ).Hash::get( $emetteurResponsable, 'Histochoixcer93etape05' ),
						Set::enum( $cer93['Histochoixcer93etape06']['decisioncadre'], $options['Histochoixcer93']['decisioncadre'] ).Hash::get( $emetteurResponsable, 'Histochoixcer93etape06' ),
						Set::enum( $cer93['Histochoixcer93etape06']['formeci'], $options['formeci'] ),
						$cer93['Histochoixcer93']['commentaire'],
						$this->Xhtml->viewLink( 'Voir', array( 'controller' => 'cers93', 'action' => 'index', $cer93['Personne']['id'] ) ),
						array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
					),
					array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
					array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
				);
			}
			echo '</tbody>';
			echo '</table>';

			echo $pagination;
		}
// 		debug($cers93);
	}
?>
<?php if( isset( $cers93 ) ):?>
<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv', 'visualisation' ) + Hash::flatten( $this->request->data, '__' ),
			( $this->Permissions->check( 'cohortescers93', 'exportcsv' ) && count( $cers93 ) > 0 )
		);
	?></li>
</ul>
<?php endif;?>