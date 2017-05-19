<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default3->titleForLayout();

	$searchFormId = 'StructurereferenteIndexForm';
	$actions =  array(
		'/Structuresreferentes/add' => array(
		),
		'/Structuresreferentes/index/#toggleform' => array(
			'title' => 'Visibilité formulaire',
			'text' => 'Formulaire',
			'class' => 'search',
			'onclick' => "$( '{$searchFormId}' ).toggle(); return false;"
		)
	);
	echo $this->Default3->actions( $actions );

	echo $this->Form->create( null, array( 'type' => 'post', 'url' => array( 'controller' => $this->request->params['controller'], 'action' => $this->request->action ), 'id' => $searchFormId ) );

	$departement = (int)Configure::read( 'Cg.departement' );

	echo $this->Default3->subform(
		$this->Translator->normalize(
			array_merge(
				array(
					'Search.Structurereferente.search' => array( 'type' => 'hidden', 'value' => true ),
					'Search.Structurereferente.lib_struc' => array( 'type' => 'text', 'required' => false ),
					'Search.Structurereferente.ville' => array( 'required' => false ),
					'Search.Structurereferente.typeorient_id' => array( 'empty' => true, 'required' => false ),
				),
				(
					( 93 === $departement )
					? array( 'Search.Structurereferente.communautesr_id' => array( 'empty' => true, 'required' => false ) )
					: array()
				),
				array(
					'Search.Structurereferente.typestructure' => array( 'empty' => true, 'required' => false ),
					'Search.Structurereferente.actif' => array( 'empty' => true, 'required' => false )
				)
			)
		),
		array(
			'options' => array( 'Search' => $options ),
			'fieldset' => true,
			'legend' => __m( 'Search.Structurereferente.Structurereferente' ),
			'hidden_empty' => array(
				'Search.Structurereferente.typeorient_id',
				'Search.Structurereferente.communautesr_id'
			)
		)
	);

	echo $this->Default3->subform(
		$this->Translator->normalize(
			array(
				'Search.Structurereferente.apre' => array( 'empty' => true, 'required' => false ),
				'Search.Structurereferente.contratengagement' => array( 'empty' => true, 'required' => false ),
				'Search.Structurereferente.cui' => array( 'empty' => true, 'required' => false ),
				'Search.Structurereferente.orientation' => array( 'empty' => true, 'required' => false ),
				'Search.Structurereferente.pdo' => array( 'empty' => true, 'required' => false )
			)
		),
		array(
			'options' => array( 'Search' => $options ),
			'fieldset' => true,
			'legend' => __m( 'Search.Structurereferente.Gestion' )
		)
	);

	echo $this->Allocataires->blocPagination( array( 'prefix' => 'Search', 'options' => $options ) );
	echo $this->Allocataires->blocScript( array( 'prefix' => 'Search', 'options' => $options, 'id' => $searchFormId ) );
?>
	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php
	echo $this->Form->end();

	if ($departement === 93) {
		echo $this->Observer->disableFieldsOnValue(
			'Search.Structurereferente.communautesr_id',
			'Search.Structurereferente.typeorient_id',
			array( '', null ),
			false
		);

		echo $this->Observer->disableFieldsOnValue(
			'Search.Structurereferente.typeorient_id',
			'Search.Structurereferente.communautesr_id',
			array( '', null ),
			false
		);
	}

	if( isset( $results ) ) {
		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);

		echo $this->Default3->index(
			$results,
			$this->Translator->normalize(
				array(
					'Structurereferente.lib_struc',
					'Structurereferente.num_voie',
					'Structurereferente.type_voie',
					'Structurereferente.nom_voie',
					'Structurereferente.code_postal',
					'Structurereferente.ville',
					'Structurereferente.code_insee',
					'Structurereferente.numtel',
					'Typeorient.lib_type_orient',
					'Structurereferente.actif',
					'Structurereferente.typestructure',
					'/Structuresreferentes/edit/#Structurereferente.id#' => array(
						'title' => false
					),
					'/Structuresreferentes/delete/#Structurereferente.id#' => array(
						'title' => false,
						'confirm' => true,
						'disabled' => '0 != "#Structurereferente.has_linkedrecords#"'
					)
				)
			),
			array(
				'options' => $options,
				'format' => $this->element( 'pagination_format', array( 'modelName' => 'Structurereferente' ) )
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