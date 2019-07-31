<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ;
	if( isset( $results ) ) {
		$annee = Hash::get( $this->request->data, 'Search.annee' );

		$types = array(
			'nbMoyJrsRdvIndivPrevuPIE' => array( 'type' => 'float', 'result' => 'avg' ),
			'nbMoyJrsRdvIndivHonorePIE' => array( 'type' => 'float', 'result' => 'avg' ),
			'nbMoyJrsRdvCollPrevuPIE' => array( 'type' => 'float', 'result' => 'avg' ),
			'nbMoyJrsRdvCollHonorePIE' => array( 'type' => 'float', 'result' => 'avg' ),
			'nbMoyJrsCerEtabliPIE' => array( 'type' => 'float', 'result' => 'avg' ),
			'nbMoyJrsRdvIndivCerEtabliePIE' => array( 'type' => 'float', 'result' => 'avg' )
		);

		//**************************************************************************
		$indicateurs = $results;

		$rows = array();
		foreach( $indicateurs as $key => $indicateur ) {
			$row = array( '<td>'.__d( 'indicateurmensuel', 'Indicateurmensuel.'.$key ).'</td>' );
			$type = Set::extract( $types, $key.'.type' );
			$result = Set::extract( $types, $key.'.result' );
			for( $i = 1 ; $i <= 12 ; $i++ ) {
				$value = ( ( isset( $indicateur[$i] ) ? $indicateur[$i] : 0 ) );
				$row[] = '<td class="number">'.$this->Locale->number( $value, ( ( $type == 'int' ) ? 0 : 2 ) ).'</td>';
			}
			$value = ( ( $type == 'int' ) ? array_sum( $indicateur ) : array_avg( $indicateur ) );
			$row[] = '<td class="number"><strong>'.$this->Locale->number( $value, ( ( $type == 'int' ) ? 0 : 2 ) ).'</strong></td>';
			$rows[] = '<tr class="'.( ( ( count( $rows ) + 1 ) % 2 ) == 0 ? 'even' : 'odd' ).'">'.implode( '', $row ).'</tr>';
		}
		//**************************************************************************

		$headers = array( null );
		for( $i = 1 ; $i <= 12 ; $i++ ) {
			$headers[] = ucfirst( $this->Locale->date( '%b %Y', $annee.( ( $i < 10 ) ? '0'.$i : $i ).'01' ) );
		}
		$headers[] = 'Total / Moyenne '.$annee;

		$thead = $this->Xhtml->tag( 'thead', $this->Xhtml->tableHeaders( $headers ) );
		$tbody = $this->Xhtml->tag( 'tbody', implode( '', $rows ) );
		echo $this->Xhtml->tag( 'table', $thead.$tbody );
	}
	include_once  dirname( __FILE__ ).DS.'footer.ctp' ;