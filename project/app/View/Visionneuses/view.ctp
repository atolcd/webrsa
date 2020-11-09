<?php
	echo $this->Default3->actions( array( '/Visionneuses/index' => array( 'class' => 'back' ) ) );

	$this->title = __m('Visionneuses::view::title', $nomFlux);
	echo '<h1>' . $this->title . '</h1>';
	// Création du formulaire de recherche
	$searchFormId = 'TalensyntIndexForm';
	$actions =  array(
		'/Visionneuses/view/#toggleform' => array(
			'title' => __m('Visionneuse::form::info'),
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
			'action' => $this->request->action,
			$identificationflux_id
			),
		'id' => $searchFormId,
		'novalidate' => true,
		)
	);

	echo $this->SearchForm->dependantCheckboxes(
		'Search.Talensynt',
		array('options' => $options, 'hide' => TRUE, 'domain' => 'visionneuses'  )
	);

	?>
	<div class="submit noprint">
		<?php echo $this->Form->button( __d('default', 'Search'), array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( __d('default', 'Reset'), array( 'type' => 'reset' ) );?>
	</div>
	<?php
	echo $this->Form->end();
    // Fin de la recherche

    if(empty($results) ) {
        echo $this->Xhtml->tag( 'p', __m('Talendsynt::index::empty'), array( 'class' => 'notice' ) );
    } else {
        //$pagination = $this->Xpaginator->paginationBlock( 'Talendsynt', $this->passedArgs );
        $this->Paginator->options(array(
            'url' => array(
                'pass' => $this->passedArgs[0]
            )
        ));
        echo $this->Default3->index(
            $results,
            $this->Translator->normalize(
                array(
                    'Talendsynt.qual',
                    'Talendsynt.nomnai',
                    'Talendsynt.nom',
                    'Talendsynt.prenom',
                    'Talendsynt.dtnai',
                    'Talendsynt.nir',
                    'Talendsynt.sexe',
                    'Talendsynt.cree'/*  => array('type' => 'string') */,
                    'Talendsynt.maj'/*  => array('type' => 'string') */,
                    'Talendsynt.rejet'/*  => array('type' => 'string') */,
                )
            ),
            array(
                'paginate' => false,
            )
        );
	}
	echo $this->Default3->actions( array( '/Visionneuses/index' => array( 'class' => 'back' ) ) );
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		document.querySelector("#<?php echo $searchFormId ?>").style.display = 'none';
	});
</script>