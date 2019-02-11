<?php
	require_once  dirname( __FILE__ ).DS.'search.ctp' ;

	if( isset( $results ) ) {
		$totaux = $results['totaux'];
		$results = $results['results'];

		$rows = $this->Xhtml->tableCells(
			array(
				array(
					__d( $domain, 'Tableau1b5.distinct_personnes_prescription' ),
					array( $this->Locale->number( (int)Hash::get( $totaux, 'Tableau1b5.distinct_personnes_prescription' ) ), array( 'class' => 'number integer' ) ),
				),
				array(
					__d( $domain, 'Tableau1b5.distinct_personnes_action' ),
					array( $this->Locale->number( (int)Hash::get( $totaux, 'Tableau1b5.distinct_personnes_action' ) ), array( 'class' => 'number integer' ) ),
				)
			)
		);
		echo $this->Xhtml->tag( 'table', $this->Xhtml->tag( 'tbody', $rows ) );

		// FIXME: depuis le contrôleur/le modèle
		$columns = array(
			'prescription_count',
			'prescriptions_effectives_count',
			'prescriptions_refus_beneficiaire_count',
			'prescriptions_refus_organisme_count',
			'prescriptions_en_attente_count',
			'prescriptions_retenu_count',
			'prescriptions_abandon_count',
		);

		$columns_grouped = array(
			'prescriptions_refus_beneficiaire_count',
			'prescriptions_refus_organisme_count',
			'prescriptions_en_attente_count',
		);

		if( !empty( $results ) ) {
			// thead
			$rows1 = array( array( __d( $domain, 'Tableau1b5.name' ) => array( 'rowspan' => 2 ) ) );
			$rows2 = array();
			$group_count = 0;
			foreach( $columns as $column ) {
				if( !in_array( $column, $columns_grouped ) ) {
					if( $group_count > 0 ) {
						$rows1[] = array( __d( $domain, "Tableau1b5.raisons" ) => array( 'colspan' => $group_count ) );
						$group_count = 0;
						$rows1[] = array( __d( $domain, "Tableau1b5.{$column}" ) => array( 'rowspan' => 2 ) );
					}
					else {
						$rows1[] = array( __d( $domain, "Tableau1b5.{$column}" ) => array( 'rowspan' => 2 ) );
					}
				}
				else {
					$group_count++;
					$rows2[] = __d( $domain, "Tableau1b5.{$column}" );
				}
			}
			$thead = $this->Xhtml->tag( 'thead', $this->Xhtml->tableHeaders( $rows1 ).$this->Xhtml->tableHeaders( $rows2 ) );

			// tbody
			$total = array();
			$rows = array();
			foreach( $results as $result ) {
				$row = array( h( Hash::get( $result, 'Tableau1b5.name' ) ) );
				foreach( $columns as $column ) {
					if( in_array( $column, array( 'prescriptions_refus_beneficiaire_count', 'prescriptions_abandon_count' ) ) ) {
						$value = 'N/C';
					}
					else {
						$value = (int)Hash::get( $result, "Tableau1b5.{$column}" );
						$total[$column] = (int)Hash::get( $total, $column ) + $value;
						$this->Locale->number( $value );
					}
					$row[] = array( $value, array( 'class' => 'number integer' ) );
				}
				$rows[] = $row;
			}
			$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $rows ) );

			// thead
			$rows = array( __d( $domain, 'Total' ) );
			foreach( $columns as $column ) {
				if( in_array( $column, array( 'prescriptions_refus_beneficiaire_count', 'prescriptions_abandon_count' ) ) ) {
					$value = 'N/C';
				}
				else {
					$value = $this->Locale->number( (int)Hash::get( $total, $column ) );
				}
				$rows[] = array( $value, array( 'class' => 'number integer' ) );
			}
			$tfoot = $this->Xhtml->tag( 'tfoot', $this->Xhtml->tableCells( $rows ) );

			// table
			echo $this->Xhtml->tag( 'table', $thead.$tfoot.$tbody );
			echo $this->Xhtml->tag( 'p', '* Autres : Préciser la nature dans le bilan qualitatif, dans le cas contraire elles ne seront pas comptabilisées dans le décompte de l\'objectif de résultat.' );

			$rows = $this->Xhtml->tableCells(
				array(
					array(
						__d( $domain, 'Tableau1b5.beneficiaires_pas_deplaces' ),
						array( $this->Locale->number( (int)Hash::get( $totaux, 'Tableau1b5.beneficiaires_pas_deplaces' ) ), array( 'class' => 'number integer' ) ),
					),
					array(
						__d( $domain, 'Tableau1b5.nombre_fiches_attente' ),
						array( $this->Locale->number( (int)Hash::get( $totaux, 'Tableau1b5.nombre_fiches_attente' ) ), array( 'class' => 'number integer' ) ),
					)
				)
			);
			echo $this->Xhtml->tag( 'table', $this->Xhtml->tag( 'caption', 'Motifs pour lesquels le positionnement n\'est pas effectif' ).$this->Xhtml->tag( 'tbody', $rows ) );

			include_once  dirname( __FILE__ ).DS.'footer.ctp' ;
		}
	}
?>