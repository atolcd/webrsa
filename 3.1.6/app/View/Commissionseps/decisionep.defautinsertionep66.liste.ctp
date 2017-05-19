<?php
echo '<table>
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup span="4" style="border-right: 5px solid #235F7D;border-left: 5px solid #235F7D;" />
		<colgroup />
		<colgroup />
		<thead>
			<tr>
				<th rowspan="2">Nom du demandeur</th>
				<th rowspan="2">Adresse</th>
				<th rowspan="2">Date de naissance</th>
				<th rowspan="2">Date d\'orientation</th>
				<th rowspan="2">Orientation actuelle</th>
				<th rowspan="2">Structure</th>
				<th rowspan="2">Motif saisine</th>
				<th colspan="4">Avis EPL</th>
				<th rowspan="2">Action</th>
			</tr>
			<tr>
				<th>Avis</th>
				<th>Type d\'orientation</th>
				<th>Structure référente</th>
				<th>Référent</th>
			</tr>
		</thead>

	<tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$examenaudition = Set::enum( @$dossierep['Defautinsertionep66']['Bilanparcours66']['examenaudition'], $options['Defautinsertionep66']['type'] );
		if( !empty( $dossierep['Defautinsertionep66']['Bilanparcours66']['examenauditionpe'] ) ){
			$examenaudition = Set::enum( @$dossierep['Defautinsertionep66']['Bilanparcours66']['examenauditionpe'], $options['Bilanparcours66']['examenauditionpe'] );
		}

		$decisionep = $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0];
		
		$decisioncg = @$dossierep['Personne']['Foyer']['Dossierpcg66'][0]['Decisiondossierpcg66'][0]['Decisionpdo'];

		echo $this->Xhtml->tableCells(
			array(
				implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
				implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['codepos'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomcom'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
				$this->Locale->date( __( 'Locale->date' ), @$dossierep['Defautinsertionep66']['Orientstruct']['date_valid'] ),
				@$dossierep['Defautinsertionep66']['Orientstruct']['Typeorient']['lib_type_orient'],
				@$dossierep['Defautinsertionep66']['Orientstruct']['Structurereferente']['lib_struc'],

				$examenaudition,
				
				array( implode( ' / ', Hash::filter( (array)array(
					$options['Decisiondefautinsertionep66']['decision'][Set::classicExtract( $decisionep, "decision" )],
					@$options['Decisiondefautinsertionep66']['decisionsup'][Set::classicExtract( $decisionep, "decisionsup" )]
				) ) ), array( 'id' => "Decisiondefautinsertionep66{$i}DecisionColumn" ) ),

				array( @$liste_typesorients[Set::classicExtract( $decisionep, "typeorient_id" )], array( 'id' => "Decisiondefautinsertionep66{$i}TypeorientId" ) ),
				array( @$liste_structuresreferentes[Set::classicExtract( $decisionep, "structurereferente_id" )], array( 'id' => "Decisiondefautinsertionep66{$i}StructurereferenteId" ) ),
				array( @$liste_referents[Set::classicExtract( $decisionep, "referent_id" )], array( 'id' => "Decisiondefautinsertionep66{$i}ReferentId" ) ),
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
			changeColspanViewInfosEps( 'Decisiondefautinsertionep66<?php echo $i;?>DecisionColumn', '<?php echo Set::classicExtract( $dossiers, "{$theme}.liste.{$i}.Passagecommissionep.0.Decisiondefautinsertionep66.0.decision" );?>', 4, [ 'Decisiondefautinsertionep66<?php echo $i;?>TypeorientId', 'Decisiondefautinsertionep66<?php echo $i;?>StructurereferenteId', 'Decisiondefautinsertionep66<?php echo $i;?>ReferentId' ] );
		<?php endfor;?>
	});
</script>