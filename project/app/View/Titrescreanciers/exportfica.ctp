<?php

	$this->Csv->preserveLeadingZerosInExcel = true;
	$this->Csv->delimiter = ';';
	$this->Csv->filename = $csvfile;

	foreach( $infosFICA as $key => $infoFICA ) {
		if($key == 0) {
			$this->Csv->addRow(
				$infoFICA
			);
		}else{
			$row = $infoFICA;
			/*
			 * Traitements des valeurs a ajouter en fonction des retours sur la qualité du fichier.
			 */
			/*
			 foreach( $infoFICA as $column ) {
				$number = $column;
				if( is_null( $number ) ) {
					$row[] = 'N/C';
				}
				else {
					$row[] = str_replace( '&nbsp;', '', $this->Locale->number( $number ) );
				}
			}*/
			$this->Csv->addRow( $row );
		}
	}

	$this->layout = null;
	Configure::write( 'debug', 0 );

	echo $this->Csv->render($csvfile);
