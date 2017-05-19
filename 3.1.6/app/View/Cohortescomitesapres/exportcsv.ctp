<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow( array( 'N° dossier RSA', __d( 'dossier', 'Dossier.matricule' ), 'Nom/Prénom allocataire', 'Commune de l\'allocataire',  'Date demande APRE', 'Décision du comité', 'Date de décision', 'Montant attribué', 'Observations' ) );

	foreach( $decisionscomites as $decisioncomite ) {

		$row = array(
			Set::classicExtract( $decisioncomite, 'Dossier.numdemrsa' ),
			Set::classicExtract( $decisioncomite, 'Dossier.matricule' ),
			Set::classicExtract( $decisioncomite, 'Personne.qual' ).' '.Set::classicExtract( $decisioncomite, 'Personne.nom' ).' '.Set::classicExtract( $decisioncomite, 'Personne.prenom' ) ,
			Set::classicExtract( $decisioncomite, 'Adresse.nomcom' ),
			$this->Locale->date( 'Date::short', Set::classicExtract( $decisioncomite, 'Apre.datedemandeapre' ) ),
			Set::enum( Set::classicExtract( $decisioncomite, 'ApreComiteapre.decisioncomite' ), $options['decisioncomite'] ),
			$this->Locale->date( 'Date::short', Set::classicExtract( $decisioncomite, 'Comiteapre.datecomite' ) ),
			Set::classicExtract( $decisioncomite, 'ApreComiteapre.montantattribue' ),
			Set::classicExtract( $decisioncomite, 'ApreComiteapre.observationcomite' ),
		);
		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'decisionscomitesapres-'.date( 'Ymd-His' ).'.csv' );
?>