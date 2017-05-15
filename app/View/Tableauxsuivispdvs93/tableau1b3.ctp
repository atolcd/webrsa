<?php
	require_once( dirname( __FILE__ ).DS.'search.ctp' );

	if( isset( $results ) ) {
		$cells = array();
		$total = (int)Hash::get( $results, 'total' );

		// Corps du tableau
		foreach( $options['problematiques'] as $problematique => $label ) {
			$nombre = (int)Hash::get( $results, $problematique );
			$taux = ( !empty( $total ) ? $nombre / $total * 100 : 0 );

			$cells[] = array(
				$label,
				array( $this->Locale->number( $nombre ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( $taux, 2 ).' %', array( 'class' => 'integer number' ) ),
			);
		}
		$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $cells ) );

		// En-tête du tableau
		$cells = array(
			'Difficultés exprimées par les bénéficiaires',
			'En nombre',
			'En taux'
		);
		$thead = $this->Xhtml->tag( 'thead', $this->Xhtml->tableHeaders( $cells ) );

		// Pied du tableau
		$cells = array(
			array(
				'Total',
				array( $this->Locale->number( $total ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( ( !empty( $total ) ? 100 : 0 ), 2 ).' %', array( 'class' => 'integer number' ) )
			)
		);
		$tfoot = $this->Xhtml->tag( 'tfoot', $this->Xhtml->tableCells( $cells ) );


		echo $this->Xhtml->tag( 'table', $thead.$tfoot.$tbody );
		echo $this->Xhtml->tag( 'p', '(1) : à préciser dans le bilan qualitatif' );

		require_once( dirname( __FILE__ ).DS.'footer.ctp' );
	}
?>