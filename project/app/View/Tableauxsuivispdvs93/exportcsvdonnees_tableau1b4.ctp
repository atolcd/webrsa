<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	// Ligne d'en-tête
	$this->Csv->addRow(
		array(
			__d( 'tableauxsuivispdvs93', 'Tableau1b4.thematique' ),
			__d( 'tableauxsuivispdvs93', 'Tableau1b4.categorie' ),
			__d( 'tableauxsuivispdvs93', 'Tableau1b4.nombre' ),
			__d( 'tableauxsuivispdvs93', 'Tableau1b4.nombre_unique' )
		)
	);

	// Corps du tableau
	$rowspans = array();
	foreach( $results as $result ) {
		if( !isset( $rowspans[$result[0]['categorie']] ) ) {
			$rowspans[$result[0]['categorie']] = 0;
		}
		$rowspans[$result[0]['categorie']]++;
	}

	$total = array();
	$cells = array();
	$categoriepcd = null;
	foreach( $results as $result ) {
		if( $result[0]['categorie'] !== 'Total' ) {
			$row = array();

			if( $categoriepcd !== $result[0]['categorie'] ) {
				$row[] = $result[0]['categorie'];
			}
			else {
				$row[] = null;
			}

			$row[] = $result[0]['thematique'];
			$row[] = (int)Hash::get( $result, "0.nombre" );
			$row[] = (int)Hash::get( $result, "0.nombre_unique" );

			$this->Csv->addRow( $row );

			$categoriepcd = $result[0]['categorie'];
		}
		else {
			$total = $result;
		}
	}

	// Ligne de totaux
	$this->Csv->addRow(
		array(
			'Total',
			null,
			(int)Hash::get( $total, "0.nombre" ),
			(int)Hash::get( $total, "0.nombre_unique" )
		)
	);

	Configure::write( 'debug', 0 );
	echo $this->Tableaud2->Csv->render( $csvfile );
?>