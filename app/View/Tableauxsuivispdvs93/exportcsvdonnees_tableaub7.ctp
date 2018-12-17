<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	// Ligne d'en-tête
	$this->Csv->addRow(
		array(
			'Nombre de personnes différentes ayant accédé à un emploi :'
		)
	);

	foreach($results as $index=>$value) {
		$this->Csv->addRow(
			array(
				$index,
				$value
			)
		);
	}

	Configure::write( 'debug', 0 );
	echo $this->Tableaud2->Csv->render( $csvfile );
?>