<?php
	if( $this->request->action != 'view' ) {
		$url = Hash::merge(
			array( 'action' => 'historiser', $this->action ),
			Hash::flatten( $this->request->data )
		);

		$actions = array(
			DefaultUrl::toString( $url ) => array(
				'enabled' => $this->Permissions->check( $this->request->params['controller'], 'historiser' ),
			)
		);
	}
	else {
		$actions = $this->Default3->DefaultAction->back();
		$id = $tableausuivipdv93['Tableausuivipdv93']['id'];

		// On permet l'export CSV des tableaux
		if( in_array( $tableausuivipdv93['Tableausuivipdv93']['name'], array( 'tableaud1', 'tableaud2', 'tableau1b3', 'tableau1b4', 'tableau1b5', 'tableau1b6', 'tableaub7', 'tableaub7d2typecontrat', 'tableaub7d2familleprofessionnelle' ) ) ) {
			// Export CSv du tableau de résultats
			$url = array( 'action' => 'exportcsvdonnees', $tableausuivipdv93['Tableausuivipdv93']['name'], $id );
			$actions[DefaultUrl::toString( $url )] = array(
				'enabled' => $this->Permissions->check( $this->request->params['controller'], 'exportcsvdonnees' ),
			);

			// Export CSV du corpus
			$url = array( 'action' => 'exportcsvcorpus', $tableausuivipdv93['Tableausuivipdv93']['name'], $id );
			$actions[DefaultUrl::toString( $url )] = array(
				'enabled' => $this->Permissions->check( $this->request->params['controller'], 'exportcsvcorpus' ),
			);
		}
	}

	echo $this->DefaultDefault->actions( $actions );
?>