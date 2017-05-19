<?php
	$this->pageTitle = 'États liquidatifs APRE';

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	if( $this->Permissions->check( 'etatsliquidatifs', 'add' ) ) {
		echo $this->Xhtml->tag(
			'ul',
			$this->Xhtml->tag(
				'li',
				$this->Xhtml->addLink(
					'Ajouter un état liquidatif',
					array( 'action' => 'add' )
				)
			),
			array( 'class' => 'actionMenu' )
		);
	}

	if( empty( $etatsliquidatifs ) ) {
		echo $this->Xhtml->tag( 'p', 'Aucun état liquidatif pour l\'instant.', array( 'class' => 'notice' ) );
	}
	else {
		$pagination = $this->Xpaginator->paginationBlock( 'Etatliquidatif', $this->passedArgs );

		$headers = array(
			$this->Xpaginator->sort( 'Entité financière', 'Etatliquidatif.entitefi' ),
			$this->Xpaginator->sort( 'Opération', 'Etatliquidatif.operation' ),
			$this->Xpaginator->sort( 'Exercice budgétaire', 'Budgetapre.exercicebudgetai' ),
			$this->Xpaginator->sort( 'Nature analytique', 'Etatliquidatif.natureanalytique' ),
			$this->Xpaginator->sort( 'Cdr.', 'Etatliquidatif.libellecdr' ),
			$this->Xpaginator->sort( 'Commentaire', 'Etatliquidatif.commentaire' ),
			$this->Xpaginator->sort( 'Date clôture', 'Etatliquidatif.datecloture' ),
		);

		///
		$thead = $this->Xhtml->tag( 'thead', $this->Xhtml->tableHeaders( $headers ) );
		$thead = str_replace( '</tr>', '<th colspan="7">Action</th></tr>', $thead );



		/// Corps du tableau
		$rows = array();
		$isComplementaire = false;
		foreach( $etatsliquidatifs as $etatliquidatif ) {

			$statut = Set::classicExtract( $etatliquidatif, 'Etatliquidatif.typeapre' );
			if( $statut == 'complementaire' ){
				$isComplementaire = true;
			}
			else if( $statut == 'forfaitaire' ) {
				$isComplementaire = false;
			}


			$cloture = Set::classicExtract( $etatliquidatif, 'Etatliquidatif.datecloture' );
			$cloture = ( !empty( $cloture ) );
			$rows[] = array(
				Set::classicExtract( $etatliquidatif, 'Etatliquidatif.entitefi' ),
				Set::classicExtract( $etatliquidatif, 'Etatliquidatif.operation' ),
				Set::classicExtract( $etatliquidatif, 'Budgetapre.exercicebudgetai' ),
				Set::classicExtract( $etatliquidatif, 'Etatliquidatif.natureanalytique' ),
				Set::classicExtract( $etatliquidatif, 'Etatliquidatif.libellecdr' ),
				Set::classicExtract( $etatliquidatif, 'Etatliquidatif.commentaire' ),
				$this->Locale->date( 'Date::short', Set::classicExtract( $etatliquidatif, 'Etatliquidatif.datecloture' ) ),
				// FIXME: droits
				$this->Theme->button( 'edit', array( 'action' => 'edit', Set::classicExtract( $etatliquidatif, 'Etatliquidatif.id' ) ), array( 'text' => 'Modifier', 'title' => 'Modifier l\'état liquidatif', 'enabled' => !$cloture ) ),
				$this->Theme->button( 'selection', array( 'action' => 'selectionapres', Set::classicExtract( $etatliquidatif, 'Etatliquidatif.id' ) ), array( 'text' => 'Sélection APREs', 'enabled' => !$cloture ) ),

				$this->Theme->button( 'money', array( 'action' => 'versementapres', Set::classicExtract( $etatliquidatif, 'Etatliquidatif.id' ) ), array( 'text' => 'Versements', 'enabled' => ( $isComplementaire && !$cloture && !empty( $apres_etatsliquidatifs ) ) ) ),

				$this->Theme->button( 'validate', array( 'action' => 'validation', Set::classicExtract( $etatliquidatif, 'Etatliquidatif.id' ) ), array( 'enabled' => ( !$cloture && !empty( $apres_etatsliquidatifs ) ) ) ),

				$this->Theme->button( 'table', array( 'action' => 'hopeyra', Set::classicExtract( $etatliquidatif, 'Etatliquidatif.id' ) ), array( 'text' => 'HOPEYRA', 'enabled' => ( $cloture && !$isComplementaire ) ) ),
				$this->Theme->button( 'pdf', array( 'action' => 'pdf', Set::classicExtract( $etatliquidatif, 'Etatliquidatif.id' ) ), array( 'text' => 'PDF', 'title' => 'Etat liquidatif', 'enabled' => $cloture ) ),
				$this->Theme->button( 'table', array( 'action' => 'visualisationapres', Set::classicExtract( $etatliquidatif, 'Etatliquidatif.id' ) ), array( 'text' => 'Notifications', 'enabled' => $cloture ) )
			);
		}
		$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $rows, array( 'class' => 'odd' ), array( 'class' => 'even' ) ) );

		///
		echo $pagination;
		echo $this->Xhtml->tag( 'table', $thead.$tbody, array( 'class' => 'nocssicons' ) );
		echo $pagination;
	}
?>