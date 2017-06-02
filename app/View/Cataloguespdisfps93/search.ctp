<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Xhtml->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

	echo $this->Default3->actions(
		array(
			'/Cataloguespdisfps93/search/#toggleform' => array(
				'onclick' => '$(\'Cataloguespdisfps93SearchForm\').toggle(); return false;'
			),
		)
	);

	echo $this->Xform->create( 'Search', array( 'id' => 'Cataloguespdisfps93SearchForm' ) );

	echo $this->Default3->subform(
		array(
			'Search.Thematiquefp93.type' => array( 'required' => false, 'empty' => true ),
			'Search.Thematiquefp93.name' => array( 'required' => false ),
			'Search.Categoriefp93.name' => array( 'required' => false ),
			'Search.Filierefp93.name' => array( 'required' => false ),
			'Search.Prestatairefp93.name' => array( 'required' => false ),
			'Search.Actionfp93.name' => array( 'required' => false ),

			'Search.Actionfp93.actif' => array( 'required' => false, 'empty' => true ),
			'Search.Actionfp93.annee' => array( 'required' => false, 'type' => 'text' ),
			'Search.Actionfp93.numconvention' => array( 'required' => false ),
		),
		array(
			'options' => array( 'Search' => $options ),
			'domain' => 'cataloguespdisfps93',
			'fieldset' => true,
			'legend' => __d( 'cataloguespdisfps93', 'Search.Cataloguepdifp93' ),
		)
	);
//'Thematiquefp93.type',
//'Thematiquefp93.name',
//'Filierefp93.name',
//
//'Prestatairefp93.name',
//'Actionfp93.annee',
//'Actionfp93.name',
//'Actionfp93.actif',

	echo $this->Allocataires->blocPagination( array( 'options' => $options ) );
	echo $this->Allocataires->blocScript( array( 'options' => $options ) );

	echo $this->Xform->end( 'Search' );

	if( isset( $results ) ) {
		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);

		App::uses( 'SearchProgressivePagination', 'Search.Utility' );
		$modelName = 'Actionfp93';

		echo $this->Default3->index(
			$results,
			array(
				'Thematiquefp93.type',
				'Thematiquefp93.name',
				'Categoriefp93.name',
				'Filierefp93.name',
				'Prestatairefp93.name',
				'Actionfp93.annee' => array( 'type' => 'text' ),
				'Actionfp93.name',
				'Actionfp93.actif',
				"/Cataloguespdisfps93/edit/{$modelName}/#{$modelName}.id#" => array(
					'disabled' => "( '#{$modelName}.id#' == '' || !'".$this->Permissions->check( 'Cataloguespdisfps93', 'edit' )."' )",
				),
				"/Cataloguespdisfps93/delete/{$modelName}/#{$modelName}.id#" => array(
					'disabled' => "( '#{$modelName}.id#' == '' ||  '#{$modelName}.occurences#' != '0' ) || ( !'".$this->Permissions->check( 'Cataloguespdisfps93', 'delete' )."' )",
					'confirm' => true
				),
			),
			array(
				'options' => $options,
				'format' => $this->element( 'pagination_format' )
			)
		);
	}

	echo $this->Default3->actions(
		array(
			"/Parametrages/index/#fichesprescriptions93" => array(
				'class' => 'back',
				'domain' => 'cataloguespdisfps93'
			)
		)
	);
?>