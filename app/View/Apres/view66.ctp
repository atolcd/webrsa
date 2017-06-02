<?php $this->pageTitle = 'APRE/ADREs de '.Set::classicExtract( $apre, 'Personne.qual' ).' '.Set::classicExtract( $apre, 'Personne.nom' ).' '.Set::classicExtract( $apre, 'Personne.prenom' );?>
<h1><?php echo 'APRE/ADRE   de '.Set::classicExtract( $apre, 'Personne.qual' ).' '.Set::classicExtract( $apre, 'Personne.nom' ).' '.Set::classicExtract( $apre, 'Personne.prenom' );?></h1>

<?php
	$montantrestant = null;
	$montantaverser = Set::classicExtract( $apre, 'Apre66.montantaverser' );
	$montantdejaverse = Set::classicExtract( $apre, 'Apre66.montantdejaverse' );
	$montantrestant = ( $montantaverser - $montantdejaverse );
?>
<?php
		$etatdossierapre = Set::enum( $apre['Apre66']['etatdossierapre'], $options['etatdossierapre'] );
		$decisionapre = Set::enum( $apre['Aideapre66']['decisionapre'], $options['decisionapre'] );
		$theme = Set::enum( $apre['Aideapre66']['themeapre66_id'], $themes );
		$typeaide = Set::enum( $apre['Aideapre66']['typeaideapre66_id'], $nomsTypeaide );
		$versement = Set::enum( $apre['Aideapre66']['versement'], $options['versement'] );
		$virement = Set::enum( $apre['Aideapre66']['virement'], $options['virement'] );
		$activitebeneficiaire = Set::enum( $apre['Apre66']['activitebeneficiaire'], array( 'P' => 'Recherche d\'Emploi', 'E' => 'Emploi' , 'F' => 'Formation', 'C' => 'Création d\'Entreprise' ) );

// 		debug( $options );
		echo $this->Default2->view(
			$apre,
			array(
				'Apre66.numeroapre',
				'Apre66.structurereferente_id' => array( 'type' => 'text', 'value' => $apre['Structurereferente']['lib_struc'] ),
				'Apre66.referent_id' => array( 'type' => 'text', 'value' => $apre['Referent']['nom_complet'] ),
				'Apre66.activitebeneficiaire' => array( 'type' => 'text', 'value' => $activitebeneficiaire ),
				'Apre66.isbeneficiaire' => array( 'type' => 'boolean' ),
				'Apre66.hascer' => array( 'type' => 'boolean' ),
				'Apre66.respectdelais' => array( 'type' => 'boolean' ),
				'Aideapre66.themeapre66_id' => array( 'type' => 'text', 'value' => $theme ),
				'Aideapre66.typeaideapre66_id' => array( 'type' => 'text', 'value' => $typeaide ),
				'Aideapre66.motivdem' => array( 'type' => 'text' ),
				'Aideapre66.montantaide' => array( 'type' => 'text' ),
				'Aideapre66.virement' => array( 'type' => 'text', 'value' => $virement ),
				'Aideapre66.versement' => array( 'type' => 'text', 'value' => $versement ),
				'Aideapre66.creancier' => array( 'type' => 'text' ),
				'Aideapre66.datedemande' => array( 'type' => 'date' ),
				'Apre66.avistechreferent' => array( 'type' => 'text', 'label' => 'Observations du référent' ),
				'Aideapre66.montantpropose' => array( 'type' => 'text' ),
				'Aideapre66.datemontantpropose' => array( 'type' => 'date' ),
				'Aideapre66.decisionapre' => array( 'value' => $decisionapre ),
				'Aideapre66.montantaccorde' => array( 'type' => 'text' ),
				'Aideapre66.datemontantaccorde' => array( 'type' => 'date' ),
				'Aideapre66.motifrejet' => array( 'type' => 'text' ),
				'Aideapre66.motifrejetequipe' => array( 'type' => 'text' ),
				'Apre66.etatdossierapre' => array( 'value' => $etatdossierapre )
			),
			array(
				'class' => 'aere',
				'id' => 'vueContrat',
				'domain' => 'apre66'
			)
		);

?>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'apres66',
			'action'     => 'index',
			$personne_id
		),
		array(
			'id' => 'Back'
		)
	);
?>