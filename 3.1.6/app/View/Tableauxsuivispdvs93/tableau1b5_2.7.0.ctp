<?php
	require_once( dirname( __FILE__ ).DS.'search.ctp' );

	if( isset( $results ) ) {
		$totaux = $results['totaux'];
		$results = $results['results'];

		$rows = $this->Xhtml->tableCells(
			array(
				array(
					__d( $domain, 'Tableau1b5.distinct_personnes_prescription' ),
					array( $this->Locale->number( (int)Hash::get( $totaux, '0.0.distinct_personnes_prescription' ) ), array( 'class' => 'number integer' ) ),
				),
				array(
					__d( $domain, 'Tableau1b5.distinct_personnes_action' ),
					array( $this->Locale->number( (int)Hash::get( $totaux, '0.0.distinct_personnes_action' ) ), array( 'class' => 'number integer' ) ),
				)
			)
		);
		echo $this->Xhtml->tag( 'table', $this->Xhtml->tag( 'tbody', $rows ) );

		// En-tête du tableau
		$thead = $this->Xhtml->tag(
			'thead',
			$this->Xhtml->tableHeaders(
				array(
					array( __d( $domain, 'Tableau1b5.thematique' ) => array( 'rowspan' => 2 ) ),
					array( __d( $domain, 'Tableau1b5.categorie' ) => array( 'rowspan' => 2 ) ),
					array( __d( $domain, 'Tableau1b5.nombre' ) => array( 'rowspan' => 2 ) ),
					array( __d( $domain, 'Tableau1b5.nombre_effectives' ) => array( 'rowspan' => 2 ) ),
					array( __d( $domain, 'Tableau1b5.raison_non_participation' ) => array( 'colspan' => 3 ) ),
					array( __d( $domain, 'Tableau1b5.nombre_participants' ) => array( 'rowspan' => 2 ) ),
				)
			)
			.$this->Xhtml->tableHeaders(
				array(
					__d( $domain, 'Tableau1b5.nombre_refus_beneficiaire' ),
					__d( $domain, 'Tableau1b5.nombre_refus_organisme' ),
					__d( $domain, 'Tableau1b5.nombre_en_attente' ),
				)
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
				// TODO: sous-totaux ?
				$cell = array();

				if( $result[0]['thematique'] === 'Sous-total' ) {
					$class = 'subtotal';
				}
				else {
					$class = null;
				}

				if( $categoriepcd !== $result[0]['categorie'] ) {
					$cell[] = array( $result[0]['categorie'], array( 'rowspan' => $rowspans[$result[0]['categorie']], 'class' => $class ) );
				}

				$cell[] = array( $result[0]['thematique'], array( 'class' => $class ) );
				$cell[] = array( $this->Locale->number( (int)Hash::get( $result, "0.nombre" ) ), array( 'class' => "integer number {$class}" ) );
				$cell[] = array( $this->Locale->number( (int)Hash::get( $result, "0.nombre_effectives" ) ), array( 'class' => "integer number {$class}" ) );
				$cell[] = array( $this->Locale->number( (int)Hash::get( $result, "0.nombre_refus_beneficiaire" ) ), array( 'class' => "integer number {$class}" ) );
				$cell[] = array( $this->Locale->number( (int)Hash::get( $result, "0.nombre_refus_organisme" ) ), array( 'class' => "integer number {$class}" ) );
				$cell[] = array( $this->Locale->number( (int)Hash::get( $result, "0.nombre_en_attente" ) ), array( 'class' => "integer number {$class}" ) );
				$cell[] = array( $this->Locale->number( (int)Hash::get( $result, "0.nombre_participants" ) ), array( 'class' => "integer number {$class}" ) );

				$cells[] = $cell;

				$categoriepcd = $result[0]['categorie'];
			}
			else {
				$total = $result;
			}
		}
		$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $cells ) );

		// Pied du tableau
		$cells = array(
			array(
				array( 'Total', array( 'colspan' => 2 ) ),
				array( $this->Locale->number( (int)Hash::get( $total, "0.nombre" ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( (int)Hash::get( $total, "0.nombre_effectives" ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( (int)Hash::get( $total, "0.nombre_refus_beneficiaire" ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( (int)Hash::get( $total, "0.nombre_refus_organisme" ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( (int)Hash::get( $total, "0.nombre_en_attente" ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( (int)Hash::get( $total, "0.nombre_participants" ) ), array( 'class' => 'integer number' ) ),
			)
		);
		$tfoot = $this->Xhtml->tag( 'tfoot', $this->Xhtml->tableCells( $cells ) );

		echo $this->Xhtml->tag( 'table', $thead.$tfoot.$tbody ,array( 'class' => 'wide' ) );

		// Tableau du dessous
		$rows = $this->Xhtml->tableCells(
			array(
				array(
					__d( $domain, 'Tableau1b5.beneficiaires_pas_deplaces' ),
					array( $this->Locale->number( (int)Hash::get( $totaux, '0.0.beneficiaires_pas_deplaces' ) ), array( 'class' => 'number integer' ) ),
				),
				array(
					__d( $domain, 'Tableau1b5.nombre_fiches_attente' ),
					array( $this->Locale->number( (int)Hash::get( $totaux, '0.0.nombre_fiches_attente' ) ), array( 'class' => 'number integer' ) ),
				)
			)
		);
		echo $this->Xhtml->tag( 'table', $this->Xhtml->tag( 'caption', 'Motifs pour lesquels la prescription n\'est pas effective' ).$this->Xhtml->tag( 'tbody', $rows ) );

		require_once( dirname( __FILE__ ).DS.'footer.ctp' );
	}
?>