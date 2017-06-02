<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	// 0. Initialisation
	// 0.1 Construction des champs virtuels des modèles liés en cases à cocher
	$virtualFields = array();
	foreach( $checkboxesVirtualFields as $checkboxVirtualField ) {
		list( $model, $field ) = model_field( $checkboxVirtualField );
		$virtualFields[] = "Donnees.{$field}";
	}

	// 0.2 On recherche le type de chacun des champs
	$fields = Hash::normalize( (array)Configure::read( 'Dsps.exportcsv' ) );
	foreach( $fields as $fieldName => $params ) {
		$params = (array)$params;
		if( !isset( $params['type'] ) ) {
			$fields[$fieldName]['type'] = $this->Default3->DefaultTable->DefaultTableCell->DefaultData->type( $fieldName );
		}
	}

	// 1. Ligne d'en-têtes
	$row = array();
	foreach( $fields as $fieldName => $params ) {
		$row[] = ( isset( $params['label'] ) ? $params['label'] : __d( 'dsps', $fieldName ) );
	}
	$this->Csv->addRow( $row );

	// 2. Lignes de résultats
	foreach( $dsps as $dsp ) {
		// "Traduction" des champs virtuels des cases à cocher pour obtenir des listes
		foreach( $checkboxesVirtualFields as $path ) {
			list( $model, $field ) = model_field( $path );

			if( Hash::check( $dsp, "Donnees.{$field}" ) ) {
				$values = vfListeToArray( Hash::get( $dsp, "Donnees.{$field}" ) );

				if( !empty( $values ) ) {
					foreach( $values as $key => $value ) {
						$values[$key] = value( $options[$model][$field], $value );
					}
					$values = implode( ',', $values );
				}
				else {
					$values = '';
				}

				$dsp = Hash::insert( $dsp, "Donnees.{$field}", $values );
			}
		}

		// Traduction des natures de prestation
		$natpf = vfListeToArray( Hash::get( $dsp, 'Detaildroitrsa.natpf' ) );
		if( !empty( $natpf ) ) {
			foreach( $natpf as $index => $value ) {
				$natpf[$index] = value( $options['Detailcalculdroitrsa']['natpf'], $value );
			}
			$dsp = Hash::insert( $dsp, 'Detaildroitrsa.natpf', implode( ', ', $natpf ) );
		}

		// Extraction d'une ligne de données
		$row = array();
		foreach( $fields as $fieldName => $params ) {
			$value = Hash::get( $dsp, $fieldName );

			if( $params['type'] === 'date' ) {
				$value = date_short( $value );
			}
			else if( Hash::check( $options, $fieldName ) ) {
				$value = value( Hash::get( $options, $fieldName ), $value );
			}
			else if( !in_array( $fieldName, $virtualFields ) ) {
				$value = $value;
			}

			$row[] = $value;
		}

		$this->Csv->addRow( $row );
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'dsps-'.date( 'Ymd-His' ).'.csv' );
?>