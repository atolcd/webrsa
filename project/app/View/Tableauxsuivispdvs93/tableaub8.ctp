<?php
    require_once  dirname( __FILE__ ).DS.'search.ctp' ;

    if( isset( $results ) ) {
        $annee = Hash::get( $this->request->data, 'Search.annee' );
		$thead = $this->Xhtml->tag(
			'thead',
			$this->Xhtml->tableHeaders(
				array(
                    __d( $domain, 'Tableaub8.nbCER' ) . $annee,
                    __d( $domain, 'Tableaub8.jan' ),
                    __d( $domain, 'Tableaub8.feb' ),
                    __d( $domain, 'Tableaub8.mar' ),
                    __d( $domain, 'Tableaub8.apr' ),
                    __d( $domain, 'Tableaub8.may' ),
                    __d( $domain, 'Tableaub8.jun' ),
                    __d( $domain, 'Tableaub8.jul' ),
                    __d( $domain, 'Tableaub8.aug' ),
                    __d( $domain, 'Tableaub8.sep' ),
                    __d( $domain, 'Tableaub8.oct' ),
                    __d( $domain, 'Tableaub8.nov' ),
                    __d( $domain, 'Tableaub8.dec' )
				)
			)
		);

        $cells = array();
        foreach( $results as $structure => $result) {
            $cells[] = array(
                $structure,
                $result[1],
                $result[2],
                $result[3],
                $result[4],
                $result[5],
                $result[6],
                $result[7],
                $result[8],
                $result[9],
                $result[10],
                $result[11],
                $result[12],
            );
        }
		$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $cells ) );

        echo $this->Xhtml->tag( 'table', $thead.$tbody ,array( 'class' => 'wide tableau1b6' ) );
    }
    include_once  dirname( __FILE__ ).DS.'footer.ctp' ;