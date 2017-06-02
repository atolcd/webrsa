<?php
	echo $this->Default3->titleForLayout( array(), array( 'msgid' => "/Cataloguesromesv3/{$tableName}/:heading" ) );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Xhtml->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

	echo $this->Default3->actions(
		array(
			'/Parametrages/index/#cataloguesromesv3' => array( 'class' => 'back' ),
			"/Cataloguesromesv3/add/{$modelName}" => array(
				'title' => __d( 'cataloguesromesv3', "/Cataloguesromesv3/add/{$modelName}/:title" ),
				'disabled' => !$this->Permissions->check( 'Cataloguesromesv3', 'add' ),
			),
			"/Cataloguesromesv3/index/{$modelName}/#toggleform" => array(
				'onclick' => '$(\'Catalogueromev3IndexForm\').toggle(); return false;'
			)
		)
	);

	// Début du formulaire de recherche
	echo $this->Default3->DefaultForm->create( $modelName, array( 'id' => 'Catalogueromev3IndexForm', 'url' => array( 'controller' => $this->request->params['controller'], 'action' => $this->request->params['action'], $modelName ), 'novalidate' => true ) );

	if( in_array( $this->action, array( 'famillesromesv3', 'domainesromesv3', 'metiersromesv3', 'appellationsromesv3' ) ) ) {
		echo $this->Html->tag(
			'fieldset',
			$this->Html->tag( 'legend', __d( 'cataloguesromesv3', 'Search.Familleromev3' ) )
			.$this->Default3->subform(
				array(
					'Search.Familleromev3.code' => array( 'required' => false ),
					'Search.Familleromev3.name' => array( 'required' => false )
				),
				array(
					'domain' => 'cataloguesromesv3',
					'options' => $options
				)
			)
		);
	}

	if( in_array( $this->action, array( 'domainesromesv3', 'metiersromesv3', 'appellationsromesv3' ) ) ) {
		echo $this->Html->tag(
			'fieldset',
			$this->Html->tag( 'legend', __d( 'cataloguesromesv3', 'Search.Domaineromev3' ) )
			.$this->Default3->subform(
				array(
					'Search.Domaineromev3.code' => array( 'required' => false ),
					'Search.Domaineromev3.name' => array( 'required' => false )
				),
				array(
					'domain' => 'cataloguesromesv3',
					'options' => $options
				)
			)
		);
	}

	if( in_array( $this->action, array( 'metiersromesv3', 'appellationsromesv3' ) ) ) {
		echo $this->Html->tag(
			'fieldset',
			$this->Html->tag( 'legend', __d( 'cataloguesromesv3', 'Search.Metierromev3' ) )
			.$this->Default3->subform(
				array(
					'Search.Metierromev3.code' => array( 'required' => false ),
					'Search.Metierromev3.name' => array( 'required' => false )
				),
				array(
					'domain' => 'cataloguesromesv3',
					'options' => $options
				)
			)
		);
	}

	if( in_array( $this->action, array( 'appellationsromesv3' ) ) ) {
		echo $this->Html->tag(
			'fieldset',
			$this->Html->tag( 'legend', __d( 'cataloguesromesv3', 'Search.Appellationromev3' ) )
			.$this->Default3->subform(
				array(
					'Search.Appellationromev3.name' => array( 'required' => false )
				),
				array(
					'domain' => 'cataloguesromesv3',
					'options' => $options
				)
			)
		);
	}

	echo $this->Default3->DefaultForm->buttons( array( 'Search' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Allocataires->blocScript( array( 'id' => 'Catalogueromev3IndexForm' ) );
	// Fin du formulaire de recherche

	if( isset( $results ) ) {
		$fields["/Cataloguesromesv3/edit/{$modelName}/#{$modelName}.id#"] = array(
			'disabled' => !$this->Permissions->check( 'Cataloguesromesv3', 'edit' )
		);
		$fields["/Cataloguesromesv3/delete/{$modelName}/#{$modelName}.id#"] = array(
			'disabled' => "( '#{$modelName}.occurences#' != '0' ) || ( !'".$this->Permissions->check( 'Cataloguesromesv3', 'delete' )."' )",
			'confirm' => true
		);

		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
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
	}

	echo $this->Default3->actions( array( '/Parametrages/index/#cataloguesromesv3' => array( 'class' => 'back' ) ) );
?>