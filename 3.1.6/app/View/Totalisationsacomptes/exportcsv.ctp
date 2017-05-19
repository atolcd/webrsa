<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	foreach( $totsacoms as $totacom ) {
		$this->Csv->addRow( array( $type_totalisation[$totacom['Totalisationacompte']['type_totalisation']] ) );
		$this->Csv->addRow( array( 'RSA socle', $totacom['Totalisationacompte']['mttotsoclrsa'] ) );
		$this->Csv->addRow( array( 'RSA socle majoré', $totacom['Totalisationacompte']['mttotsoclmajorsa'] ) );
		$this->Csv->addRow( array( 'RSA local', $totacom['Totalisationacompte']['mttotlocalrsa'] ) );
		$this->Csv->addRow( array( 'RSA socle total', $totacom['Totalisationacompte']['mttotrsa'] ) );
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'totalisationsacomptes-'.date( 'Ymd-His' ).'.csv' );
?>