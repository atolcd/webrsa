<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default3->titleForLayout();

	$searchFormId = "{$this->request->params['controller']}_{$this->request->params['action']}_form";
	$actions =  array(
		'/Referents/add' => array(),
		"/Referents/{$this->request->params['action']}/#toggleform" => array(
			'title' => 'Visibilité formulaire',
			'text' => 'Formulaire',
			'class' => 'search',
			'onclick' => "$( '{$searchFormId}' ).toggle(); return false;"
		)
	);
	echo $this->Default3->actions( $actions );

	echo $this->Form->create( null, array( 'type' => 'post', 'url' => array( 'controller' => $this->request->params['controller'], 'action' => $this->request->action ), 'id' => $searchFormId, 'class' => ( isset( $referents ) ? 'folded' : 'unfolded' ) ) );

	echo $this->Default3->subform(
		$this->Translator->normalize(
			array(
				'Search.Referent.nom' => array( 'required' => false ),
				'Search.Referent.prenom' => array( 'required' => false ),
				'Search.Referent.fonction' => array( 'required' => false ),
				'Search.Referent.structurereferente_id' => array(
					'label' => 'Structure référente liée',
					'required' => false,
					'empty' => true
				),
				'Search.Referent.actif' => array( 'required' => false, 'empty' => true )
			)
		),
		array(
			'options' => array( 'Search' => $options ),
			'fieldset' => true,
			'legend' => 'Filtrer par référent'
		)
	);

	echo $this->SearchForm->dateRange( 'Search.Referent.datecloture', array(
		'domain' => Inflector::underscore( "{$this->request->params['controller']}_{$this->request->params['action']}" ),
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 4
	) );

	echo $this->Allocataires->blocPagination( array( 'prefix' => 'Search', 'options' => $options ) );
?>
	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php
	echo $this->Form->end();
	echo $this->Observer->disableFormOnSubmit( $searchFormId );

	if( isset( $referents ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);

		echo $this->Default3->index(
			$referents,
			$this->Translator->normalize(
				array(
					'Referent.qual',
					'Referent.nom',
					'Referent.prenom',
					'Referent.fonction',
					'Referent.numero_poste',
					'Referent.email',
					'Structurereferente.lib_struc',
					'Referent.actif',
					'Referent.datecloture',
					'/referents/cloturer/#Referent.id#' => array(
						'disabled' => "('#Referent.datecloture#' == '' || '#PersonneReferent.nb_referents_lies#' > 0) === false"
					),
					'/referents/edit/#Referent.id#',
					'/referents/delete/#Referent.id#' => array(
						'disabled' => "('#Referent.has_linkedrecords#') == 1"
					),
				)
			),
			array(
				'options' => $options,
				'format' => $this->element( 'pagination_format', array( 'modelName' => 'Referent' ) )
			)
		);
	}

	if( 'index' === $this->request->params['action'] ) {
		echo $this->Default->button(
			'back',
			array(
				'controller' => 'parametrages',
				'action'     => 'index'
			)
		);
	}
?>