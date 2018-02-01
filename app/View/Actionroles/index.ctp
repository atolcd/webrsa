<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default3->titleForLayout();

	$searchFormId = "{$this->request->params['controller']}_{$this->request->params['action']}_form";
	$actions =  array(
		'/Parametrages/index' => array( 'class' => 'back' ),
		'/Actionroles/add' => array(),
		"/Actionroles/{$this->request->params['action']}/#toggleform" => array(
			'title' => 'Visibilité formulaire',
			'text' => 'Formulaire',
			'class' => 'search',
			'onclick' => "$( '{$searchFormId}' ).toggle(); return false;"
		)
	);
	echo $this->Default3->actions( $actions );

	echo $this->Form->create( null, array( 'type' => 'post', 'url' => array( 'controller' => $this->request->params['controller'], 'action' => $this->request->action ), 'id' => $searchFormId, 'class' => ( isset( $results ) ? 'folded' : 'unfolded' ), 'novalidate' => true ) );

	echo $this->Default3->subform(
		$this->Translator->normalize(
			array(
				'Search.Actionrole.categorieactionrole_id' => array( 'required' => false, 'empty' => true ),
				'Search.Actionrole.role_id' => array( 'required' => false, 'empty' => true ),
				'Search.Role.actif' => array( 'required' => false, 'empty' => true ),
				'Search.Actionrole.name' => array( 'required' => false ),
				'Search.Actionrole.description' => array( 'required' => false, 'type' => 'text' ),
				'Search.RoleUser.user_id' => array( 'required' => false, 'empty' => true )
			)
		),
		array(
			'options' => array( 'Search' => $options ),
			'fieldset' => true,
			'legend' => 'Filtrer par action de rôle'
		)
	);

	echo $this->Allocataires->blocPagination( array( 'prefix' => 'Search', 'options' => $options ) );
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

		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);

		echo $this->Default3->index(
			$results,
			$this->Translator->normalize(
					array(
					'Categorieactionrole.name',
					'Role.name',
					'Role.actif' => array( 'type' => 'boolean' ),
					'Actionrole.name',
					'Actionrole.description',
					'/Actionroles/edit/#Actionrole.id#' => array(
						'title' => true
					),
					'/Actionroles/delete/#Actionrole.id#' => array(
						'title' => true,
						'confirm' => true,
						'disabled' => 'true == "#Actionrole.has_linkedrecords#"'
					)
				)
			),
			array(
				'options' => $options,
				'format' => $this->element( 'pagination_format', array( 'modelName' => 'Actionrole' ) )
			)
		);
	}

	echo $this->Default3->actions( array( '/Parametrages/index' => array( 'class' => 'back' ) ) );
?>