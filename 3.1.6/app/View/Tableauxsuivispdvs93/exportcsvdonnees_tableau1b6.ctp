<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$this->Csv->addRow(
		array(
			__d( 'tableauxsuivispdvs93', 'Tableau1b6.name' ),
			__d( 'tableauxsuivispdvs93', 'Tableau1b6.count_personnes_prevues' ),
			__d( 'tableauxsuivispdvs93', 'Tableau1b6.count_invitations' ),
			__d( 'tableauxsuivispdvs93', 'Tableau1b6.count_seances' ),
			__d( 'tableauxsuivispdvs93', 'Tableau1b6.count_personnes' ),
			__d( 'tableauxsuivispdvs93', 'Tableau1b6.count_participations' )
		)
	);

	// Lignes du tableau
	foreach( $results as $result ) {
		$this->Csv->addRow(
			array(
				Hash::get( $result, 'Tableau1b6.name' ),
				(int)Hash::get( $result, "Tableau1b6.count_personnes_prevues" ),
				(int)Hash::get( $result, "Tableau1b6.count_invitations" ),
				(int)Hash::get( $result, "Tableau1b6.count_seances" ),
				(int)Hash::get( $result, "Tableau1b6.count_personnes" ),
				(int)Hash::get( $result, "Tableau1b6.count_participations" ),
			)
		);
	}

	// Ligne de totaux
	$this->Csv->addRow(
		array(
			'Total',
			(int)array_sum( (array)Hash::extract( $results, '{n}.Tableau1b6.count_personnes_prevues' ) ),
			(int)array_sum( (array)Hash::extract( $results, '{n}.Tableau1b6.count_invitations' ) ),
			(int)array_sum( (array)Hash::extract( $results, '{n}.Tableau1b6.count_seances' ) ),
			(int)array_sum( (array)Hash::extract( $results, '{n}.Tableau1b6.count_personnes' ) ),
			(int)array_sum( (array)Hash::extract( $results, '{n}.Tableau1b6.count_participations' ) )
		)
	);

	Configure::write( 'debug', 0 );
	echo $this->Tableaud2->Csv->render( $csvfile );
?>