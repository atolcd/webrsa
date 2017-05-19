<?php
	$this->pageTitle = 'Budgets APRE';

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	if( $this->Permissions->check( 'budgetsapres', 'add' ) ) {
		echo $this->Xhtml->tag(
			'ul',
			$this->Xhtml->tag(
				'li',
				$this->Xhtml->addLink(
					'Ajouter un budget',
					array( 'controller' => 'budgetsapres', 'action' => 'add' )
				)
			),
			array( 'class' => 'actionMenu' )
		);
	}

	if( empty( $budgetsapres ) ) {
		echo $this->Xhtml->tag( 'p', 'Aucun budget pour l\'instant.', array( 'class' => 'notice' ) );
	}
	else {
		$pagination = $this->Xpaginator->paginationBlock( 'Budgetapre', $this->passedArgs );

		//----------------------------------------------------------------------

		$headers = array(
			'Exercice budgétaire',
			'Date de début d\'exécution',
			'Date de fin d\'exécution',
			'Attribution état',
			'Consommation budget',
			'Ratio'
		);

		///
		$thead = $this->Xhtml->tag( 'thead', $this->Xhtml->tableHeaders( $headers ) );
		$thead = str_replace( '</tr>', '<th colspan="2">Action</th></tr>', $thead );

		/// Corps du tableau
		$rows = array();
		foreach( $budgetsapres as $budgetapre ) {
			$montantattretat = Set::classicExtract( $budgetapre, 'Budgetapre.montantattretat' );
			$montantattretat = ( empty( $montantattretat ) ? 0 : $montantattretat );

			$montanttotalapre = Set::extract( $budgetapre, '/Etatliquidatif/montanttotalapre' );
			$montanttotalapre = array_sum( $montanttotalapre);

			$rows[] = array(
				Set::classicExtract( $budgetapre, 'Budgetapre.exercicebudgetai' ),
				$this->Locale->date( 'Date::short', Set::classicExtract( $budgetapre, 'Budgetapre.ddexecutionbudge' ) ),
				$this->Locale->date( 'Date::short', Set::classicExtract( $budgetapre, 'Budgetapre.dfexecutionbudge' ) ),
				$this->Locale->money( $montantattretat, 2 ),
				$this->Locale->money( $montanttotalapre, 2 ), // FIXME -> $montantattretat < $montanttotalapre
				$this->Locale->number( ( $montanttotalapre / $montantattretat ) * 100, 2 ).'&nbsp;%',
				// FIXME: droits
				$this->Xhtml->editLink( 'Éditer le budget', array( 'controller' => 'budgetsapres', 'action' => 'edit', Set::classicExtract( $budgetapre, 'Budgetapre.id' ) ) ),
				$this->Theme->button( 'view', array( 'controller' => 'etatsliquidatifs', 'action' => 'index', 'budgetapre_id' => Set::classicExtract( $budgetapre, 'Budgetapre.id' ) ), array( 'text' => 'Voir états liquidatifs' ) ),
			);
		}
		$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $rows, array( 'class' => 'odd' ), array( 'class' => 'even' ) ) );

		///
		echo $pagination;
		echo $this->Xhtml->tag( 'table', $thead.$tbody );
		echo $pagination;
	}
?>