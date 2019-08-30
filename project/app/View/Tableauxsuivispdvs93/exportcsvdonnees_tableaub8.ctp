<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	// Ligne d'en-tête
	$this->Csv->addRow(
		array(
			__d('tableauxsuivispdvs93', 'Tableaub8.CSVTitle')
		)
	);

	$this->Csv->addRow(
        array(
            __d( 'tableauxsuivispdvs93', 'Tableaub8.nbCER' ),
            __d( 'tableauxsuivispdvs93', 'Tableaub8.jan' ),
            __d( 'tableauxsuivispdvs93', 'Tableaub8.feb' ),
            __d( 'tableauxsuivispdvs93', 'Tableaub8.mar' ),
            __d( 'tableauxsuivispdvs93', 'Tableaub8.apr' ),
            __d( 'tableauxsuivispdvs93', 'Tableaub8.may' ),
            __d( 'tableauxsuivispdvs93', 'Tableaub8.jun' ),
            __d( 'tableauxsuivispdvs93', 'Tableaub8.jul' ),
            __d( 'tableauxsuivispdvs93', 'Tableaub8.aug' ),
            __d( 'tableauxsuivispdvs93', 'Tableaub8.sep' ),
            __d( 'tableauxsuivispdvs93', 'Tableaub8.oct' ),
            __d( 'tableauxsuivispdvs93', 'Tableaub8.nov' ),
            __d( 'tableauxsuivispdvs93', 'Tableaub8.dec' )
        )
    );
    foreach( $results as $structure => $result) {
        $this->Csv->addRow(array(
            $structure,
            $result[1],
            $result[2],
            $result[3],
            $result[4],
            $result[5],
            $result[6],
            $result[7],
            $result[8],
            $result[9],
            $result[10],
            $result[11],
            $result[12],
        ) );
    }

	Configure::write( 'debug', 0 );
	echo $this->Tableaud2->Csv->render( $csvfile );
?>