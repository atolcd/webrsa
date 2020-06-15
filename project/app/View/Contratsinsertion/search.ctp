<?php $this->start( 'custom_search_filters' );?>
<?php
	$departement = Configure::read( 'Cg.departement' );
	$controller = $this->params->controller;
	$action = $this->action;
	$formId = ucfirst($controller) . ucfirst($action) . 'Form';
	$availableDomains = WebrsaTranslator::domains();
	$domain = isset( $availableDomains[0] ) ? $availableDomains[0] : $controller;
	$paramDate = array(
		'domain' => $domain,
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 4
	);
	$paramAllocataire = array(
		'options' => $options,
		'prefix' => 'Search',
	);
	$dateRule = array(
		'date' => array(
			'rule' => array('date'),
			'message' => null,
			'required' => null,
			'allowEmpty' => true,
			'on' => null
		)
	);

	$dates = array(
		'Dossier' => array('dtdemrsa' => $dateRule),
		'Personne' => array('dtnai' => $dateRule),
		'Contratinsertion' => array(
			'created' => $dateRule,
			'datevalidation_ci' => $dateRule,
			'dd_ci' => $dateRule,
			'df_ci' => $dateRule,
			'periode_validite' => $dateRule,
		)
	);

	echo $this->FormValidator->generateJavascript($dates, false);

	$paramDateCer = array(
		'minYear_from' => '2009',
		'maxYear_from' => 58 == $departement ? date( 'Y' ) + 3 : date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 4
	) + $paramDate;
	echo '<fieldset><legend>' . __m( 'Contratinsertion.search' ) . '</legend>'
		. $this->Default3->subform(
			array_merge(
				array(
					'Search.Contratinsertion.dernier' => array( 'type' => 'checkbox' ),
				),
				(
					( !in_array( $departement, array( 58, 976 ), true ) )
					? array(
						'Search.Contratinsertion.forme_ci' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __m('Search.Contratinsertion.forme_ci') )
					)
					: array()
				)
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		)
		. $this->SearchForm->dateRange( 'Search.Contratinsertion.created', $paramDate )
		. $this->Allocataires->communautesr( 'Contratinsertion', array( 'options' => array( 'Search' => $options ), 'hide' => false ) )
		. $this->Default3->subform(
			array_merge(
				array(
					'Search.Contratinsertion.structurereferente_id' => array( 'empty' => true ),
					'Search.Contratinsertion.referent_id' => array( 'empty' => true ),
				),
				(
					$departement == 66
					? array(
						'Search.Dernierreferent.recherche' => array('name' => false, 'before' => '<hr>'),
						'Search.Dernierreferent.dernierreferent_id' => array('empty' => true, 'after' => '<hr>'),
					)
					: array()
				),
				(
					$departement == 93
					? array(
						'Search.Cer93.positioncer' => array( 'empty' => true, 'required' => false ),
					)
					: array(
						'Search.Contratinsertion.decision_ci' => array( 'empty' => true )
					)
				),
				(
					$departement == 66
					? array(
						'Search.Contratinsertion.positioncer' => array( 'empty' => true ),
						'Search.Contratinsertion.num_contrat_66' => array( 'empty' => true ),
					)
					: array()
				),
				array(
					'Search.Contratinsertion.duree_engag' => array( 'empty' => true, 'type' => ( $departement === 58 ? 'text' : 'select' ) ),
				)
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		);

		if( $departement == 93 ) {
			// 1. Partie "Expériences professionnelles significatives"
			echo $this->Html->tag(
				'fieldset',
				$this->Html->tag( 'legend', __m( 'Expprocer93Expprocer93' ) )
				.$this->Romev3->fieldset( 'Expprocer93', array( 'options' => array( 'Expprocer93' => $options['Catalogueromev3'] ), 'multi_domain_translator' => true, 'prefix' => 'Search' ) )
				.$this->Html->tag(
					'fieldset',
					$this->Html->tag( 'legend', __m( 'Expprocer93Insee' ) )
					.$this->Xform->input( 'Search.Expprocer93.secteuracti_id', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Expprocer93.secteuracti_id' ), 'empty' => true, 'label' => __m( 'Search.Expprocer93.secteuracti_id' ) ) )
					.$this->Xform->input( 'Search.Expprocer93.metierexerce_id', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Expprocer93.metierexerce_id' ), 'empty' => true, 'label' => __m( 'Search.Expprocer93.metierexerce_id' ) ) )
				)
			);

			// 2. Partie "Emploi trouvé"
			echo $this->Html->tag(
				'fieldset',
				$this->Html->tag( 'legend', __m( 'Emptrouvromev3Emptrouvromev3' ) )
				.$this->Romev3->fieldset( 'Emptrouvromev3', array( 'options' => array( 'Emptrouvromev3' => $options['Catalogueromev3'] ), 'domain' => 'contratsinsertion_search_cg93', 'prefix' => 'Search', 'multi_domain_translator' => true  ) )
				.$this->Html->tag(
					'fieldset',
					$this->Html->tag( 'legend', __m( 'Emptrouvromev3Insee' ) )
					.$this->Xform->input( 'Search.Cer93.secteuracti_id', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Expprocer93.secteuracti_id' ), 'empty' => true, 'label' => __m( 'Search.Cer93.secteuracti_id' ) ) )
					.$this->Xform->input( 'Search.Cer93.metierexerce_id', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Expprocer93.metierexerce_id' ), 'empty' => true, 'label' => __m( 'Search.Cer93.metierexerce_id' ) ) )
				)
			);

			echo $this->Html->tag(
				'fieldset',
				$this->Html->tag( 'legend', 'Filtrer par ce sur quoi le contrat porte' )
				.$this->Form->input( 'Search.Cer93Sujetcer93.sujetcer93_id', array( 'label' => 'Sujet du CER', 'type' => 'select', 'options' => (array)Hash::get( $options, 'Cer93Sujetcer93.sujetcer93_id' ), 'empty' => true ) )
				.$this->Form->input( 'Search.Cer93Sujetcer93.soussujetcer93_id', array( 'label' => 'Sous sujet du CER', 'type' => 'select', 'options' => (array)Hash::get( $options, 'Cer93Sujetcer93.soussujetcer93_id' ), 'empty' => true ) )
				.$this->Form->input( 'Search.Cer93Sujetcer93.valeurparsoussujetcer93_id', array( 'label' => 'Valeur par sous sujet du CER', 'type' => 'select', 'options' => (array)Hash::get( $options, 'Cer93Sujetcer93.valeurparsoussujetcer93_id' ), 'empty' => true ) )
				.$this->Romev3->fieldset( 'Sujetromev3', array( 'options' => array( 'Sujetromev3' => $options['Catalogueromev3'] ), 'prefix' => 'Search' ) )
			);

			// Activation / désactivation de la partie "Votre contrat porte sur l'emploi (ROME v.3)" en fonciton des réponses à "Votre contrat porte sur"
			$activationPath = Configure::read( 'Cer93.Sujetcer93.Romev3.path' );
			$activationValues = (array)Configure::read( 'Cer93.Sujetcer93.Romev3.values' );

			$activationSujetcer93 = ( 'Sujetcer93.Sujetcer93.{n}.sujetcer93_id' === $activationPath );
			$activationSoussujetcer93 = ( 'Sujetcer93.Sujetcer93.{n}.soussujetcer93_id' === $activationPath );
			$activationIds = array();

			if( $activationSujetcer93 ) {
				$master = 'Search.Cer93Sujetcer93.sujetcer93_id';
				$activationIds = $activationValues;
			}
			else if( $activationSoussujetcer93 ) {
				$master = 'Search.Cer93Sujetcer93.soussujetcer93_id';
				foreach( array_keys( $options['Cer93Sujetcer93']['soussujetcer93_id'] ) as $soussujetcer93_id ) {
					if( in_array( suffix( $soussujetcer93_id ), $activationValues ) ) {
						$activationIds[] = $soussujetcer93_id;
					}
				}
			}

			if( $activationSujetcer93 || $activationSoussujetcer93 ) {
				echo $this->Observer->disableFieldsetOnValue(
					$master,
					'SearchSujetromev3FieldsetId',
					$activationIds,
					false,
					true
				);
			}
		}

		echo $this->SearchForm->dateRange( 'Search.Contratinsertion.datevalidation_ci', $paramDate )
		. $this->SearchForm->dateRange( 'Search.Contratinsertion.dd_ci', $paramDateCer )
		. $this->SearchForm->dateRange( 'Search.Contratinsertion.df_ci', $paramDateCer )
		. $this->SearchForm->dateRange( 'Search.Contratinsertion.periode_validite', $paramDateCer )
		. $this->Default3->subform(
			array_merge(
				array(
					'Search.Contratinsertion.arriveaecheance' => array( 'type' => 'checkbox' ),
					'Search.Contratinsertion.echeanceproche' => array(
						'type' => 'checkbox',
						'label' => sprintf(
							__m( 'Search.Contratinsertion.echeanceproche' ),
							localized_interval( Configure::read( 'Criterecer.delaiavanteecheance' ), array( 'precision' => 'd' ) )
						)
					),
				),
				(
					$departement == 66
					? array(
						'Search.Contratinsertion.istacitereconductionNotNull' => array( 'type' => 'checkbox' ),
						'Search.Contratinsertion.istacitereconduction' => array( 'type' => 'checkbox' )
					)
					: array()
				)
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		)
		. '</fieldset>'
	;

	echo '<fieldset><legend>' . __m( 'Contratinsertion.orientation' ) . '</legend>'
		. $this->Default3->subform(
			array_merge(
				(
					$departement == 58
					? array(
						'Search.Personne.etat_dossier_orientation' => array( 'type' => 'select', 'empty' => true ),
					)
					: array()
				),
				array(
					'Search.Orientstruct.typeorient_id' => array( 'type' => 'select', 'empty' => true ),
				),
				(
					$departement == 66
					? array(
						'Search.Orientstruct.not_typeorient_id' => array( 'type' => 'select', 'multiple' => 'checkbox' ),
					)
					: array()
				)
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		)
		. '</fieldset>'
	;
?>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);
?>
<?php
	if( $departement == 93 ) {
		echo $this->Observer->dependantSelect(
			array(
				'Search.Cer93Sujetcer93.sujetcer93_id' => 'Search.Cer93Sujetcer93.soussujetcer93_id',
				'Search.Cer93Sujetcer93.soussujetcer93_id' => 'Search.Cer93Sujetcer93.valeurparsoussujetcer93_id',
			)
		);
	}
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php if( $departement == 58 ): ?>
			new MaskedInput( '#SearchContratinsertionDureeEngag', '9?9' );
		<?php endif;?>

		<?php if( $departement == 66 ): ?>
		/**
		 * Remplissage auto Dernierreferent
		 *
		 * @see View/Referents/add_edit.ctp
		 */
		var index = [];

		function format_approchant(text) {
			return text.toLowerCase().replace(/[àâä]/g, 'a').replace(/[éèêë]/g, 'e')
					.replace(/[ïî]/g, 'i').replace(/[ôö]/g, 'o').replace(/[ùüû]/g, 'u').replace('-', ' ');
		}

		$$('#SearchDernierreferentDernierreferentId option').each(function(option){
			index.push({
				value: option.getAttribute('value'),
				textlo: format_approchant(option.innerHTML),
				text: option.innerHTML
			});
		});

		$('SearchDernierreferentRecherche').observe('keypress', function(event){
			'use strict';
			var value = $('SearchDernierreferentRecherche').getValue(),
				regex = /^[a-zA-Z éèï\-ç]$/,
				i,
				newValue = ''
			;

			// Ajoute à la valeur du champ, la "lettre" utilisé
			if (regex.test(event.key)) {
				value += event.key;
			} else if (event.key === 'Backspace') {
				value = value.substr(0, value.length -1);
			}

			// Recherche la valeur à selectionner
			for (i=0; i<index.length; i++) {
				if (index[i].text.indexOf(value) >= 0) {
					newValue = index[i].value;
					break;
				} else if (index[i].textlo.toLowerCase().indexOf(format_approchant(value)) >= 0) {
					newValue = index[i].value;
				}
			}

			// Set de la valeur
			$('SearchDernierreferentDernierreferentId').setValue(newValue);
		});
		<?php endif;?>

		dependantSelect( 'SearchContratinsertionReferentId', 'SearchContratinsertionStructurereferenteId' );
	});
</script>