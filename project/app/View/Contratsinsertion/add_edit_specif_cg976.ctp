<?php
	$personne = Hash::extract( $dossierMenu, "Foyer.Personne.{n}[id={$dossierMenu['personne_id']}]" );
	$personne = array( 'Personne' => $personne[0] );

	echo $this->Default3->titleForLayout( $personne );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	$structurereferente_id = suffix( Hash::get( $this->request->data, 'Contratinsertion.structurereferente_id' ) );
	$referent_id = suffix( Hash::get( $this->request->data, 'Contratinsertion.referent_id' ) );
	if( !empty( $structurereferente_id ) && !empty( $referent_id ) ) {
		$this->request->data['Contratinsertion']['referent_id'] = "{$structurereferente_id}_{$referent_id}";
	}

	echo $this->Default3->DefaultForm->create();

	echo $this->Default3->subform(
		[
			'Contratinsertion.id',
			'Contratinsertion.personne_id' => array( 'type' => 'hidden', 'value' => $dossierMenu['personne_id'] ),
			'Contratinsertion.structurereferente_id' => array( 'empty' => true ),
			'Contratinsertion.referent_id' => array( 'empty' => true ),
			'Contratinsertion.non_respect',
			'Contratinsertion.cause_non_respect'
		],
		[
			'options' => [
				'Contratinsertion' => [
					'structurereferente_id' => $structures,
					'referent_id' => $referents,
				]
			]
		]
	);

	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __m("contrat.dejabeneficie") )
		.$this->Default3->subform(
			[
				'Contratinsertion.type_contrat_travail' => ['empty' => true, 'required' => false, 'type' => 'select' ],
				'Contratinsertion.temps_contrat_travail' => ['empty' => true, 'required' => false, 'type' => 'select' ],
				'Contratinsertion.nb_heures_contrat_travail' => ['empty' => true, 'required' => false, 'type' => 'text' ],
				'Contratinsertion.dd_contrat_travail' => ['empty' => true, 'required' => false, 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 5, 'maxYear' => date( 'Y' ) + 5 ],
				'Contratinsertion.df_contrat_travail' => ['empty' => true, 'required' => false, 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 5, 'maxYear' => date( 'Y' ) + 5 ],
			],
			[
				'options' => [
					'Contratinsertion' => [
						'type_contrat_travail' => $typescontrattravail,
						'temps_contrat_travail' => $tempscontrattravail
					]
				]
			]
		)
	);


	echo "<fieldset><legend>".__m('Contratinsertion.themes')."</legend>";

		$i = 0;

		echo '<ul class="liste_sujets">';
		foreach( $sujetsCER as $idSujet => $nameSujet ) {
			if(isset($this->request->data['Sujetcer'])){
				$array_key = array_search( $idSujet, array_column(
					array_column(
						$this->request->data['Sujetcer'],
						'ContratinsertionSujetcer'
					),
					'sujetcer_id'
				) );
			} else {
				$array_key = false;
			}
			$checked = ( ( $array_key !== false ) ? 'checked' : '' );
			$soussujetcer_id = ( $array_key !== false ) ? $this->request->data['Sujetcer'][$array_key]['ContratinsertionSujetcer']['soussujetcer_id'] : '';
			$valeurparsoussujetcer_id =  ( $array_key !== false ) ? $soussujetcer_id.'_'.$this->request->data['Sujetcer'][$array_key]['ContratinsertionSujetcer']['valeurparsoussujetcer_id'] : '';
			$commentaire = ( $array_key !== false ) ? $this->request->data['Sujetcer'][$array_key]['ContratinsertionSujetcer']['commentaire'] : '';
			$champtexte = array_column(
				array_column(
					$sujetsCERComplets,
					'Sujetcer'
				),
				'champtexte',
				'id'
			)[$idSujet];


			// Niveau 1
			echo '<li>';

			$domPath = "themes.Sujetcer.{$idSujet}.sujetcer_id";

			echo $this->Xform->input( $domPath, array( 'name' => "data[themes][Sujetcer][{$i}][sujetcer_id]", 'label' => $nameSujet, 'type' => 'checkbox', 'value' => $idSujet, 'hiddenField' => false, 'checked' => $checked ) );

			// Niveau 2
			if( !empty( $soussujetsCER[$idSujet] ) ) {
				echo '<ul><li>';

				$domPath = "themes.Sujetcer.{$idSujet}.soussujetcer_id";

				echo $this->Xform->input( $domPath, array( 'name' => "data[themes][Sujetcer][{$i}][soussujetcer_id]", 'label' => false, 'type' => 'select', 'options' => $soussujetsCER[$idSujet], 'empty' => true, 'value' => $soussujetcer_id ) );

				// Niveau 3
				if( !empty( $valeursparsoussujetsCER[$idSujet] ) ) {
					$correspondChilParent[$this->Html->domId( "themes.Sujetcer.{$idSujet}.valeurparsoussujetcer_id" )] = $this->Html->domId( "themes.Sujetcer.{$idSujet}.soussujetcer_id" );
					echo '<ul><li>'; // Niveau 3
					echo $this->Xform->input( "themes.Sujetcer.{$idSujet}.valeurparsoussujetcer_id", array( 'name' => "data[themes][Sujetcer][{$i}][valeurparsoussujetcer_id]", 'label' => false, 'type' => 'select', 'options' => $valeursparsoussujetsCER[$idSujet], 'empty' => true, 'value' => $valeurparsoussujetcer_id ) );
					echo '</li></ul>'; // Niveau 3
					echo $this->Observer->dependantSelect(
						["themesSujetcer{$idSujet}SoussujetcerId" => "themesSujetcer{$idSujet}ValeurparsoussujetcerId"]
					);
				}
				echo '</li></ul>'; // Niveau 2
			}
			if($champtexte){
				echo $this->Xform->input( "themes.Sujetcer.{$idSujet}.commentaire", array( 'name' => "data[themes][Sujetcer][{$i}][commentaire]", 'label' => false, 'type' => 'text', 'value' => $commentaire) );
			}

			echo '</li>'; // Niveau 1
			$i++;
		}
		echo '<br><br>';
		echo $this->Default3->subform(['Contratinsertion.descriptionaction' => array( 'empty' => true )]);

		echo '</ul>';

	echo "</fieldset>";


	echo $this->Default3->subform(
		[
			'Contratinsertion.nature_projet',
			'Contratinsertion.observ_ci',
			'Contratinsertion.observ_benef' => ['label' => __m("Contratinsertion.bilan")],
			'Contratinsertion.action_conclusion' => ['type' => 'radio'],
			'Contratinsertion.duree_engag' => array( 'empty' => true ),
			'Contratinsertion.dd_ci' => array( 'empty' => true, 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 5, 'maxYear' => date( 'Y' ) + 5  ),
			'Contratinsertion.df_ci' => array( 'empty' => true, 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 5, 'maxYear' => date( 'Y' ) + 5  ),
			'Contratinsertion.lieu_saisi_ci',
			'Contratinsertion.date_saisi_ci' => array( 'empty' => true, 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 5, 'maxYear' => date( 'Y' ) + 5  ),
		],
		[
			'options' => [
				'Contratinsertion' => [
					'duree_engag' => $duree_engag,
					'decision_ci' => $decision_ci,
					'action_conclusion' => $actions_conclusion
				]
			]
		]
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();


	echo $this->Observer->dependantSelect(
		array(
			'Contratinsertion.structurereferente_id' => 'Contratinsertion.referent_id'
		)
	);

?>
<script type="text/javascript">
	function checkDatesToRefresh() {
		if( ( $F( 'ContratinsertionDdCiMonth' ) ) && ( $F( 'ContratinsertionDdCiYear' ) ) && ( $F( 'ContratinsertionDureeEngag' ) ) ) {
			setDateIntervalCer( 'ContratinsertionDdCi', 'ContratinsertionDfCi', $F( 'ContratinsertionDureeEngag' ), false );
		}
	}

	function affichageChampCause() {
		if( $( 'ContratinsertionNonRespect' ).checked == true) {
			$( 'ContratinsertionCauseNonRespect' ).parentNode.hidden = false;
		} else {
			$( 'ContratinsertionCauseNonRespect' ).parentNode.hidden = true;
		}
	}

	function affichageChampNbHeures() {
		if( $( 'ContratinsertionTempsContratTravail' ).options[$( 'ContratinsertionTempsContratTravail' ).selectedIndex].text == 'Temps partiel') {
			$( 'ContratinsertionNbHeuresContratTravail' ).disabled = false;
		} else {
			$( 'ContratinsertionNbHeuresContratTravail' ).disabled = true;
		}
	}

	document.observe( "dom:loaded", function() {
		if( $( 'ContratinsertionNonRespect' ).checked == true) {
			$( 'ContratinsertionCauseNonRespect' ).parentNode.hidden = false;
		} else {
			$( 'ContratinsertionCauseNonRespect' ).parentNode.hidden = true;
		}
		if( $( 'ContratinsertionTempsContratTravail' ).options[$( 'ContratinsertionTempsContratTravail' ).selectedIndex].text == 'Temps partiel') {
			$( 'ContratinsertionNbHeuresContratTravail' ).disabled = false;
		} else {
			$( 'ContratinsertionNbHeuresContratTravail' ).disabled = true;
		}
		Event.observe( $( 'ContratinsertionDdCiDay' ), 'change', function() {
			checkDatesToRefresh();
		} );
		Event.observe( $( 'ContratinsertionDdCiMonth' ), 'change', function() {
			checkDatesToRefresh();
		} );
		Event.observe( $( 'ContratinsertionDdCiYear' ), 'change', function() {
			checkDatesToRefresh();
		} );

		Event.observe( $( 'ContratinsertionDureeEngag' ), 'change', function() {
			checkDatesToRefresh();
		} );

		Event.observe( $( 'ContratinsertionNonRespect' ), 'change', function() {
			affichageChampCause();
		} );
		Event.observe( $( 'ContratinsertionTempsContratTravail' ), 'change', function() {
			affichageChampNbHeures();
		} );

		<?php foreach( array_keys( $sujetsCER ) as $key ) :?>
			observeDisableFieldsOnCheckbox(
				'themesSujetcer<?php echo $key;?>SujetcerId',
				['themesSujetcer<?php echo $key;?>SoussujetcerId', 'themesSujetcer<?php echo $key;?>ValeurparsoussujetcerId', 'themesSujetcer<?php echo $key;?>Commentaire', 'themesSujetcer<?php echo $key;?>Autrevaleur'],
				false,
				true
			);
		<?php endforeach;?>

	});
</script>

