<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	// 0.2 On recherche le type de chacun des champs
	$fields = Hash::normalize( (array)Configure::read( "Tableauxsuivispdvs93.{$action}.exportcsvcorpus" ) );
	foreach( $fields as $fieldName => $params ) {
		$params = (array)$params;
		if( !isset( $params['type'] ) ) {
			$fields[$fieldName]['type'] = $this->Default3->DefaultTable->DefaultTableCell->DefaultData->type( $fieldName );
		}
	}

	// 1. Ligne d'en-têtes
	$row = array();
	foreach( $fields as $fieldName => $params ) {
		$row[] = ( isset( $params['label'] ) ? $params['label'] : __d( $this->request->params['controller'], $fieldName ) );
	}
	$this->Csv->addRow( $row );

	// 2. Lignes de résultats
	foreach( $results as $result ) {
		// Extraction d'une ligne de données
		$row = array();
		foreach( $fields as $fieldName => $params ) {
			$value = Hash::get( $result, $fieldName );

			if( $params['type'] === 'date' ) {
				$value = date_short( $value );
			}
			else if( Hash::check( $options, $fieldName ) ) {
				$value = value( Hash::get( $options, $fieldName ), $value );
			}

			$row[] = $value;
		}

		$this->Csv->addRow( $row );
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( $csvfile );
?>