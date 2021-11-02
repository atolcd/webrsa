<?php
	echo $this->Default3->titleForLayout( array(), array( 'msgid' => "/Cataloguespdisfps93/index/{$modelName}/:heading" ) );

	$searchFormId = $modelName . 'IndexForm';
	$actions =  array(
		'/Parametrages/index' => array( 'class' => 'back' ),
		"/Cataloguespdisfps93/add/{$modelName}" => array(
			'title' => __d( 'cataloguespdisfps93', "/Cataloguespdisfps93/add/{$modelName}/:title" ),
			'disabled' => !$this->Permissions->check( 'Cataloguespdisfps93', 'add' ),
		)
	);

	if( isset($searchFields) ) {
		$actions['/Cataloguespdisfps93/index/#toggleform'] = array(
			'title' => __m('/Cataloguespdisfps93/search/:title'),
			'text' => __m('/Cataloguespdisfps93/search'),
			'class' => 'search',
			'onclick' => "$( '{$searchFormId}' ).toggle(); return false;"
		);
	}

	echo $this->Default3->actions( $actions );

	if( isset($searchFields) ) {
		echo $this->Form->create( null, array( 'type' => 'post', 'url' => array( 'controller' => $this->request->params['controller'], 'action' => $this->request->action ), 'id' => $searchFormId, 'novalidate' => true ) );

		echo $this->Default3->subform(
			$this->Translator->normalize(
				array_merge(
					array(
						'Search.model_name' => array( 'value' => $modelName, 'type' => 'hidden' )
					),
					$searchFields
				)
			),
			array(
				'options' => array( 'Search' => $options ),
				'fieldset' => true,
				'legend' => __m("Search.Cataloguespdisfps93.{$modelName}" )
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
	}


	if( isset( $results ) ) {
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

		if( isset($searchFields) ) {
			$this->Default3->DefaultPaginator->options(
				array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
			);
		} else {
			$this->Default3->DefaultPaginator->options(
				array( 'url' => array( $modelName ) )
			);
		}

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

	echo $this->Default3->actions(
		array(
			"/Parametrages/index/#fichesprescriptions93" => array(
				'class' => 'back',
				'domain' => 'cataloguespdisfps93'
			)
		)
	);
?>