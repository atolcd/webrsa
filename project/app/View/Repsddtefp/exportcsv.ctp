<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow( array( 'Nom/Prénom allocataire',  'Sexe', 'Age', 'Adresse', 'Montant aides'/*, 'Types d\'aide'*/, 'Activité du bénéficiaire', 'Secteur d \'activité', 'Statut de l\'apre' ) );

	foreach( $apres as $apre ) {
		///Calcul de l'age des bénéficiaires
		if( !empty( $apre ) ){
			$dtnai = Set::classicExtract( $apre, 'Personne.dtnai' );
			$today = ( date( 'Y' ) );
			if( !empty( $dtnai ) ){
				$age = ($today - $dtnai);
			}
		}

		$statutApre = Set::classicExtract( $apre, 'Apre.statutapre' );

		$row = array(
			Set::classicExtract( $apre, 'Personne.nom' ).' '.Set::classicExtract( $apre, 'Personne.prenom'),
			Set::enum( Set::classicExtract( $apre, 'Personne.sexe' ), $sexe ),
			$age,
			Set::classicExtract( $apre, 'Adresse.nomcom' ),
			$this->Locale->money( Set::classicExtract( $apre, 'Apre.mtforfait' ) + Set::classicExtract( $apre, 'Apre.montantaides' ) ),

			Set::enum( Set::classicExtract( $apre, 'Apre.activitebeneficiaire' ), $options['activitebeneficiaire'] ),
			Set::enum( Set::classicExtract( $apre, 'Apre.secteuractivite' ), $sect_acti_emp ),
			Set::enum( $statutApre , $options['statutapre'] )
		);
		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'apres-'.date( 'Ymd-His' ).'.csv' );
?>