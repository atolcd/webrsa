<?php
	echo $this->Default3->titleForLayout();

	// Création du formulaire de recherche
	$searchFormId = 'VisionneuseIndexForm';

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

	$actions = array(
		'/Visionneuses/index/#toggleform' => array(
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
			'action' => $this->request->action ),
			'id' => $searchFormId,
			'novalidate' => true
			)
		);

	echo $this->Default3->subform(
		$this->Translator->normalize(
				array(
					'Search.Visionneuse.search' => array( 'type' => 'hidden', 'value' => true ),
					'Search.Visionneuse.flux' => array( 'empty' => true, 'required' => false ),
				)
		),
		array(
					'options' => array( 'Search' => $options ),
					'fieldset' => true,
					'legend' => __m( 'Search.Visionneuse' )
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

	if( empty( $visionneuses ) ) {
		echo $this->Xhtml->tag( 'p', __m('Visionneuse::index::empty'), array( 'class' => 'notice' ) );
	}
	else {
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

?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		document.querySelector("#<?php echo $searchFormId ?>").style.display = 'none';
	});
</script>