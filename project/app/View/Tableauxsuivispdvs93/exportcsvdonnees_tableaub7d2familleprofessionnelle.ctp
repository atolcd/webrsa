<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	// Ligne d'en-tête
	$this->Csv->addRow(
		array(
			'',
			'Nombre d\'accès à l\'emploi - maintien dans l\'accompagnement',
			'',
			'Nombre d\'accès à l\'emploi - sortie',
			'',
			'TOTAL'
		)
	);
	$this->Csv->addRow(
		array(
			'',
			'Nb',
			'%',
			'Nb',
			'%',
			'Nb',
			'%'
		)
	);

	foreach( $results['familleRomev3'] as $famille ) {
		$idFamille = $famille['Familleromev3']['id'];

		$this->Csv->addRow(
			array(
				$famille['Familleromev3']['code'].' - '.$famille['Familleromev3']['name'],
				$results['tableauRomev3']['B7'][$idFamille],
				($results['totalFamilleB7'] == 0) ? 0 : round (100 * $results['tableauRomev3']['B7'][$idFamille] / $results['totalFamilleB7'], 2)."%",
				$results['tableauRomev3']['D2'][$idFamille],
				($results['totalFamilleD2'] == 0) ? 0 : round (100 * $results['tableauRomev3']['D2'][$idFamille] / $results['totalFamilleD2'], 2)."%",
				$results['tableauRomev3']['TOTAL'][$idFamille],
				($results['totalFamilleTotal'] == 0) ? 0 : round (100 * $results['tableauRomev3']['TOTAL'][$idFamille] / $results['totalFamilleTotal'], 2)."%"
			)
		);
	}
	$this->Csv->addRow(
		array(
			'TOTAL',
			$results['totalFamilleB7'],
			($results['totalFamilleB7'] == 0) ? 0 : "100%",
			$results['totalFamilleD2'],
			($results['totalFamilleD2'] == 0) ? 0 : "100%",
			$results['totalFamilleTotal'],
			($results['totalFamilleTotal'] == 0) ? 0 : "100%"
		)
	);

	Configure::write( 'debug', 0 );
	echo $this->Tableaud2->Csv->render( $csvfile );
?>