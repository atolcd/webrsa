<?php
	$this->Csv->preserveLeadingZerosInExcel = true;
 
	foreach( $export as $line ) {
		$this->Csv->addRow( $line );
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'TDB 2 - Pilotage conventionnel - Corpus -'.date( 'Ymd-His' ).'.csv' );
?>