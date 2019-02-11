<?php
	echo $this->Default3->titleForLayout( array(), array( 'msgid' => "/Cataloguespdisfps93/index/{$modelName}/:heading" ) );

	echo $this->Default3->actions(
		array(
			"/Cataloguespdisfps93/add/{$modelName}" => array(
				'title' => __d( 'cataloguespdisfps93', "/Cataloguespdisfps93/add/{$modelName}/:title" ),
				'disabled' => !$this->Permissions->check( 'Cataloguespdisfps93', 'add' ),
			)
		)
	);

	//Si on doit affichée l'année alors on corrige l'effet texte
	$keypos = array_keys($fields, 'Actionfp93.annee');
	if (!empty ($keypos) ) {
			$fields = array(
		'Thematiquefp93.yearthema',
		'Thematiquefp93.type',
		'Thematiquefp93.name',
		'Categoriefp93.name',
		'Filierefp93.name',
		'Prestatairefp93.name',
		'Adresseprestatairefp93.name',
		'Actionfp93.name',
		'Actionfp93.numconvention',
		'Actionfp93.annee' => array( 'type' => 'text' ),
		'Actionfp93.duree',
		'Actionfp93.actif',
		'Actionfp93.created',
		'Actionfp93.modified',
	  );
	}

	$fields["/Cataloguespdisfps93/edit/{$modelName}/#{$modelName}.id#"] = array(
		'disabled' => !$this->Permissions->check( 'Cataloguespdisfps93', 'edit' )
	);
	$fields["/Cataloguespdisfps93/delete/{$modelName}/#{$modelName}.id#"] = array(
		'disabled' => "( '#{$modelName}.occurences#' != '0' ) || ( !'".$this->Permissions->check( 'Cataloguespdisfps93', 'delete' )."' )",
		'confirm' => true
	);

	$this->Default3->DefaultPaginator->options(
		array( 'url' => array( $modelName ) )
	);

	App::uses( 'SearchProgressivePagination', 'Search.Utility' );

	echo $this->Default3->index(
		$results,
		$fields,
		array(
			'options' => $options,
			'format' => $this->element( 'pagination_format' )
		)
	);

	echo $this->Default3->actions(
		array(
			"/Parametrages/index/#fichesprescriptions93" => array(
				'class' => 'back',
				'domain' => 'cataloguespdisfps93'
			)
		)
	);
?>