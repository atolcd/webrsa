<?php $this->pageTitle = 'APREs';?>

<h1><?php echo 'APRE  ';?></h1>

<?php
	$montantrestant = null;
	$montantaverser = Set::classicExtract( $apre, 'Apre.montantaverser' );
	$montantdejaverse = Set::classicExtract( $apre, 'Apre.montantdejaverse' );
	$montantrestant = ( $montantaverser - $montantdejaverse );

	$typedemandeapre = Set::enum( $apre['Apre']['typedemandeapre'], $options['typedemandeapre'] );
	$naturelogement = Set::enum( $apre['Apre']['naturelogement'], $options['naturelogement'] );
	$activitebeneficiaire = Set::enum( $apre['Apre']['activitebeneficiaire'], $options['activitebeneficiaire'] );
	$typecontrat = Set::enum( $apre['Apre']['typecontrat'], $options['typecontrat'] );
	$statutapre = Set::enum( $apre['Apre']['statutapre'], $options['statutapre'] );
	$etatdossierapre = Set::enum( $apre['Apre']['etatdossierapre'], $options['etatdossierapre'] );
	$eligibiliteapre = Set::enum( $apre['Apre']['eligibiliteapre'], $options['eligibiliteapre'] );
	$justificatif = Set::enum( $apre['Apre']['justificatif'], $options['justificatif'] );
	$isdecision = Set::enum( $apre['Apre']['isdecision'], $options['isdecision'] );
	$cessderact = Set::enum( $apre['Apre']['cessderact'], $optionsdsps['cessderact'] );
	$sect_acti_emp = Set::enum( $apre['Apre']['secteuractivite'], $sect_acti_emp );

	echo $this->Default2->view(
		$apre,
		array(
			'Personne.nom_complet' => array( 'type' => 'text' ),
			'Apre.numeroapre',
			'Apre.typedemandeapre' => array( 'value' => $typedemandeapre ),
			'Apre.datedemandeapre',
			'Apre.naturelogement' => array( 'value' => $naturelogement ),
			'Apre.precisionsautrelogement',
			'Apre.anciennetepoleemploi' => array( 'type' => 'text' ),
			'Apre.projetprofessionnel' => array( 'type' => 'text' ),
			'Apre.secteurprofessionnel' => array( 'type' => 'text' ),
			'Apre.activitebeneficiaire' => array( 'value' => $activitebeneficiaire ),
			'Apre.dateentreeemploi',
			'Apre.typecontrat' => array( 'value' => $typecontrat ),
			'Apre.precisionsautrecontrat' => array( 'type' => 'text' ),
			'Apre.nbheurestravaillees' => array( 'type' => 'text' ),
			'Apre.nomemployeur' => array( 'type' => 'text' ),
			'Apre.adresseemployeur' => array( 'type' => 'text' ),
			'Apre.avistechreferent' => array( 'type' => 'text' ),
			'Apre.etatdossierapre' => array( 'value' => $etatdossierapre ),
			'Apre.eligibiliteapre' => array( 'value' => $eligibiliteapre ),
			'Apre.secteuractivite' => array( 'type' => 'text', 'value' => $sect_acti_emp ),
			'Apre.nbenf12' => array( 'type' => 'text' ),
			'Apre.statutapre' => array( 'value' => $statutapre ),
			'Apre.justificatif' => array( 'value' => $justificatif ),
			'Apre.structurereferente_id' => array( 'type' => 'text', 'value' => $apre['Structurereferente']['lib_struc'] ),
			'Apre.referent_id' => array( 'type' => 'text', 'value' => $apre['Referent']['nom_complet'] ),
			'Apre.montantaverser' => array( 'type' => 'text' ),
			'Apre.nbpaiementsouhait' => array( 'type' => 'text' ),
			'Apre.montantdejaverse' => array( 'type' => 'text' ),
			'Apre.cessderact' => array( 'value' => $cessderact  )/*,
			'Apre.isdecision' => array( 'value' => $isdecision )*/
		),
		array(
			'class' => 'aere',
			'id' => 'vueContrat',
			'domain' => 'apre'
		)
	);

	if( Configure::read( 'Cg.departement' ) == 93 ) {
		echo '<h2>Liste des décisions de comités d\'APRE</h2>';
		if( isset( $apre['Comiteapre'] ) && !empty( $apre['Comiteapre'] ) ) {
			foreach( $apre['Comiteapre'] as $key => $comite ){
				echo $this->Default2->view(
					$comite,
					array(
						'Comiteapre.datecomite' => array( 'domain' => 'apre', 'value' => Set::classicExtract( $comite, 'datecomite' ) ),
						'ApreComiteapre.decisioncomite' => array( 'domain' => 'apre', 'type' => 'text', 'value' => Set::enum( Set::classicExtract( $comite, 'ApreComiteapre.decisioncomite' ), $optionsaprecomite['decisioncomite'] ) )
					),
					array(
						'class' => 'aere',
						'domain' => 'apre'
					)
				);
			}
		}
		else{
			echo '<p class="notice">Aucune décision émise pour le moment</p>';
		}
	}

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'apres',
			'action'     => 'index',
			$personne_id
		),
		array(
			'id' => 'Back'
		)
	);
?>