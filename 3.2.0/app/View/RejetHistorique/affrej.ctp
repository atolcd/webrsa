<?php

	$text = 'Rejets : '.$fichier;
	$this->pageTitle = $text ;

	if( empty( $rejetHistoriques ) ) {

		echo $this->Html->tag( 'p', 'Aucun rejet.', array( 'class' => 'notice' ) );
	}
	else {

	echo $this->Html->tag( 'h1', $this->pageTitle );

       	$pagination = $this->Xpaginator->paginationBlock( 'RejetHistorique', $this->passedArgs );

		//----------------------------------------------------------------------

		$headers = array(
			'Numero RSA',
			'Matricule',
			'Erreur',

			);

		$thead = $this->Html->tag( 'thead', $this->Html->tableHeaders( $headers ) );
		$thead = str_replace( '</tr>', '<th colspan="7">Voir XML</th></tr>', $thead );

		/// Corps du tableau
		$rows = array();

		foreach ($rejetHistoriques as $rejetHistorique){


			$rows[] = array(
				Set::classicExtract( $rejetHistorique, 'RejetHistorique.numdemrsa' ),
				Set::classicExtract( $rejetHistorique, 'RejetHistorique.matricule' ),
				Set::classicExtract( $rejetHistorique, 'RejetHistorique.log' ),

				$this->Xhtml->viewLink(
					'Voir le xml',
					array( 'controller' => 'rejet_historique', 'action' => 'affxml', $fichier, $rejetHistorique['RejetHistorique']['numdemrsa'] ),
					$this->Permissions->check( 'RejetHistorique', 'affxml' )
				),
			);
		}
		$tbody = $this->Html->tag( 'tbody', $this->Html->tableCells( $rows, array( 'class' => 'odd' ), array( 'class' => 'even' ) ) );

		echo $pagination;
		echo $this->Html->tag( 'table', $thead.$tbody );
		echo $pagination;

	}
?>