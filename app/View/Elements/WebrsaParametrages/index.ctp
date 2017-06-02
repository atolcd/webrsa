<?php
	/**
	 * Paramètres utilisés:
	 *	- results: obligatoire, les enregistrement à afficher
	 *	- cells: obligatoire, les cellules de chacune des lignes du tableau de résultats
	 *	- name: facultatif, le nom du contrôleur (pour l'URL d'ajout)
	 *	- modelClass: facultatif, le nom de la classe concernée par la pagination
	 *	- options: facultatif, les options à utiliser dans le tableau de résultats
	 *	- addUrl: facultatif, l'URL servant à ajouter un enregistrement
	 *	- addDisabled: facultatif, désactiver l'ajout d'un enregistrement
	 *	- backUrl: facultatif, l'URL servant à revenir au niveau précédent des paramétrages.
	 *	- messages: facultatif, un array de messages à afficher avec en valeur un nom de classe css.
	 */
	$name = isset( $name ) ? $name : $this->name;
	$modelClass = isset( $modelClass ) ? $modelClass : Inflector::classify( $name );
	$options = isset( $options ) ? $options : array();
	$addUrl = isset( $addUrl ) ? $addUrl : "/{$this->name}/add";
	$addDisabled = isset( $addDisabled ) ? $addDisabled : false;
	$backUrl = isset( $backUrl ) ? $backUrl : "/Parametrages/index";
	$messages = isset( $messages ) ? $messages : array();

	// -------------------------------------------------------------------------

	echo $this->Default3->titleForLayout();

	echo $this->Default3->messages( $messages );

	echo $this->Default3->actions(
		( false !== $backUrl ? array( $backUrl => array( 'class' => 'back' ) ) : array() )
		+ array( $addUrl => array( 'disabled' => $addDisabled ) )
	);

	echo $this->Default3->index(
		$results,
		$this->Translator->normalize( $cells ),
		array(
			'format' => $this->element( 'pagination_format', array( 'modelName' => $modelClass ) ),
			'options' => $options
		)
	);

	if( false !== $backUrl ) {
		echo $this->Default3->actions( array( $backUrl => array( 'class' => 'back' ) ) );
	}
?>