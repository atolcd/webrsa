<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	// Ligne d'en-tête
	$this->Csv->addRow(
		array(
			'Difficultés exprimées par les bénéficiaires',
			'En nombre',
			'En taux'
		)
	);

	// Corps du tableau
	$total = (int)Hash::get( $results, 'total' );
	foreach( $categories as $categorie => $label ) {
		$result = (int)Hash::get( $results, $categorie );
		$this->Csv->addRow(
			array(
				$label,
				$result,
				number_format( $result / $total * 100, 2 ),
			)
		);
	}
	// Ligne de total
	$this->Csv->addRow(
		array(
			'Total',
			$total,
			number_format( $total / $total * 100, 2 ),
		)
	);

	Configure::write( 'debug', 0 );
	echo $this->Tableaud2->Csv->render( $csvfile );
?>