<?php
	$this->pageTitle = 'Versements pour les APREs complémentaires pour l\'état liquidatif';

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	///Fin pagination


	if( empty( $apres ) ) {
		echo $this->Xhtml->tag( 'p', 'Aucune APRE à sélectionner.', array( 'class' => 'notice' ) );
	}
	else {
		$pagination = $this->Xpaginator->paginationBlock( 'Apre', $this->passedArgs );

		$headers = array(
			$this->Xpaginator->sort( 'N° Dossier', 'Dossier.numdemrsa' ),
			$this->Xpaginator->sort( 'N° APRE', 'Apre.numeroapre' ),
			$this->Xpaginator->sort( 'Date de demande APRE', 'Apre.datedemandeapre' ),
			$this->Xpaginator->sort( 'Nom bénéficiaire', 'Personne.nom' ),
			$this->Xpaginator->sort( 'Prénom bénéficiaire', 'Personne.prenom' ),
			$this->Xpaginator->sort( 'Adresse', 'Adresse.nomcom' ),
			$this->Xpaginator->sort( 'Montant attribué par le comité', 'Apre.montantaverser' ),
			'Nb paiement souhaité',
			'Nb paiement effectué',
			'Montant à verser',
			'Montant déjà versé',
		);

		///
		$thead = $this->Xhtml->tag( 'thead', $this->Xhtml->tableHeaders( $headers ) );

		echo $this->Xform->create( 'ApreEtatliquidatif' );

		/// Corps du tableau
		$rows = array();
		$ajaxes = array();
		foreach( $apres as $i => $apre ) {
			$params = array( 'id' => "apre_{$i}", 'class' => ( ( $i % 2 == 1 ) ? 'odd' : 'even' ) );
			$rows[] = $this->Xhtml->tag( 'tr', $this->Apreversement->cells( $i, $apre, $nbpaiementsouhait ), $params );

			/**
			*   Ajax
			**/
			$ajaxes[] = $this->Ajax->observeField(
				"Apre{$i}Nbpaiementsouhait",
				array(
					'success' => "\ntry {
	var json = request.responseText.evalJSON(true);
	$( 'ApreEtatliquidatif{$i}Montantattribue' ).value = json.montantattribue;
}
catch(e) {
	alert( 'Erreur' );
}",
					'url' => array(
						'action' => 'ajaxmontant',
						$this->request->params['pass'][0],
						Set::classicExtract( $apre, 'Apre.id' ),
						$i
					),
				)
			);
		}
		$tbody = $this->Xhtml->tag( 'tbody', implode( '', $rows ) );

		echo $pagination;
		echo $this->Xhtml->tag( 'table', $thead.$tbody );
		echo $pagination;

		$buttons = array();
		$buttons[] = $this->Xform->submit( 'Valider la liste', array( 'div' => false ) );
		$buttons[] = $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
		echo $this->Xhtml->tag( 'div', implode( '', $buttons ), array( 'class' => 'submit' ) );

		echo $this->Xform->end();
		echo implode( '', $ajaxes );
	}
?>