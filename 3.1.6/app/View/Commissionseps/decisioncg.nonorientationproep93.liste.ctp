<?php
echo '<table id="Decisionnonorientationproep93" class="tooltips"><thead>
<tr>
<th>Nom du demandeur</th>
<th>Adresse</th>
<th>Date de naissance</th>
<th>Création du dossier EP</th>
<th>Date d\'orientation</th>
<th>Orientation actuelle</th>
<th>Avis EPL</th>
<th colspan="4">Décision CG</th>
<th>Observations</th>
<th>Action</th>
<th class="innerTableHeader noprint">Avis EP</th>
</tr>
</thead><tbody>';
foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$decisionep = @$dossierep['Passagecommissionep'][0]['Decisionnonorientationproep93'][1];
		$decisioncg = @$dossierep['Passagecommissionep'][0]['Decisionnonorientationproep93'][0];

		$avisep = @$options['Decisionnonorientationproep93']['decision'][Set::classicExtract( $decisionep, "decision" )];
		if ( $decisionep['decision'] == 'maintienref' ) {
			$avisep .= ' - '.$dossierep['Nonorientationproep93']['Orientstruct']['Typeorient']['lib_type_orient'].' - '.$dossierep['Nonorientationproep93']['Orientstruct']['Structurereferente']['lib_struc'];
		}
		else if ( $decisionep['decision'] == 'reorientation' ) {
			$avisep .= ' - '.$decisionep['Typeorient']['lib_type_orient'].' - '.$decisionep['Structurereferente']['lib_struc'];
		}

		$innerTable = "<table id=\"innerTableDecisionnonorientationproep93{$i}\" class=\"innerTable\">
			<tbody>
				<tr>
					<th>Observations de l'EP</th>
					<td>".Set::classicExtract( $decisionep, "commentaire" )."</td>
				</tr>
			</tbody>
		</table>";

		echo $this->Xhtml->tableCells(
			array(
				implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
				implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Dossierep']['created'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Nonorientationproep93']['Orientstruct']['date_valid'] ),
				implode( ' - ', Hash::filter( (array)array( $dossierep['Nonorientationproep93']['Orientstruct']['Typeorient']['lib_type_orient'], $dossierep['Nonorientationproep93']['Orientstruct']['Structurereferente']['lib_struc'], implode( ' ', Hash::filter( (array)array( @$dossierep['Nonorientationproep93']['Orientstruct']['Referent']['qual'], @$dossierep['Nonorientationproep93']['Orientstruct']['Referent']['nom'], @$dossierep['Nonorientationproep93']['Orientstruct']['Referent']['prenom'] ) ) ) ) ) ),

				$avisep,

				@$options['Decisionnonorientationproep93']['decisionpcg'][Set::classicExtract( $decisioncg, "decisionpcg" )],
				@array( $options['Decisionnonorientationproep93']['decision'][Set::classicExtract( $decisioncg, "decision" )], array( 'id' => "Decisionnonorientationproep93{$i}DecisionColumn" ) ),
				array( @$liste_typesorients[Set::classicExtract( $decisioncg, "typeorient_id" )], array( 'id' => "Decisionnonorientationproep93{$i}TypeorientId" ) ),
				array( @$liste_structuresreferentes[Set::classicExtract( $decisioncg, "structurereferente_id" )], array( 'id' => "Decisionnonorientationproep93{$i}StructurereferenteId" ) ),
				Set::classicExtract( $decisioncg, "commentaire" ),
				$this->Xhtml->printLink( 'Imprimer', array( 'controller' => 'commissionseps', 'action' => 'impressionDecision', $dossierep['Passagecommissionep'][0]['id'] ), ( $commissionep['Commissionep']['etatcommissionep'] != 'annule' ) ),
				array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
			),
			array( 'class' => "odd {$multiple}" ),
			array( 'class' => "even {$multiple}" )
		);
	}
	echo '</tbody></table>';
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php for( $i = 0 ; $i < count( $dossiers[$theme]['liste'] ) ; $i++ ):?>
			changeColspanViewInfosEps( 'Decisionnonorientationproep93<?php echo $i;?>DecisionColumn', '<?php echo Set::classicExtract( $dossiers, "{$theme}.liste.{$i}.Passagecommissionep.0.Decisionnonorientationproep93.0.decision" );?>', 3, [ 'Decisionnonorientationproep93<?php echo $i;?>TypeorientId', 'Decisionnonorientationproep93<?php echo $i;?>StructurereferenteId' ] );
		<?php endfor;?>
	});
</script>