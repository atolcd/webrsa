<?php
	// $actions
	$actions = isset( $actions ) ? (array)$actions : array();
	// $searchFormId
	$searchFormId = isset( $searchFormId ) ? $searchFormId : Inflector::camelize( "{$this->request->params['controller']}_{$this->request->params['action']}_form" );
	//--------------------------------------------------------------------------
	$beforeSearch = isset( $beforeSearch ) ? $beforeSearch : '';
	$customSearch = isset( $customSearch ) ? $customSearch : '';
	$afterSearch = isset( $afterSearch ) ? $afterSearch : '';
	$beforeResults = isset( $beforeResults ) ? $beforeResults : '';
	$afterResults = isset( $afterResults ) ? $afterResults : '';
	//--------------------------------------------------------------------------
	$searchKey = isset( $searchKey ) ? $searchKey : 'Search';
	// $url
	// $exportcsv
	$exportcsv = isset( $exportcsv ) ? $exportcsv : array( 'action' => 'exportcsv' );
	if( is_array( $exportcsv ) ) {
		$exportcsv += array( 'controller' => $this->request->params['controller'] );
	}

	// $css
	// $scripts
	// $modelName
	$modelName = isset( $modelName ) ? $modelName : Inflector::classify( $this->request->params['controller'] );

	// $paginate
	$paginate = isset( $paginate ) ? $paginate : null;

	$configurableQueryParams = isset($configurableQueryParams) ? $configurableQueryParams : array();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js', 'cake.prototype.js' ) );
	}

	echo $this->Default3->titleForLayout();

	$actions['/'.Inflector::camelize( $this->request->params['controller'] ).'/'.$this->request->params['action'].'/#toggleform'] =  array(
		'title' => 'Visibilité formulaire', // TODO: nettoyer les fichiers de traduction
		'text' => 'Formulaire', // TODO: nettoyer les fichiers de traduction
		'class' => 'search',
		'onclick' => "$( '{$searchFormId}' ).toggle(); return false;"
	);
	echo $this->Default3->actions( $actions );

	echo $this->Form->create( null, array( 'type' => 'post', 'url' => array( 'controller' => $this->request->params['controller'], 'action' => $this->request->action ), 'id' => $searchFormId, 'class' => ( isset( $results ) ? 'folded' : 'unfolded' ), 'novalidate' => true ) );

	echo $beforeSearch;
	if (Configure::read('Module.Savesearch.enabled') && WebrsaPermissions::check('savesearchs', 'index')) {
		echo $this->element('saved_criteres');
	}
	echo $this->Allocataires->blocDossier( array( 'prefix' => 'Search', 'options' => $options ) );
	echo $this->Allocataires->blocAdresse( array( 'prefix' => 'Search', 'options' => $options ) );
	echo $this->Allocataires->blocAllocataire(
		array('prefix' => 'Search', 'options' => $options, 'configurableQueryParams' => $configurableQueryParams)
	);
	echo $customSearch;
	echo $this->Allocataires->blocHave( array( 'prefix' => 'Search', 'options' => $options ) );
	echo $this->Allocataires->blocReferentparcours( array( 'prefix' => 'Search', 'options' => $options ) );
	echo $this->Allocataires->blocPagination( array( 'prefix' => 'Search', 'options' => $options ) );
	echo $this->Allocataires->blocScript( array( 'prefix' => 'Search', 'options' => $options ) );
	echo $afterSearch;
?>
	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php
	echo $this->Form->end();
	echo $this->Observer->disableFormOnSubmit( $searchFormId );

	if( isset( $results ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		if (Configure::read('Module.Savesearch.enabled') && WebrsaPermissions::check('savesearchs', 'save')) {
			echo $this->element('savesearch');
		}

		echo $beforeResults;

		echo $this->Default3->configuredindex(
			$results,
			array(
				'format' => $this->element( 'pagination_format', array( 'modelName' => $modelName ) ),
				'options' => $options,
				'paginate' => $paginate
			)
		);

		echo $afterResults;

		if( $exportcsv !== false ) {
			echo $this->element( 'search_footer', array( 'modelName' => $modelName, 'url' => $exportcsv, 'searchKey' => $searchKey ) );
		}
	}
?>
<script type="text/javascript">
//<![CDATA[
	Event.observe( window, 'load', function() {
		Event.observe( '<?php echo $searchFormId;?>', 'submit', Cake.Search.onSubmit );
	} );
//]]>
</script>