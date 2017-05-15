<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$searchFormId = Inflector::camelize( "{$this->request->params['controller']}_{$this->request->params['action']}_form" );
	$params = array(
		'domain' => 'covs58',
		'minYear_from' => 2009,
		'minYear_to' => 2009,
		'maxYear_from' => date( 'Y' ),
		'maxYear_to' => date( 'Y' ) + 1,
	);

	echo $this->Default3->titleForLayout();
?>
<ul class="actionMenu">
	<?php
		echo '<li>'.$this->Xhtml->addLink(
			'Ajouter',
			array( 'controller' => 'covs58', 'action' => 'add' ),
			$this->Permissions->check( 'covs58', 'add' )
		).' </li>';
	?>
</ul>
<?php
	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( '{$searchFormId}' ).toggle(); return false;" )
	).'</li></ul>';

	echo $this->Form->create( null, array( 'type' => 'post', 'url' => array( 'controller' => $this->request->params['controller'], 'action' => $this->request->action ), 'id' => $searchFormId, 'class' => ( isset( $results ) ? 'folded' : 'unfolded' ) ) );
	echo $this->Default3->subform(
		array(
			'Search.Cov58.sitecov58_id' => array( 'empty' => true, 'required' => false ),
			'Search.Cov58.etatcov' => array( 'empty' => true, 'required' => false ),
		),
		array( 'options' => array( 'Search' => $options ) )
	) ;
	echo $this->SearchForm->dateRange( 'Search.Cov58.datecommission', $params );
?>
	<div class="submit noprint">
		<?php
			echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );
			echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );
		?>
	</div>
<?php
	echo $this->Form->end();
	echo $this->Observer->disableFormOnSubmit( $searchFormId );

	if( isset( $results ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		$etatsActions = var_export( $etatsActions, true );

		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);

		echo $this->Default3->index(
			$results,
			array(
                'Sitecov58.name',
				'Cov58.datecommission',
				'Cov58.etatcov',
				'Cov58.observation',
				'/Covs58/view/#Cov58.id#' => array(
					'title' => false
				),
				'/Covs58/edit/#Cov58.id#' => array(
					'title' => false,
					'disabled' => "!in_array( 'covs58::edit', Hash::get( {$etatsActions}, '#Cov58.etatcov#' ) )"
				),
				'/Covs58/ordredujour/#Cov58.id#' => array(
					'title' => false,
					'class' => 'impression',
					'disabled' => "!in_array( 'covs58::ordredujour', Hash::get( {$etatsActions}, '#Cov58.etatcov#' ) )"
				),
				'/Covs58/decisioncov/#Cov58.id#' => array(
					'title' => false,
					'class' => 'edit',
					'disabled' => "!in_array( 'covs58::decisioncov', Hash::get( {$etatsActions}, '#Cov58.etatcov#' ) )"
				),
				'/Covs58/visualisationdecisions/#Cov58.id#' => array(
					'title' => false,
					'class' => 'view',
					'disabled' => "!in_array( 'covs58::visualisationdecisions', Hash::get( {$etatsActions}, '#Cov58.etatcov#' ) )"
				),
				'/Covs58/impressionpv/#Cov58.id#' => array(
					'title' => false,
					'class' => 'impression',
					'disabled' => "!in_array( 'covs58::impressionpv', Hash::get( {$etatsActions}, '#Cov58.etatcov#' ) )"
				)
			),
			array(
				'options' => $options
			)
		);
	}
?>