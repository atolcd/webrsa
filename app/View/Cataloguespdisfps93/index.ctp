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
			'format' => __( SearchProgressivePagination::format() )
		)
	);

	echo $this->Default3->actions(
		array(
			"/Parametrages/fichesprescriptions93" => array(
				'class' => 'back',
				'disabled' => !$this->Permissions->check( 'parametrages', 'fichesprescriptions93' ),
				'domain' => 'cataloguespdisfps93'
			),
		)
	);
?>