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
	 *  - paginationTotal: le chemin vers la case à cocher qui indique que l'on
	 *    veut le nombre total d'enregistrements, par défaut, 'Search.Pagination.nombre_total'.
	 */
	$modelName = isset( $modelName ) ? $modelName : Inflector::classify( $this->request->params['controller'] );

	$paging = Hash::get( $this->request->params, "paging.{$modelName}" );

	if( $paging['nextPage'] === false ) {
		echo 'Résultats %start% - %end% sur %count% résultats.';
	}
	else {
		$paginationTotal = isset( $paginationTotal ) ? $paginationTotal : 'Search.Pagination.nombre_total';
		$progressive = !Hash::get( $this->request->data, $paginationTotal );
		echo SearchProgressivePagination::format( $progressive );
	}
?>