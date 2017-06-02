<?php
	/**
	 * Cet élément retourne différents format à utiliser pour la pagination:
	 *  - avec la pagintion progressive et une page suivante: la traduction de
	 *    "'Page %page% of %pages%, showing %current% records out of %count% total,
	 *    starting on record %start%, ending on %end%'"
	 *  - sans la pagination progressive et avec plus d'une page: "Résultats %start%
	 *    - %end% sur au moins %count% résultats."
	 *  - avec une seule page: "Résultats %start% - %end% sur %count% résultats."
	 *
	 * Les paramètres pouvant être passés à cet élément ont les clés suivantes:
	 *	- modelName: le nom du modèle sur lequel se fait la pagination, par défaut
	 *	  le nom du modèle lié au contrôleur courant.
	 */
	if( false === isset( $modelName ) ) {
		$keys = array_keys( $this->request->params['paging'] );
		if( true === isset( $keys[0] ) ) {
			$modelName = $keys[0];
		}
		else {
			$modelName = Inflector::classify( $this->request->params['controller'] );
		}
	}

	$paging = Hash::get( $this->request->params, "paging.{$modelName}" );

	if( true === $paging['nextPage'] && true === isset( $paging['progressive'] ) ) {
		echo __m( 'Page %%page%% sur au moins %%pages%%, résultats %%start%% à %%end%% sur au moins %%count%% résultats' );
	}
	else {
		echo __m( 'Page %%page%% sur %%pages%%, résultats %%start%% à %%end%% sur %%count%% résultats' );
	}
?>