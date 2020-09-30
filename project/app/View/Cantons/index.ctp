<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	};

	if( false === empty( $notice ) ) {
		echo $this->Html->tag( 'p', $notice, array( 'class' => 'notice' ) );
	}

	echo $this->Default3->titleForLayout();

	$searchFormId = 'CantonIndexForm';
	$actions =  array(
		'/Parametrages/index' => array( 'class' => 'back' ),
		'/Cantons/add' => array(),
		'/Cantons/index/#toggleform' => array(
			'title' => 'Visibilité formulaire',
			'text' => 'Formulaire',
			'class' => 'search',
			'onclick' => "$( '{$searchFormId}' ).toggle(); return false;"
		)
	);
	echo $this->Default3->actions( $actions );

	echo $this->Default3->form(
		$this->Translator->normalize(
			array(
				'Search.Canton.canton' => array( 'required' => false ),
				'Search.Canton.nomcom' => array( 'required' => false ),
				'Search.Canton.zonegeographique_id' => array( 'empty' => true, 'required' => false ),
				'Search.Canton.codepos' => array( 'required' => false ),
				'Search.Canton.numcom' => array( 'required' => false ),
				'Search.Canton.cantonvide' => array('type' => 'checkbox', 'required' => false )
			)
		),
		array(
			'buttons' => array( 'Search', 'Reset' ),
			'options' => array( 'Search' => $options ),
			'id' => $searchFormId,
			'class' => isset( $results ) ? 'folded' : 'unfolded'
		)
	);

	echo $this->Observer->disableFormOnSubmit();

	if( true === isset( $results ) ) {
		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);

		echo $this->Default3->index(
			$results,
			$this->Translator->normalize(
				array(
					'Canton.canton',
					'Zonegeographique.libelle',
					'Canton.numvoie',
					'Canton.libtypevoie',
					'Canton.nomvoie',
					'Canton.nomcom',
					'Canton.codepos',
					'Canton.numcom',
					'/Cantons/edit/#Canton.id#' => array(
						'title' => true
					),
					'/Cantons/delete/#Canton.id#' => array(
						'title' => true,
						'confirm' => true,
						'disabled' => 'true == "#Canton.has_linkedrecords#"'
					)
				)
			),
			array(
				'format' => $this->element( 'pagination_format', array( 'modelName' => 'Canton' ) )
			)
		);
	}

	echo $this->Default3->actions (
		array (
			'/Parametrages/index' => array( 'class' => 'back' ),
			'/Cantons/adressesnonassociees' => array( 'class' => 'exportcsv' ),
			'/Cantons/adressessanscanton' => array( 'class' => 'exportcsv' )
		)
	);

	echo (__m('Canton.adressesnonassociees.message'));
