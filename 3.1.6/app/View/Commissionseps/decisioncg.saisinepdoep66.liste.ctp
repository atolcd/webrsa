<?php
echo '<table id="Decisionsaisinepdoep66" class="tooltips"><thead>
<tr>
<th>Nom du demandeur</th>
<th>Adresse</th>
<th>Date de naissance</th>
<th>Création du dossier EP</th>
<th>Motif(s) de la PDO</th>
<th>Description du traitement</th>
<th colspan=\'2\'>Avis de l\'EP</th>
<th>Décision du CG</th>
<th>Observations</th>
<th>Action</th>
<th class="innerTableHeader noprint">Avis EP</th>
</tr>
</thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$decisionep = @$dossierep['Passagecommissionep'][0]['Decisionsaisinepdoep66'][1];
		$decisioncg = @$dossierep['Passagecommissionep'][0]['Decisionsaisinepdoep66'][0];

		$listeSituationPdo = array();
		foreach($dossierep['Saisinepdoep66']['Traitementpdo']['Propopdo']['Situationpdo'] as $situationpdo) {
			$listeSituationPdo[] = $situationpdo['libelle'];
		}

		$innerTable = "<table id=\"innerTableDecisionsaisinepdoep66{$i}\" class=\"innerTable\">
			<tbody>
				<tr>
					<th>Observations de l'EP</th>
					<td>".Set::classicExtract( $decisionep, "commentaire" )."</td>
				</tr>
			</tbody>
		</table>";

		if ( $decisioncg['decision'] == 'annule' || $decisioncg['decision'] == 'reporte' ) {
			$cg = $options['Decisionsaisinepdoep66']['decision'][$decisioncg['decision']];
		}
		else {
			$cg = @$options['Decisionsaisinepdoep66']['decisionpdo_id'][Set::classicExtract( $decisioncg, "decisionpdo_id" )].' au '.Set::classicExtract( $decisioncg, "datedecisionpdo");
		}

		echo $this->Xhtml->tableCells(
			array(
				implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
				implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Dossierep']['created'] ),
				implode(' / ', $listeSituationPdo),
				$dossierep['Saisinepdoep66']['Traitementpdo']['Descriptionpdo']['name'],

				array( @$options['Decisionsaisinepdoep66']['decision'][Set::classicExtract( $decisioncg, "decision" )], array( 'id' => "Decisionsaisinepdoep66{$i}DecisionColumn" ) ),

				@$decisioncg['Decisionpdo']['libelle'],
				array( $cg, array( 'id' => "Decisionsaisinepdoep66{$i}Decisioncg" ) ),
				Set::classicExtract( $decisioncg, "commentaire" ),
				array( $this->Xhtml->link( 'Voir', array( 'controller' => 'historiqueseps', 'action' => 'view_passage', $dossierep['Passagecommissionep'][0]['id'] ), array( 'class' => 'external' ) ), array( 'class' => 'button view' ) ),
				array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
			),
			array( 'class' => "odd {$multiple}" ),
			array( 'class' => "even {$multiple}" )
		);
	}
	echo '</tbody></table>';

?>