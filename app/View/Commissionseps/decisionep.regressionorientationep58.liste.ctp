<?php
echo '<table><thead>
<tr>
<th>Nom du demandeur</th>
<th>Adresse</th>
<th>Date de naissance</th>
<th>Création du dossier EP</th>
<th colspan="2">Orientation actuelle</th>
<th colspan="2">Proposition référent</th>
<th colspan="4">Avis EPL</th>
<th>Observations</th>
<th>Action</th>
</tr>
</thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$decisionep = @$dossierep['Passagecommissionep'][0]['Decisionregressionorientationep58'][0];

		echo $this->Xhtml->tableCells(
			array(
				implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
				implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Dossierep']['created'] ),
				@$dossierep['Personne']['Orientstruct'][0]['Typeorient']['lib_type_orient'],
				@$dossierep['Personne']['Orientstruct'][0]['Structurereferente']['lib_struc'],
				@$dossierep['Regressionorientationep58']['Typeorient']['lib_type_orient'],
				@$dossierep['Regressionorientationep58']['Structurereferente']['lib_struc'],

				array( @$options['Decisionregressionorientationep58']['decision'][Set::classicExtract( $decisionep, "decision" )], array( 'id' => "Decisionregressionorientationep58{$i}DecisionColumn" ) ),
				array( @$liste_typesorients[Set::classicExtract( $decisionep, "typeorient_id" )], array( 'id' => "Decisionregressionorientationep58{$i}TypeorientId" ) ),
				array( @$liste_structuresreferentes[Set::classicExtract( $decisionep, "structurereferente_id" )], array( 'id' => "Decisionregressionorientationep58{$i}StructurereferenteId" ) ),
				array( @$liste_referents[Set::classicExtract( $decisionep, "referent_id" )], array( 'id' => "Decisionregressionorientationep58{$i}ReferentId" ) ),
				Set::classicExtract( $decisionep, "commentaire" ),
				$this->Xhtml->printLink( 'Imprimer', array( 'controller' => 'commissionseps', 'action' => 'impressionDecision', $dossierep['Passagecommissionep'][0]['id'] ), ( $commissionep['Commissionep']['etatcommissionep'] != 'annule' ) ),
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
			changeColspanViewInfosEps(
				'Decisionregressionorientationep58<?php echo $i;?>DecisionColumn',
				'<?php echo Set::classicExtract( $dossiers, "{$theme}.liste.{$i}.Passagecommissionep.0.Decisionregressionorientationep58.0.decision" );?>',
				4,
				[
					'Decisionregressionorientationep58<?php echo $i;?>TypeorientId',
					'Decisionregressionorientationep58<?php echo $i;?>StructurereferenteId',
					'Decisionregressionorientationep58<?php echo $i;?>ReferentId'
				],
				[ 'reporte', 'annule', 'suspensionnonrespect', 'suspensiondefaut' ]
			);
		<?php endfor;?>
	});
</script>