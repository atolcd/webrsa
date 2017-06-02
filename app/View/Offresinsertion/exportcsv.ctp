<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow(
		array(
            'Intitulé de l\'action',
            'Code de l\'action',
            'Chargé d\'insertion',
            'Nom du correspondant',
            'Secrétaire',
            'Ville',
            'Canton',
            'Début de l\'action',
            'Fin de l\'action',
            'Nombre de postes disponibles',
            'Nombre d\'heures disponibles',
            'Nom du contact',
            'N° de téléphone du contact',
            'N° de fax',
            'Email du contact',
            'Libellé du partenaire',
            'Code du partenaire',
            'Adresse du partenaire',
            'N° de téléphone du partenaire'

		)
	);

	foreach( $actionscandidat as $actioncandidat ) {
		$row = array(
			Set::classicExtract( $actioncandidat, 'Actioncandidat.name' ),
            Set::classicExtract( $actioncandidat, 'Actioncandidat.codeaction' ),
            Set::classicExtract( $actioncandidat, 'Chargeinsertion.nom_complet' ),
            Set::classicExtract( $actioncandidat, 'Secretaire.nom_complet' ),
            Set::enum( Set::classicExtract( $actioncandidat, 'Actioncandidat.referent_id' ), $correspondants ),
            Set::classicExtract( $actioncandidat, 'Actioncandidat.lieuaction' ),
            Set::classicExtract( $actioncandidat, 'Actioncandidat.cantonaction' ),
            date_short( Set::classicExtract( $actioncandidat, 'Actioncandidat.ddaction' ) ),
            date_short( Set::classicExtract( $actioncandidat, 'Actioncandidat.dfaction' ) ),
            Set::classicExtract( $actioncandidat, 'Actioncandidat.nbpostedispo' ),
            Set::classicExtract( $actioncandidat, 'Actioncandidat.nbheuredispo' ),
            Set::classicExtract( $actioncandidat, 'Contactpartenaire.nom_candidat' ),
            Set::classicExtract( $actioncandidat, 'Contactpartenaire.numtel' ),
            Set::classicExtract( $actioncandidat, 'Contactpartenaire.numfax' ),
            Set::classicExtract( $actioncandidat, 'Contactpartenaire.email' ),
            Set::classicExtract( $actioncandidat, 'Partenaire.libstruc' ),
            Set::classicExtract( $actioncandidat, 'Partenaire.codepartenaire' ),
            Set::classicExtract( $actioncandidat, 'Partenaire.adresse' ),
            Set::classicExtract( $actioncandidat, 'Partenaire.numtel' )
		);
		$this->Csv->addRow($row);
	}
	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'liste_actions-'.date( 'Ymd-His' ).'.csv', Configure::read( 'App.encoding' ) );
?>