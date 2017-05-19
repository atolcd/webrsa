<?php
	/**
	 * Liste des dossiers dans lesquels l'allocataire ne possède plus de prestation
	 * mais pour lesquels des enregistrements ont été trouvés.
	 */
	if( isset( $entriesAncienDossier ) ) {
		if( !empty( $entriesAncienDossier ) ) {
			$controllerName = Inflector::camelize( $this->request->params['controller'] );
			$modelName = Inflector::singularize( $controllerName );

			echo $this->Html->tag( 'h2', __d( 'ancien_dossier', 'EntriesAncienDossier' ) );

			$index = $this->Default3->index(
				$entriesAncienDossier,
				array(
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Dossier.matricule',
					'Personne.records' => array( 'type' => 'integer' ),
					"/{$controllerName}/{$this->request->params['action']}/#Personne2.id#" => array(
						'class' => 'ancien_dossier external view',
						'domain' => 'ancien_dossier'
					)
				),
				array(
					'paginate' => false,
					'domain' => 'ancien_dossier'
				)
			);

			$domId = $this->Html->domId( "Table.{$controllerName}.{$this->request->params['action']}" );
			$newDomId = $this->Html->domId( "Table.ancien_dossier.{$controllerName}.{$this->request->params['action']}" );
			$index = str_replace( "table id=\"{$domId}\" class=\"", "table id=\"{$domId}\" class=\"ancien_dossier ", $index );
			$index = str_replace( $domId, $newDomId, $index );
			$index = str_replace( ">/{$controllerName}/{$this->request->params['action']}<", '>Voir<', $index );
			$index = preg_replace( '/title="[^"]*"/', 'title="Voir les enregistrement de l\'ancien dossier"', $index );

			echo $index;

			echo $this->Html->tag( 'h2', __d( 'ancien_dossier', 'EntriesDossierActuel' ) );
		}
	}
?>