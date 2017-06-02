<?php
	require_once( dirname( __FILE__ ).DS.'search.ctp' );

	if( isset( $results ) ) {
		// En-tête du tableau
		$thead = $this->Xhtml->tag(
			'thead',
			$this->Xhtml->tableHeaders(
				array(
					__d( $domain, 'Tableau1b4.thematique' ),
					__d( $domain, 'Tableau1b4.categorie' ),
					__d( $domain, 'Tableau1b4.nombre' ),
					__d( $domain, 'Tableau1b4.nombre_unique' )
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
				$cell[] = array( $this->Locale->number( (int)Hash::get( $result, "0.nombre_unique" ) ), array( 'class' => "integer number {$class}" ) );

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
				array( $this->Locale->number( (int)Hash::get( $total, "0.nombre_unique" ) ), array( 'class' => 'integer number' ) )
			)
		);
		$tfoot = $this->Xhtml->tag( 'tfoot', $this->Xhtml->tableCells( $cells ) );

		echo $this->Xhtml->tag( 'table', $thead.$tfoot.$tbody ,array( 'class' => 'wide' ) );

		require_once( dirname( __FILE__ ).DS.'footer.ctp' );
	}
?>