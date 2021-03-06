<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default3->titleForLayout();

	$searchFormId = 'ConfigurationIndexForm';
 	$actions =  array(
		'/Configurations/index/#toggleform' => array(
			'title' => 'Visibilité formulaire',
			'text' => 'Formulaire',
			'class' => 'search',
			'onclick' => "$( '{$searchFormId}' ).toggle(); return false;"
		)
	);
	echo $this->Default3->actions( $actions );

	echo $this->Form->create( null, array(
		'type' => 'post',
		'url' => array(
			'controller' => $this->request->params['controller'],
			'action' => $this->request->action ),
			'id' => $searchFormId,
			'novalidate' => true
			)
		);

	echo $this->Default3->subform(
		$this->Translator->normalize(
				array(
					'Search.Configuration.search' => array( 'type' => 'hidden', 'value' => true ),
					'Configuration.lib_variable' => array( 'type' => 'text', 'required' => false ),
					'Configuration.value_variable' => array( 'type' => 'text', 'required' => false ),
					'Search.ConfigurationCategorie.lib_categorie' => array( 'empty' => true, 'required' => false )
				)
		),
		array(
					'options' => array( 'Search' => $options ),
					'fieldset' => true,
					'legend' => __m( 'Search.Configuration.Categorie' )
		)
	);

?>
	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php
	echo $this->Form->end();

	if( isset( $results ) ) {
		echo $this->Default3->index(
			$results,
			$this->Translator->normalize(
				array(
					'ConfigurationCategorie.lib_categorie',
					'Configuration.lib_variable',
					'Configuration.value_variable' => array('style' => 'max-width: 25vw; overflow: hidden; text-overflow: ellipsis; '),
					'Configuration.comments_variable' => array( 'style' => 'max-width: 25vw; overflow: hidden; text-overflow: ellipsis; '),
					'Configuration.modified' => array('type' => 'date'),
					'/configurations/edit/#Configuration.id#' => array(
						'title' => false
					),
				)
			),
			array(
				'options' => $options,
				'paginate' => false
			)
		);
	}

?>