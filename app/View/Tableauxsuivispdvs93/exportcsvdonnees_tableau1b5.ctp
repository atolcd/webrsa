<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	// Petit tableau du haut
	$this->Csv->addRow(
		array(
			__d( 'tableauxsuivispdvs93', 'Tableau1b5.distinct_personnes_prescription' ),
			(int)Hash::get( $results, 'totaux.0.0.distinct_personnes_prescription' )
		)
	);
	$this->Csv->addRow(
		array(
			__d( 'tableauxsuivispdvs93', 'Tableau1b5.distinct_personnes_action' ),
			(int)Hash::get( $results, 'totaux.0.0.distinct_personnes_action' )
		)
	);

	// Séparateur
	$this->Csv->addRow( array() );

	// Tableau principal, lignes d'en-têtes
	$this->Csv->addRow(
		array(
			__d( 'tableauxsuivispdvs93', 'Tableau1b5.thematique' ),
			__d( 'tableauxsuivispdvs93', 'Tableau1b5.categorie' ),
			__d( 'tableauxsuivispdvs93', 'Tableau1b5.nombre' ),
			__d( 'tableauxsuivispdvs93', 'Tableau1b5.nombre_effectives' ),
			__d( 'tableauxsuivispdvs93', 'Tableau1b5.raison_non_participation' ),
			null,
			null,
			__d( 'tableauxsuivispdvs93', 'Tableau1b5.nombre_participations' )
		)
	);

	// Tableau principal
	$rowspans = array();
	foreach( $results['results'] as $result ) {
		if( !isset( $rowspans[$result[0]['categorie']] ) ) {
			$rowspans[$result[0]['categorie']] = 0;
		}
		$rowspans[$result[0]['categorie']]++;
	}

	$total = array();
	$cells = array();
	$categoriepcd = null;
	foreach( $results['results'] as $result ) {
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
			$row[] = (int)Hash::get( $result, "0.nombre_effectives" );
			$row[] = (int)Hash::get( $result, "0.nombre_refus_organisme" );
			$row[] = (int)Hash::get( $result, "0.nombre_en_attente" );
			$row[] = (int)Hash::get( $result, "0.nombre_participations" );

			$this->Csv->addRow( $row );

			$categoriepcd = $result[0]['categorie'];
		}
		else {
			$total = $result;
		}
	}

	// Tableau principal, ligne de total
	$this->Csv->addRow(
		array(
			'Total',
			null,
			(int)Hash::get( $total, "0.nombre" ),
			(int)Hash::get( $total, "0.nombre_effectives" ),
			(int)Hash::get( $total, "0.nombre_refus_organisme" ),
			(int)Hash::get( $total, "0.nombre_en_attente" ),
			(int)Hash::get( $total, "0.nombre_participations" )
		)
	);


	// Séparateur
	$this->Csv->addRow( array() );

	// Petit tableau du bas
	$this->Csv->addRow(
		array(
			__d( 'tableauxsuivispdvs93', 'Tableau1b5.beneficiaires_pas_deplaces' ),
			(int)Hash::get( $results, 'totaux.0.0.beneficiaires_pas_deplaces' )
		)
	);
	$this->Csv->addRow(
		array(
			__d( 'tableauxsuivispdvs93', 'Tableau1b5.nombre_fiches_attente' ),
			(int)Hash::get( $results, 'totaux.0.0.nombre_fiches_attente' )
		)
	);

	Configure::write( 'debug', 0 );
	echo $this->Tableaud2->Csv->render( $csvfile );
?>