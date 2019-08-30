<?php
    $this->Csv->preserveLeadingZerosInExcel = true;

    $headers = array( null );
    for( $i = 1 ; $i <= 12 ; $i++ ) {
        $headers[] = ucfirst( $this->Locale->date( '%b %Y', $annee.( ( $i < 10 ) ? '0'.$i : $i ).'01' ) );
    }
    $headers[] = 'Total / Moyenne '.$annee;
    $this->Csv->addRow( $headers );

    $indicateurs = $results;

    foreach( $indicateurs as $key => $indicateur ) {
        $rows = array();
        $row = __d( 'indicateurmensuel', 'Indicateurmensuel.'.$key );
        $row = str_replace('<b>', '', $row);
        $row = str_replace('</b>', '', $row);
        if( strpos($row, '<br />') !== false) {
            $row = str_replace('<br />', ' ', $row);
        }
        $rows[] = $row;
        for( $i = 1 ; $i <= 12 ; $i++ ) {
            $value = ( ( isset( $indicateur[$i] ) ? $indicateur[$i] : 0 ) );
            if($value != 0) {
                $value = number_format($value, 2);
            }
            $rows[] = $value;
        }
        $this->Csv->addRow( $rows );
    }

	Configure::write( 'debug', 0 );
	echo $this->Tableaud2->Csv->render( $csvfile );
?>