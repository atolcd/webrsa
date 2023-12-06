

<?php
	echo $this->Default3->titleForLayout();

	// Création du formulaire de recherche
	$searchFormFluxId = 'IndexFluxForm';
	$searchFormPersonneId = 'IndexPersonneForm';

	echo $this->element('rapports_talend_menu', ['ali' => true]);

	// Gestion des formulaires de recherche
	// Recherche par flux
	$actions = array(
		'/rapportsechangesali/index/#toggleformflux' => array(
			'title' => __m('RapportEchangeALI::form::infoFlux'),
			'text' => __m('RapportEchangeALI::form::infoFlux'),
			'class' => 'search',
			'onclick' => "$( '{$searchFormFluxId}' ).toggle(); $( '{$searchFormPersonneId}' ).style.display = 'none'; return false;"
		),
		'/rapportsechangesali/index/#toggleformpersonne' => array(
			'title' => __m('RapportEchangeALI::form::infoPersonne'),
			'text' => __m('RapportEchangeALI::form::infoPersonne'),
			'class' => 'search',
			'onclick' => "$( '{$searchFormPersonneId}' ).toggle(); $( '{$searchFormFluxId}' ).style.display = 'none'; return false;"
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
					'Search.RapportEchangeALI.searchFlux' => array( 'type' => 'hidden', 'value' => true ),
					'Search.RapportEchangeALI.flux' => array( 'empty' => true, 'required' => false ),
				)
		),
		array(
					'options' => array( 'Search' => $options ),
					'fieldset' => true,
					'legend' => __m( 'Search.RapportEchangeALIFlux' )
		)
	);

	echo $this->SearchForm->dateRange( 'Search.RapportEchangeALI.date', array(
		'domain' => 'rapportsechangesali',
		'minYear_from' => 2022,
		'minYear_to' => 2022,
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


	//Recherche par personne
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
					'Search.RapportEchangeALI.searchPersonne' => array( 'type' => 'hidden', 'value' => true ),
					'Search.RapportEchangeALI.nom' => array( 'empty' => true, 'required' => false ),
					'Search.RapportEchangeALI.prenom' => array( 'empty' => true, 'required' => false ),
					'Search.RapportEchangeALI.dtnai' => array( 'empty' => true, 'required' => false, 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date('Y'), 'minYear' => date('Y') - 120 ),
					'Search.RapportEchangeALI.nir' => array( 'empty' => true, 'required' => false ),
				)
		),
		array(
					'options' => array( 'Search' => $options ),
					'fieldset' => true,
					'legend' => __m( 'Search.RapportEchangeALIPersonne' )
		)
	);

	?>
	<div class="submit noprint">
		<?php echo $this->Form->button( __d('default', 'Search'), array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( __d('default', 'Reset'), array( 'type' => 'reset' ) );?>
	</div>
	<?php
	echo $this->Form->end();
	// Fin de la recherche



	if(isset($Rapports)){

		$pagination = $this->Xpaginator->paginationBlock( 'RapportEchangeALI', $this->passedArgs );
		echo $pagination;

		echo $this->Default3->index(
			$Rapports,
			$this->Translator->normalize(
				array(
					'RapportEchangeALI.type',
					'Structurereferente.lib_struc',
					'RapportEchangeALI.periode',
					'RapportEchangeALI.nom_fichier',
					'RapportEchangeALI.debut',
					'RapportEchangeALI.created',
					'RapportEchangeALI.duree',
					'RapportEchangeALI.nb_pers',
					'RapportEchangeALI.statut',
					'/rapportsechangesali/details/#RapportEchangeALI.code#/#RapportEchangeALI.id#' => array(
						'title' => false, 'msgid' => "voir_details", 'target' => '_blank'
					),
					'/rapportsechangesali/details/#RapportEchangeALI.code#/#RapportEchangeALI.id#/true' => array(
						'title' => false, 'msgid' => "voir_erreurs", 'target' => '_blank'
					)
				)
			),
			array(
				'paginate' => false,
				'options' => $options
			)
		);
		echo $pagination;
	}

	if (isset($personnes)){

		$pagination = $this->Xpaginator->paginationBlock( 'PersonneEchangeALI', $this->passedArgs );
		echo $pagination;

		echo $this->Default3->index(
			$personnes,
			$this->Translator->normalize(
				array(
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'RapportEchangeALI.debut',
					'RapportEchangeALI.nom_fichier',
					'PersonneEchangeALI.referentparcours',
					'/personnes/view/#Personne.id#' => array(
						'title' => false, 'target' => '_blank'
					)
				)
			),
			array(
				'paginate' => false,
				'id' => 'TableRapportsechangesaliDetails'
			)
		);

		echo $pagination;
	}

?>

<script type="text/javascript">
	document.observe( "dom:loaded", function() {

		document.querySelector("#<?php echo $searchFormFluxId ?>").style.display = 'none';
		document.querySelector("#<?php echo $searchFormPersonneId ?>").style.display = 'none';

		var nameTdIndex = document.querySelector('#TableRapportsechangesaliIndexColumnRapportEchangeALIStatut').cellIndex + 1;
		document.querySelectorAll('#TableRapportsechangesaliIndex td:nth-child(' + nameTdIndex + ')').forEach(function(element) {
  			element.classList.add('boolean');
			if(element.textContent == 'Ok'){
				element.classList.add('true');
			} else {
				element.classList.add('false');
			}
		});

});
</script>