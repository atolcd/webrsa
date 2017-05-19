<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow(
		array(
			null,
			null,
			'Nombre de participants prévisionnel',
			'Report des participants de l\'année précédente, le cas échéant',
			null,
			null,
			'Entrées enregistrées, au titre de la période d\'exécution considérée',
			null,
			null,
			'Sorties enregistrées, au titre de la période d\'exécution considérée',
			null,
			null,
			sprintf( "Nombre de participants à l'action au 31/12/%d", $tableausuivipdv93['Tableausuivipdv93']['annee'] ),
			null,
			null,
		)
	);

	$this->Csv->addRow(
		array(
			null,
			null,
			'Total',
			'Total',
			'Hommes',
			'Femmes',
			'Total',
			'Hommes',
			'Femmes',
			'Total',
			'Hommes',
			'Femmes',
			'Total',
			'Hommes',
			'Femmes',
		)
	);

	foreach( $results as $categorie1 => $data1 ) {
		// Ligne de premier niveau
		$row = array(
			__d( 'tableauxsuivispdvs93', "/Tableauxsuivispdvs93/tableaud1/{$categorie1}" ),
			null
		);

		foreach( $columns as $column ) {
			$number = $data1[$column];
			if( is_null( $number ) ) {
				$row[] = 'N/C';
			}
			else {
				$row[] = str_replace( '&nbsp;', '', $this->Locale->number( $number ) );
			}
		}
		$this->Csv->addRow( $row );

		// Lignes de second niveau ?
		if( isset( $data1['dont'] ) ) {
			$i = 0;
			foreach( $data1['dont'] as $categorie2 => $data2 ) {
				$row = array();

				if( $i === 0 ) {
					$row[] = 'dont';
				}
				else {
					$row[] = null;
				}

				$row[] = $categories[$categorie1][$categorie2];

				foreach( $columns as $column ) {
					$number = $data2[$column];
					if( is_null( $number ) ) {
						$row[] = 'N/C';
					}
					else {
						$row[] = str_replace( '&nbsp;', '', $this->Locale->number( $number ) );
					}
				}
				$i++;

				$this->Csv->addRow( $row );
			}
		}
	}

	Configure::write( 'debug', 0 );
	echo $this->Tableaud2->Csv->render( $csvfile );
?>