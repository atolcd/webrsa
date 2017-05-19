<?php
	require_once( dirname( __FILE__ ).DS.'search.ctp' );

	if( isset( $results ) ) {
		$thead = $this->Xhtml->tag(
			'thead',
			$this->Xhtml->tableHeaders(
				array(
					__d( $domain, 'Tableau1b4.acteur' ),
					__d( $domain, 'Tableau1b4.nombre' ),
					__d( $domain, 'Tableau1b4.nombre_unique' )
				)
			)
		);

		$cells = array();
		foreach( $options['acteurs'] as $acteur => $label ) {
			$cells[] = array(
				$label,
				array( $this->Locale->number( (int)Hash::get( $results, "{$acteur}.nombre" ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( (int)Hash::get( $results, "{$acteur}.nombre_unique" ) ), array( 'class' => 'integer number' ) )
			);
		}
		$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $cells ) );

		// Pied du tableau
		$cells = array(
			array(
				'Total',
				array( $this->Locale->number( (int)Hash::get( $results, "total.nombre" ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( (int)Hash::get( $results, "total.nombre_unique" ) ), array( 'class' => 'integer number' ) )
			)
		);
		$tfoot = $this->Xhtml->tag( 'tfoot', $this->Xhtml->tableCells( $cells ) );

		echo $this->Xhtml->tag( 'table', $thead.$tfoot.$tbody ,array( 'class' => 'wide' ) );

		require_once( dirname( __FILE__ ).DS.'footer.ctp' );
	}
?>