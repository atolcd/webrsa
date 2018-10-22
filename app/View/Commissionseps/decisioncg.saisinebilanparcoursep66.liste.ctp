<?php
	echo '<ul class="actions">';
	echo '<li>'.$this->Xhtml->link(
		__d( 'commissionep','Commissionseps::impressionsDecisions' ),
		array( 'controller' => 'commissionseps', 'action' => 'impressionsDecisions', $commissionep['Commissionep']['id'] ),
		array( 'class' => 'button impressionsDecisions', 'enabled' => $commissionep['Commissionep']['etatcommissionep'] != 'annule' ),
        'Etes-vous sûr de vouloir imprimer les décisions ?'
	).' </li>';
	echo '</ul>';

echo '<table id="Decisionsaisinebilanparcoursep66">
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup span="3" style="border-right: 5px solid #235F7D;border-left: 5px solid #235F7D;" />
		<colgroup />
		<colgroup span="5" style="border-right: 5px solid #235F7D;border-left: 5px solid #235F7D;" />
		<colgroup />
		<colgroup span="2"/>
		<colgroup />
		<thead>
			<tr>
				<th rowspan="2">Nom du demandeur</th>
				<th rowspan="2">Adresse</th>
				<th rowspan="2">Date de naissance</th>
				<th rowspan="2">Création du dossier EP</th>
				<th rowspan="2">Orientation actuelle</th>
				<th colspan="3" rowspan="2">Proposition référent</th>
				<th rowspan="2">Avis EPL</th>
				<th colspan="5">Décision coordonnateur/CG</th>
				<th colspan="2" rowspan="2">Actions</th>
			</tr>
			<tr>
				<th>Décision</th>
				<th>SOCIAL/Emploi</th>
				<th>Type d\'orientation</th>
				<th>Structure référente</th>
				<th>Référent</th>
			</tr>
		</thead>
	<tbody>';

	$typeorientprincipaleSocial = (array)Configure::read( 'Orientstruct.typeorientprincipale.SOCIAL' );
	$typeorientprincipaleEmploi = (array)Configure::read( 'Orientstruct.typeorientprincipale.Emploi' );

	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$decisionep = @$dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][1];
		$decisioncg = @$dossierep['Passagecommissionep'][0]['Decisionsaisinebilanparcoursep66'][0];

		$listeFields = array(
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
			@$options['Saisinebilanparcoursep66']['choixparcours'][Set::classicExtract( $dossierep, "Saisinebilanparcoursep66.choixparcours" )],
			@$liste_typesorients[Set::classicExtract( $dossierep, "Saisinebilanparcoursep66.typeorient_id" )],
			@$liste_structuresreferentes[Set::classicExtract( $dossierep, "Saisinebilanparcoursep66.structurereferente_id" )]
		);

		if(isset($dossierep["Passagecommissionep"][0]["Decisionsaisinebilanparcoursep66"][0]["commentaire"]))
			$listeFields[]	=	$dossierep["Passagecommissionep"][0]["Decisionsaisinebilanparcoursep66"][0]["commentaire"];
		else
			$listeFields[]	=	'';

		$enabled = ( $commissionep['Commissionep']['etatcommissionep'] != 'annule' );

		$typeorientPrincipale = null;
		if( in_array( @$decisioncg['Typeorient']['parentid'], $typeorientprincipaleSocial ) ) {
			$typeorientPrincipale = 'SOCIAL';
		}
		else if( in_array( @$decisioncg['Typeorient']['parentid'], $typeorientprincipaleEmploi ) ) {
			$typeorientPrincipale = 'Emploi';
		}

		echo $this->Xhtml->tableCells(
			array_merge(
				$listeFields,
				array(
					array( @$options['Decisionsaisinebilanparcoursep66']['decision'][Set::classicExtract( $decisioncg, "decision" )], array( 'id' => "Decisionsaisinebilanparcoursep66{$i}DecisionColumn" ) ),
					$typeorientPrincipale,
					array( @$liste_typesorients[Set::classicExtract( $decisioncg, "typeorient_id" )], array( 'id' => "Decisionsaisinebilanparcoursep66{$i}TypeorientId" ) ),
					array( @$liste_structuresreferentes[Set::classicExtract( $decisioncg, "structurereferente_id" )], array( 'id' => "Decisionsaisinebilanparcoursep66{$i}StructurereferenteId" ) ),
					array( @$liste_referents[Set::classicExtract( $decisioncg, "referent_id" )], array( 'id' => "Decisionsaisinebilanparcoursep66{$i}ReferentId" ) ),
// 					array( Set::classicExtract( $decisioncg, "commentaire" ), array( 'id'  => "Decisionsaisinebilanparcoursep66{$i}Commentaire") ),
					array( $this->Xhtml->link( 'Voir', array( 'controller' => 'historiqueseps', 'action' => 'view_passage', $dossierep['Passagecommissionep'][0]['id'] ), array( 'class' => 'external' ) ), array( 'class' => 'button view' ) ),
					$this->Xhtml->printLink( 'Imprimer', array( 'controller' => 'commissionseps', 'action' => 'impressionDecision', $dossierep['Passagecommissionep'][0]['id'] ), ( $enabled ) ),
// 					array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
				)
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
			changeColspanViewInfosEps( 'Decisionsaisinebilanparcoursep66<?php echo $i;?>DecisionColumn', '<?php echo Set::classicExtract( $dossiers, "{$theme}.liste.{$i}.Passagecommissionep.0.Decisionsaisinebilanparcoursep66.0.decision" );?>', 4, [ 'Decisionsaisinebilanparcoursep66<?php echo $i;?>TypeorientId', 'Decisionsaisinebilanparcoursep66<?php echo $i;?>StructurereferenteId', 'Decisionsaisinebilanparcoursep66<?php echo $i;?>ReferentId' ] );
		<?php endfor;?>
	});
</script>