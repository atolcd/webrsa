<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	// Ligne d'en-tête
	$this->Csv->addRow(
		array(
			'',
			'Maintiens dans l’accompagnement',
			'',
			'',
			'',
			'',
			'',
			'Sortie de l’accompagnement'
		)
	);
	$this->Csv->addRow(
		array(
			'',
			'Tps complet',
			'',
			'Tps partiel',
			'',
			'Non communiqué',
			'Tps complet',
			'',
			'Tps partiel',
			'',
			'Non communiqué'
		)
	);
	$this->Csv->addRow(
		array(
			'',
			'Nb d\'accès',
			'%',
			'Nb d\'accès',
			'%',
			'Nb d\'accès',
			'%',
			'Nb d\'accès',
			'%',
			'Nb d\'accès',
			'%',
			'Nb d\'accès',
			'%'
		)
	);
	
	foreach( $results['typeemploi'] as $numero => $typeemploi ) {
		$numero = $typeemploi['Typeemploi']['codetypeemploi'];
		
		$this->Csv->addRow(
			array(
				$typeemploi['Typeemploi']['name'],
				$results['tableauB7']['complet'][$numero],
				($results['total']['B7']['complet'] == 0) ? 0 : round (100 * $results['tableauB7']['complet'][$numero] / $results['total']['B7']['complet'], 2)."%",
				$results['tableauB7']['partiel'][$numero],
				($results['total']['B7']['partiel'] == 0) ? 0 : round (100 * $results['tableauB7']['partiel'][$numero] / $results['total']['B7']['partiel'], 2)."%",
				$results['tableauB7']['non_com'][$numero],
				($results['total']['B7']['non_com'] == 0) ? 0 : round (100 * $results['tableauB7']['non_com'][$numero] / $results['total']['B7']['non_com'], 2)."%",
				@$results['tableauD2']['complet'][$numero],
				(@$results['total']['D2']['complet'] == 0) ? 0 : round (100 * @$results['tableauD2']['complet'][$numero] / @$results['total']['D2']['complet'], 2)."%",
				@$results['tableauD2']['partiel'][$numero],
				(@$results['total']['D2']['partiel'] == 0) ? 0 : round (100 * @$results['tableauD2']['partiel'][$numero] / @$results['total']['D2']['partiel'], 2)."%",
				@$results['tableauD2']['non_com'][$numero],
				(@$results['total']['D2']['non_com'] == 0) ? 0 : round (100 * @$results['tableauD2']['non_com'][$numero] / @$results['total']['D2']['non_com'], 2)."%"
			)
		);
	}

	$this->Csv->addRow(
		array(
			'TOTAL',
			$results['total']['B7']['complet'],
			($results['total']['B7']['complet'] == 0) ? 0 : "100%",
			$results['total']['B7']['partiel'],
			($results['total']['B7']['partiel'] == 0) ? 0 : "100%",
			$results['total']['B7']['non_com'],
			($results['total']['B7']['non_com'] == 0) ? 0 : "100%",
			$results['total']['D2']['complet'],
			($results['total']['D2']['complet'] == 0) ? 0 : "100%",
			$results['total']['D2']['partiel'],
			($results['total']['D2']['partiel'] == 0) ? 0 : "100%",
			$results['total']['D2']['non_com'],
			($results['total']['D2']['non_com'] == 0) ? 0 : "100%"
		)
	);

	Configure::write( 'debug', 0 );
	echo $this->Tableaud2->Csv->render( $csvfile );
?>