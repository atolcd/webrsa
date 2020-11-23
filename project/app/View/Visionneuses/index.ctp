<?php
	echo $this->Default3->titleForLayout();

	// Création du formulaire de recherche
	$searchFormFluxId = 'VisionneuseIndexFluxForm';
	$searchFormPersonneId = 'VisionneuseIndexPersonneForm';

	// Gestion des boutons pour voir les flux créances / CNAF
	$visionneusesLinkEnabled = false;
	$actions =  array(
		'/Visionneuses/index' => array(
			'title' => __m('Visionneuse::index::title'),
			'text' => __m('Visionneuse::index::link'),
			'class' => 'link',
			'enabled' => $visionneusesLinkEnabled
		),
		'/Rapportstalendscreances/index' => array(
			'title' => __m('Rapportstalendscreances::index::title'),
			'text' => __m('Rapportstalendscreances::index::link'),
			'class' => 'link',
			'enabled' => !$visionneusesLinkEnabled
		),
	);

	echo $this->Default3->actions( $actions );

	// Gestion des formulaires de recherche
	// Recherche par flux
	$actions = array(
		'/Visionneuses/index/#toggleformflux' => array(
			'title' => __m('Visionneuse::form::info'),
			'text' => __m('Visionneuse::form::infoFlux'),
			'class' => 'search',
			'onclick' => "$( '{$searchFormFluxId}' ).toggle(); return false;"
		),
		'/Visionneuses/index/#toggleformpersonne' => array(
			'title' => __m('Visionneuse::form::info'),
			'text' => __m('Visionneuse::form::infoPersonne'),
			'class' => 'search',
			'onclick' => "$( '{$searchFormPersonneId}' ).toggle(); return false;"
		)
	);

	echo $this->Default3->actions( $actions );

	echo $this->Form->create( null, array(
		'type' => 'post',
		'url' => array(
			'controller' => $this->request->params['controller'],
			'action' => $this->request->action ),
			'id' => $searchFormFluxId,
			'novalidate' => true
			)
		);

	echo $this->Default3->subform(
		$this->Translator->normalize(
				array(
					'Search.Visionneuse.searchFlux' => array( 'type' => 'hidden', 'value' => true ),
					'Search.Visionneuse.flux' => array( 'empty' => true, 'required' => false ),
				)
		),
		array(
					'options' => array( 'Search' => $options ),
					'fieldset' => true,
					'legend' => __m( 'Search.VisionneuseFlux' )
		)
	);

	echo $this->SearchForm->dateRange( 'Search.Visionneuse.dtdeb', array(
		'domain' => 'visionneuse',
		'minYear_from' => 2009,
		'minYear_to' => 2009,
		'maxYear_from' => date( 'Y' ) + 1,
		'maxYear_to' => date( 'Y' ) + 1,
	) );

	?>
	<div class="submit noprint">
		<?php echo $this->Form->button( __d('default', 'Search'), array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( __d('default', 'Reset'), array( 'type' => 'reset' ) );?>
	</div>
	<?php
	echo $this->Form->end();
	// Fin de la recherche

	// Recherche par personne
	echo $this->Form->create( null, array(
		'type' => 'post',
		'url' => array(
			'controller' => $this->request->params['controller'],
			'action' => $this->request->action ),
			'id' => $searchFormPersonneId,
			'novalidate' => true
			)
		);

	echo $this->Default3->subform(
		$this->Translator->normalize(
				array(
					'Search.Visionneuse.searchPersonne' => array( 'type' => 'hidden', 'value' => true ),
					'Search.Visionneuse.nom' => array( 'empty' => true, 'required' => false ),
					'Search.Visionneuse.prenom' => array( 'empty' => true, 'required' => false ),
					'Search.Visionneuse.dtnai' => array(
						'empty' => true,
						'required' => false,
						'type' => 'date',
						'dateFormat' => 'DMY',
						'minYear' => date('Y')-100,
						'maxYear' => date('Y')
					),
					'Search.Visionneuse.nir' => array( 'empty' => true, 'required' => false ),
				)
		),
		array(
					'options' => array( 'Search' => $options ),
					'fieldset' => true,
					'legend' => __m( 'Search.Visionneuse.Personne' )
		)
	);

	?>
	<div class="submit noprint">
		<?php echo $this->Form->button( __d('default', 'Search'), array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( __d('default', 'Reset'), array( 'type' => 'reset' ) );?>
	</div>
	<?php
	echo $this->Form->end();
	if( empty( $visionneuses ) ) {
		echo $this->Xhtml->tag( 'p', __m('Visionneuse::index::empty'), array( 'class' => 'notice' ) );
	}
	else if($isFlux) {
		// Ce sont des informations relatives aux flux
		echo $this->Default3->index(
			$visionneuses,
			$this->Translator->normalize(
				array(
					'Visionneuse.flux',
					'Visionneuse.nomfic',
					'Visionneuse.dtdeb',
					'Visionneuse.dtfin',
					'Visionneuse.duree' => array('sort' => false),
					'Visionneuse.dossier' => array('sort' => false),
					'Visionneuse.nbrejete' => array('type' => 'string'),
					'Visionneuse.nbinser' => array('type' => 'string'),
					'Visionneuse.nbmaj' => array('type' => 'string'),
					'Visionneuse.perscree' => array('type' => 'string'),
					'Visionneuse.persmaj' => array('type' => 'string'),
					'Visionneuse.dspcree' => array('type' => 'string'),
					'Visionneuse.dspmaj' => array('type' => 'string'),
					'/Visionneuses/view/#Visionneuse.identificationflux_id#' => array(
						'disabled' => '( \'#Visionneuse.identificationflux_id#\' == 0 )'
					),
				)
			),
			array(
				'paginate' => true,
			)
		);
	}
	else {
		// Ce sont des informations relatives aux personnes
		echo $this->Default3->index(
			$visionneuses,
			$this->Translator->normalize(
				array(
					'Talendsynt.qual',
					'Talendsynt.nomnai',
					'Talendsynt.nom',
					'Talendsynt.prenom',
					'Talendsynt.dtnai',
					'Talendsynt.nir',
					'Talendsynt.sexe',
					'Talendsynt.cree',
					'Talendsynt.maj',
					'Talendsynt.rejet',
					'Visionneuse.flux',
					'Visionneuse.nomfic',
					'/Visionneuses/view/#Talendsynt.identificationflux_id#' => array(
						'disabled' => '( \'#Talendsynt.identificationflux_id#\' == 0 )'
					),
				)
			),
			array(
				'paginate' => false,
			)
		);
	}

?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		document.querySelector("#<?php echo $searchFormFluxId ?>").style.display = 'none';
		document.querySelector("#<?php echo $searchFormPersonneId ?>").style.display = 'none';
	});

</script>