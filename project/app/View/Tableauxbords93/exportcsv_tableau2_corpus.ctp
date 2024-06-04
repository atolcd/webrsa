<?php
	$this->Csv->preserveLeadingZerosInExcel = true;
 
	foreach( $export as $line ) {
		$this->Csv->addRow( $line );
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'tableau_de_bord_rsa_tableau2_corpus-'.date( 'Ymd-His' ).'.csv' );
?>