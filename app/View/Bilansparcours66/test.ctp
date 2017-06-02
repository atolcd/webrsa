<?php $personne_id = Set::classicExtract( $this->request->params, 'pass.0' ); ?>
<?php
	$this->pageTitle = 'Bilan de parcours de la personne';
debug($urlmenu);
?>
<h1><?php echo $this->pageTitle;?></h1>

	<?php
		echo $this->element( 'ancien_dossier' );

		echo "<ul class='actions'><li class='add'>";
			echo $this->Default2->button('add', array('controller'=>'bilansparcours66', 'action'=>'add', $personne_id));
		echo "</li></ul>";

		if( empty( $bilansparcours66 ) ){
			echo '<p class="notice">Aucun bilan de parcours présent pour cette personne.</p>';
		}
		else{
			echo '<table class="tooltips default2"  id="searchResults"><thead><tr>';
				echo "<th>".__d( 'bilanparcours66', 'Bilanparcours66.datebilan' )."</th>";
				echo "<th>".__d( 'bilanparcours66', 'Bilanparcours66.positionbilan' )."</th>";
				echo "<th>MSP</th>";
				echo "<th>".__d( 'structurereferente', 'Structurereferente.lib_struc' )."</th>";
				echo "<th>Nom du référent</th>";
				echo "<th>".__d( 'bilanparcours66', 'Bilanparcours66.proposition' )."</th>";
				echo "<th>Motif de la saisine</th>";
				echo "<th colspan='2'>".__d( 'saisinebilanparcoursep66', 'Saisinebilanparcoursep66.propref' )."</th>";
				echo "<th colspan='2'>".__d( 'saisinebilanparcoursep66', 'Saisinebilanparcoursep66.avisep' )."</th>";
				echo "<th colspan='2'>".__d( 'saisinebilanparcoursep66', 'Saisinebilanparcoursep66.decisioncg' )."</th>";
				echo "<th colspan='7'>Actions</th>";
				echo "</tr></thead><tbody>";

			foreach($bilansparcours66 as $key => $bilanparcours66) {

				// Infobulle
				$innerTable = '<table id="innerTablesearchResults'.$key.'" class="innerTable">
						<tbody>
							<tr>
								<th>Raison annulation</th>
								<td>'.$bilanparcours66['Bilanparcours66']['motifannulation'].'</td>
							</tr>
						</tbody>
					</table>';

				echo "<tr>\n";

				// Date du bilan de parcours, Position du bilan ...
				$data = array(
					date_short( $bilanparcours66['Bilanparcours66']['datebilan'] ),
					value( $options['Bilanparcours66']['positionbilan'], $bilanparcours66['Bilanparcours66']['positionbilan'] ),
					$bilanparcours66['Serviceinstructeur']['lib_service'], // MSP
					$bilanparcours66['Structurereferente']['lib_struc'], // Type de structure
					$bilanparcours66['Referent']['nom_complet'], // Nom du référent
				);

				// Type de commission
				$data[5] = $bilanparcours66['Bilanparcours66']['proposition'] ? value( $options['Bilanparcours66']['proposition'], $bilanparcours66['Bilanparcours66']['proposition'] ) : '&nbsp;';

				// Motif de la saisine
				$data[6] =
					( $bilanparcours66['Bilanparcours66']['examenauditionpe'] ) ?
						value( $options['Bilanparcours66']['examenauditionpe'], $bilanparcours66['Bilanparcours66']['examenauditionpe'] ) :
					(( $bilanparcours66['Bilanparcours66']['examenaudition'] ) ?
						value( $options['Bilanparcours66']['examenaudition'], $bilanparcours66['Bilanparcours66']['examenaudition'] ) :
					(( $bilanparcours66['Bilanparcours66']['choixparcours'] ) ?
						value( $options['Bilanparcours66']['choixparcours'], $bilanparcours66['Bilanparcours66']['choixparcours'] ) :
					'&nbsp;'));

				// Proposition du referent
				$data[7] = ( $bilanparcours66['Propotypeorient']['lib_type_orient'] ) ?
					array( $bilanparcours66['Propotypeorient']['lib_type_orient'], $bilanparcours66['Propostructurereferente']['lib_struc'] ) :
					'&nbsp;'
				;

				// Avis de l'EP
				if ( $bilanparcours66['Avisdefautinsertionep66']['decision'] ){
					$avis = '';

					if ($bilanparcours66['Avisdefautinsertionep66']['decisionsup']){
						$avis = __d( 'decisiondefautinsertionep66', 'ENUM::DECISION::' . $bilanparcours66['Avisdefautinsertionep66']['decisionsup'] ) . ' - ';
					}

					$avis .= __d( 'decisiondefautinsertionep66', 'ENUM::DECISION::' . $bilanparcours66['Avisdefautinsertionep66']['decision'] );
					$data[8] = $avis;
				}
				elseif ( in_array( $bilanparcours66['Avissaisinebilanparcoursep66']['decision'], array( 'maintien', 'annule', 'reporte' ) ) ){
					$data[8] = value($options['Decisionsaisinebilanparcoursep66']['decision'], $bilanparcours66['Avissaisinebilanparcoursep66']['decision']);
				}
				elseif ( $bilanparcours66['Avistypeorient']['lib_type_orient'] ){
					$data[8] = array( $bilanparcours66['Avistypeorient']['lib_type_orient'], $bilanparcours66['Avisstructurereferente']['lib_struc'] );
				}
				else{
					$data[8] = '&nbsp;';
				}


				// Decision du CG
				if ($bilanparcours66['Decisiondefautinsertionep66']['decision']){
					$avis = __d( 'decisiondefautinsertionep66', 'ENUM::DECISION::' . $bilanparcours66['Decisiondefautinsertionep66']['decision'] );

					if ($bilanparcours66['Decisiondefautinsertionep66']['decisionsup']){
						$avis .= __d( 'decisiondefautinsertionep66', 'ENUM::DECISION::' . $bilanparcours66['Decisiondefautinsertionep66']['decisionsup'] ) . ' - ';
					}

					if ($bilanparcours66['Decisionpdo']['libelle']){
						$avis .= '<br /><br />CGA : ' . $bilanparcours66['Decisionpdo']['libelle'];
					}

					$data[9] = $avis;
				}
				elseif ( in_array( $bilanparcours66['Decisionsaisinebilanparcoursep66']['decision'], array( 'maintien', 'annule', 'reporte' ) ) ){
					$data[9] = value($options['Decisionsaisinebilanparcoursep66']['decision'], $bilanparcours66['Decisionsaisinebilanparcoursep66']['decision']);
				}
				elseif ( $bilanparcours66['Decisiontypeorient']['lib_type_orient'] ){
					$data[9] = array( $bilanparcours66['Decisiontypeorient']['lib_type_orient'], $bilanparcours66['Decisionstructurereferente']['lib_struc'] );
				}
				else{
					$data[9] = '&nbsp;';
				}

				$block = !( $bilanparcours66['Bilanparcours66']['positionbilan'] == 'annule' );
				$epparcours = !( in_array( $bilanparcours66['Bilanparcours66']['proposition'], array( 'audition', 'auditionpe' ) ) && !empty( $bilanparcours66['Defautinsertionep66']['dateimpressionconvoc'] ) );

				foreach($data as $key => $val){
					if ( is_array($val) ){
						echo '<td>' . $val[0] . '</td>' . '<td>' . $val[1] . '</td>';
					}
					else{
						$colspan = '';

						if ($key >= 7){
							$colspan = ' colspan="2"';
						}

						if (!$val) $val = '&nbsp;';

						echo '<td' . $colspan . '>'.$val.'</td>';
					}
				}

				echo $this->Xhtml->tag(
					'td',
					$this->Default2->button(
						'view',
						array( 'controller' => 'bilansparcours66', 'action' => 'view',
						$bilanparcours66['Bilanparcours66']['id']),
						array(
							'enabled' => (
								( $this->Permissions->checkDossier( 'bilansparcours66', 'view', $dossierMenu ) == 1 )
								&& $block
							)
						)
					)
				);
				echo $this->Xhtml->tag(
					'td',
					$this->Default2->button(
						'edit',
						array( 'controller' => 'bilansparcours66', 'action' => 'edit',
						$bilanparcours66['Bilanparcours66']['id']),
						array(
							'enabled' => (
								( $this->Permissions->checkDossier( 'bilansparcours66', 'edit', $dossierMenu ) == 1 )
								&& $block
							)
						)
					)
				);
				echo $this->Xhtml->tag(
					'td',
					$this->Default2->button(
						'print',
						array( 'controller' => 'bilansparcours66', 'action' => 'impression',
						$bilanparcours66['Bilanparcours66']['id']),
						array(
							'enabled' => (
								( $this->Permissions->checkDossier( 'bilansparcours66', 'impression', $dossierMenu ) == 1 )
								&& $block
							)
						)
					)
				);

				$enabled = (( $this->Permissions->checkDossier( 'manifestationsbilansparcours66', 'index', $dossierMenu ) == 1 )
								&& $block
								&& !$epparcours
				);

				$manif = $this->Xhtml->tag(
					'td',
					$this->Default2->button(
						'manifestation',
						array( 'controller' => 'manifestationsbilansparcours66', 'action' => 'index',
						$bilanparcours66['Bilanparcours66']['id'] ),
						array(
							'label' => 'Manifestations',
							'enabled' => $enabled
						)
					)
				);

				// Ajout du nombre de manifestations
				$cutPos = ( $enabled ) ? strpos($manif, '</a>') : strpos($manif, '</span>');

				echo substr($manif, 0, $cutPos) . '&nbsp;(' . $bilanparcours66['Bilanparcours66']['nb_manifestations'] . ')' . substr($manif, $cutPos);

				echo $this->Xhtml->tag(
					'td',
					$this->Default2->button(
						'cancel',
						array( 'controller' => 'bilansparcours66', 'action' => 'cancel',
						$bilanparcours66['Bilanparcours66']['id']),
						array(
							'enabled' => (
								( $this->Permissions->checkDossier( 'bilansparcours66', 'cancel', $dossierMenu ) == 1 )
								&& $block
							)
						)
					)
				);
				echo $this->Xhtml->tag(
					'td',
					$this->Default2->button(
						'filelink',
						array( 'controller' => 'bilansparcours66', 'action' => 'filelink',
						$bilanparcours66['Bilanparcours66']['id']),
						array(
							'enabled' => (
								( $this->Permissions->checkDossier( 'bilansparcours66', 'filelink', $dossierMenu ) == 1 )
							)
						)
					)
				);
				echo $this->Xhtml->tag(
					'td',
					'('.$bilanparcours66['Fichiermodule']['nb_fichiers'].')'
				);
				echo $this->Xhtml->tag(
					'td',
					$innerTable,
					array( 'class' => 'innerTableCell noprint' )
				);

				echo "</tr>\n";

			}
			echo "</tbody></table>";
		}
	?>
