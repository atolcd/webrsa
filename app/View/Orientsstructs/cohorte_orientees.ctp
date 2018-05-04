<?php
	// Conditions d'accès aux tags
	$departement = (int)Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );
	$utilisateursAutorises = (array)Configure::read( 'acces.recherche.tag' );
	$viewTag = false;

	foreach ($utilisateursAutorises as $utilisateurAutorise) {
		if ($utilisateurAutorise == $user_type) {
			$viewTag = true;
			break;
		}
	}

	if ($departement != 93) {
		$viewTag = true;
	}
	// Conditions d'accès aux tags

	// Conditions d'accès aux origines d'orientation prestataires
	$utilisateursAutorises = (array)Configure::read( 'acces.origine.orientation.prestataire' );
	$viewOriginePresta = false;

	foreach ($utilisateursAutorises as $utilisateurAutorise) {
		if ($utilisateurAutorise == $user_type) {
			$viewOriginePresta = true;
			break;
		}
	}

	if ($departement == 93 && $viewOriginePresta == false) {
	    foreach ($options['Orientstruct']['origine'] as $key => $value) {
	        if (preg_match('|^presta|', $key)) {
	            unset ($options['Orientstruct']['origine'][$key]);
	        }
	    }
	}
	// Conditions d'accès aux origines d'orientation prestataires

	$this->start( 'custom_search_filters' );

	$paramDate = array(
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 1
	);
?>
<fieldset>
	<legend>Orientation</legend>
	<?php
		echo $this->SearchForm->dateRange( 'Search.Orientstruct.date_valid', $paramDate + array( 'legend' => __m( 'Search.Orientstruct.date_valid' ) ) );
	?>
	<fieldset>
		<legend>Imprimé/Non imprimé</legend>
		<?php
			echo $this->Default3->subform(
				array(
					'Search.Orientstruct.impression' => array( 'empty' => true ),
				),
				array( 'options' => array( 'Search' => $options ) )
			);
			echo $this->SearchForm->dateRange( 'Search.Orientstruct.date_impression', $paramDate + array( 'legend' => __m( 'Search.Orientstruct.date_impression' ) ) );
		?>
	</fieldset>
	<?php
		echo $this->Default3->subform(
			array(
				'Search.Orientstruct.typeorient_id' => array( 'empty' => true, 'required' => false ),
				'Search.Orientstruct.origine' => array( 'empty' => true ),
			),
			array( 'options' => array( 'Search' => $options ) )
		);
	?>
</fieldset>
<?php
	$this->end();
?>

<?php
	$buttons = '<ul class="actionMenu">
		<li>'.$this->Xhtml->printCohorteLink(
				'Imprimer la cohorte',
				Hash::merge(
					array(
						'controller' => 'orientsstructs',
						'action'     => 'cohorte_impressions',
						'id' => 'Cohorteoriente'
					),
					Hash::flatten( $this->request->data, '__' )
				)
			).'</li>
		</ul>';

	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'modelName' => 'Personne',
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => false,
			'afterResults' => $buttons
		)
	);
?>