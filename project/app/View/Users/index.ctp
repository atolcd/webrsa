<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js' ), array( 'inline' => false ) );
	}

	echo $this->Default3->titleForLayout( array(), array( 'msgid' => __m( '/Users/index/:heading' ) ) );

	$departement = Configure::read( 'Cg.departement' );
	$searchFormId = 'UserIndexForm';
	$actions =  array(
		'/Users/add' => array(
		),
		'/Users/index/#toggleform' => array(
			'title' => 'Visibilité formulaire',
			'text' => 'Formulaire',
			'class' => 'search',
			'onclick' => "$( '{$searchFormId}' ).toggle(); return false;"
		)
	);
	echo $this->Default3->actions( $actions );

	$jetonsEnabled = ( Configure::read( 'Jetons2.disabled' ) ? '0' : '1' );
	$jetonsfonctionsEnabled = ( Configure::read( 'Jetonsfonctions2.disabled' ) ? '0' : '1' );

	$search = array_merge(
		array(
			'Search.User.search' => array( 'type' => 'hidden', 'value' => true, 'label' => false ),
			'Search.User.username' => array( 'required' => false ),
			'Search.User.nom' => array( 'required' => false ),
			'Search.User.prenom' => array( 'type' => 'text', 'required' => false ),
			'Search.User.group_id' => array( 'options' => $options['Groups'], 'empty' => true, 'required' => false ),
			'Search.User.serviceinstructeur_id' => array( 'options' => $options['Serviceinstructeur'], 'empty' => true, 'required' => false ),
			'Search.User.categorieutilisateur_id' => array('options' => $options['Categoriesutilisateurs'], 'empty' => true, 'required' => false ),
		),
		(
			( true === in_array( $departement, array( 66, 93) ) )
			? array_merge(
				array(
					'Search.User.type' => array( 'options' => $options['User']['type'], 'empty' => true, 'required' => false )
				),
				(
					( 93 == $departement )
					? array(
						'Search.User.communautesr_id' => array( 'options' => $options['communautessrs'], 'empty' => true, 'required' => false ),
						'Search.User.structurereferente_id' => array( 'empty' => true, 'required' => false ),
					)
					: array()
				),
				(
					( 66 == $departement )
					? array(
						'Search.User.poledossierpcg66_id' => array( 'options' => $options['polesdossierspcgs66'], 'empty' => true, 'required' => false ),
						'Search.User.ancienpoledossierpcg66_id' => array( 'options' => $options['polesdossierspcgs66'], 'empty' => true, 'required' => false )
					)
					: array()
				),
				array(
					'Search.User.referent_id' => array( 'empty' => true, 'required' => false )
				)
			)
			: array()
		),
		array(
			'Search.User.has_connections' => array( 'empty' => true, 'required' => false )
		)
	);

	if( $jetonsEnabled ) {
		$search['Search.User.has_jetons'] = array( 'empty' => true, 'required' => false );
	}

	if( $jetonsfonctionsEnabled ) {
		$search['Search.User.has_jetonsfonctions'] = array( 'empty' => true, 'required' => false );
	}

	$search['Search.Pagination.nombre_total'] = array( 'label' =>  __d( 'search_plugin', 'Search.Pagination.nombre_total' ), 'type' => 'checkbox' );

	echo $this->Default3->form(
		$this->Translator->normalize(
			$search
		),
		array(
			'id' => $searchFormId,
			'options' => array( 'Search' => $options ),
			'buttons' => array( 'Search', 'Reset' => array( 'type' => 'reset' ) ),
			// INFO: pour avoir une valeur vide pour les champs désactivés
			'hidden_empty' => array(
				'Search.User.communautesr_id',
				'Search.User.structurereferente_id',
				'Search.User.referent_id'
			),
			'class' => ( isset( $results ) ? 'folded' : 'unfolded' )
		)
	);
	echo $this->Observer->disableFormOnSubmit( $searchFormId );

	if( in_array( $departement, array( 66, 93 ) ) ) {
		echo $this->Observer->dependantSelect(
			array(
				'Search.User.structurereferente_id' => 'Search.User.referent_id'
			)
		);
	}

	if( 93 == $departement ) {
		echo $this->Observer->disableFieldsOnValue(
			'Search.User.communautesr_id',
			array( 'Search.User.structurereferente_id', 'Search.User.referent_id' ),
			array( '', null ),
			false
		);

		echo $this->Observer->disableFieldsOnValue(
			'Search.User.structurereferente_id',
			'Search.User.communautesr_id',
			array( '', null ),
			false
		);
	}

	if( isset( $results ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);

		$connectedUserId = $this->Session->read( 'Auth.User.id' );

		echo $this->Default3->index(
			$results,
			$this->Translator->normalize(
				array_merge(
					array(
						'User.nom',
						'User.prenom',
						'User.username',
						'User.date_deb_hab',
						'User.date_fin_hab'
					),
					(
						( in_array( $departement, array( 66, 93 ) ) )
						? array( 'User.type' )
						: array()
					),
					array(
						'Group.name',
						'Serviceinstructeur.lib_service',
						'User.has_connections' => array( 'type' => 'boolean' ),
						'User.has_jetons' => array(
							'condition' => $jetonsEnabled,
							'condition_group' => 'jetons',
							'type' => 'boolean'
						),
						'User.has_jetonsfonctions' => array(
							'condition' => $jetonsfonctionsEnabled,
							'condition_group' => 'jetonsfonctions',
							'type' => 'boolean'
						),
						'/Users/edit/#User.id#' => array(
							'title' => false
						),
						'/Users/duplicate/#User.id#' => array(
							'title' => false,
						),
						'/Users/delete_jetons/#User.id#' => array(
							'condition' => $jetonsEnabled,
							'condition_group' => 'jetons',
							'title' => false,
							'confirm' => true,
							'disabled' => '0 == "#User.has_jetons#"'
						),
						'/Users/delete_jetonsfonctions/#User.id#' => array(
							'condition' => $jetonsfonctionsEnabled,
							'condition_group' => 'jetonsfonctions',
							'title' => false,
							'confirm' => true,
							'disabled' => '0 == "#User.has_jetonsfonctions#"'
						),
						'/Users/force_logout/#User.id#' => array(
							'title' => false,
							'confirm' => true,
							'disabled' => '( 0 == "#User.has_connections#" || "'.$connectedUserId.'" == "#User.id#" )'
						),
						'/Users/delete/#User.id#' => array(
							'title' => false,
							'confirm' => true,
							'disabled' => '( 0 != "#User.has_linkedrecords#" || "'.$connectedUserId.'" == "#User.id#" )'
						)
					)
				)
			),
			array(
				'options' => $options,
				'format' => $this->element( 'pagination_format', array( 'modelName' => 'User' ) ),
				'innerTable' => $this->Translator->normalize(
					array(
						'User.date_naissance',
						'User.numtel',
						'ReferentAccueil.nom',
						'ReferentAccueil.prenom',
					)
				)
			)
		);
	}

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'parametrages',
			'action'     => 'index'
		)
	);
?>
<?php if( isset( $results ) ): ?>
<script type="text/javascript">
	//<![CDATA[
	$('<?php echo $searchFormId;?>').toggle();
	//]]>
</script>
<?php endif; ?>