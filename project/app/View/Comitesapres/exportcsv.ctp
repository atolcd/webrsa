<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow( array( 'Intitulé du comité', 'Lieu du comité', 'Date du comité',  'Heure du comité', 'Observations du comité' ) );

	foreach( $comitesapres as $comiteapre ) {

		$row = array(
			Set::classicExtract( $comiteapre, 'Comiteapre.intitulecomite' ),
			Set::classicExtract( $comiteapre, 'Comiteapre.lieucomite' ),
			$this->Locale->date( 'Date::short', Set::classicExtract( $comiteapre, 'Comiteapre.datecomite' ) ),
			$this->Locale->date( 'Time::short', Set::classicExtract( $comiteapre, 'Comiteapre.heurecomite' ) ),
			Set::classicExtract( $comiteapre, 'Comiteapre.observationcomite' ),
		);
		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'comitesapres-'.date( 'Ymd-His' ).'.csv' );
?>