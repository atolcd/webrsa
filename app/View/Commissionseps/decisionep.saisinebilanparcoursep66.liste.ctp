<?php
echo '<table>
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup span="3" style="border-right: 5px solid #235F7D;border-left: 5px solid #235F7D;" />
		<colgroup span="4" style="border-right: 5px solid #235F7D;border-left: 5px solid #235F7D;" />
		<colgroup />
		<thead>
			<tr>
				<th rowspan="2">Nom du demandeur</th>
				<th rowspan="2">Adresse</th>
				<th rowspan="2">Date de naissance</th>
				<th rowspan="2">Création du dossier EP</th>
				<th rowspan="2">Orientation actuelle</th>
				<th colspan="3" rowspan="2">Proposition référent</th>
				<th colspan="4">Avis EPL</th>
				<th rowspan="2">Action</th>
			</tr>
			<tr>
				<th rowspan="2">Avis</th>
				<th rowspan="2">Type d\'orientation</th>
				<th rowspan="2">Structure référente</th>
				<th rowspan="2">Référent</th>
			</tr>
	</thead>
<tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$decisionep = $dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0];

		echo $this->Xhtml->tableCells(
			array(
				implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
				implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Dossierep']['created'] ),
				implode( ' - ', Hash::filter( (array)array(
					@$dossierep['Saisinebilanparcoursep66']['Bilanparcours66']['Orientstruct']['Typeorient']['lib_type_orient'],
					@$dossierep['Saisinebilanparcoursep66']['Bilanparcours66']['Orientstruct']['Structurereferente']['lib_struc'],
					Hash::filter( (array)array(
						@$dossierep['Saisinebilanparcoursep66']['Bilanparcours66']['Orientstruct']['Referent']['qual'],
						@$dossierep['Saisinebilanparcoursep66']['Bilanparcours66']['Orientstruct']['Referent']['nom'],
						@$dossierep['Saisinebilanparcoursep66']['Bilanparcours66']['Orientstruct']['Referent']['prenom']
					) )
				) ) ),
				$options['Saisinebilanparcoursep66']['choixparcours'][Set::classicExtract( $dossierep, "Saisinebilanparcoursep66.choixparcours" )],
				@$liste_typesorients[Set::classicExtract( $dossierep, "Saisinebilanparcoursep66.typeorient_id" )],
				@$liste_structuresreferentes[Set::classicExtract( $dossierep, "Saisinebilanparcoursep66.structurereferente_id" )],
				array( $options['Decisionsaisinebilanparcoursep66']['decision'][Set::classicExtract( $decisionep, "decision" )], array( 'id' => "Decisionsaisinebilanparcoursep66{$i}DecisionColumn" ) ),
				array( @$liste_typesorients[Set::classicExtract( $decisionep, "typeorient_id" )], array( 'id' => "Decisionsaisinebilanparcoursep66{$i}TypeorientId" ) ),
				array( @$liste_structuresreferentes[Set::classicExtract( $decisionep, "structurereferente_id" )], array( 'id' => "Decisionsaisinebilanparcoursep66{$i}StructurereferenteId" ) ),
				array( @$liste_referents[Set::classicExtract( $decisionep, "referent_id" )], array( 'id' => "Decisionsaisinebilanparcoursep66{$i}ReferentId" ) ),
				array( $this->Xhtml->link( 'Voir', array( 'controller' => 'dossiers', 'action' => 'view', $dossierep['Personne']['Foyer']['dossier_id'] ), array( 'class' => 'external' ) ), array( 'class' => 'button view' ) )
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
			changeColspanViewInfosEps( 'Decisionsaisinebilanparcoursep66<?php echo $i;?>DecisionColumn', '<?php echo Set::classicExtract( $dossiers, "{$theme}.liste.{$i}.Passagecommissionep.0.Decisionsaisinebilanparcoursep66.0.decision" );?>', 3, [ 'Decisionsaisinebilanparcoursep66<?php echo $i;?>TypeorientId', 'Decisionsaisinebilanparcoursep66<?php echo $i;?>StructurereferenteId' ] );
		<?php endfor;?>
	});
</script>