<?php
	$this->pageTitle = 'Traitements des PDOs';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
	$domain = current(WebrsaTranslator::domains());
	$defaultParams = compact('options', 'domain');
?>
<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'traitementpcg66', "Traitementspcgs66::{$this->action}" ).' '.$nompersonne
	);

	echo $this->Default2->search(
		array(
			'Personnepcg66.dossierpcg66_id' => array( 'domain' => 'traitementpcg66', 'value' => $dossierpcgId )
		),
		array(
			'options' => $searchOptions
		)
	);

	$addLink = $this->Default3->actions(
		WebrsaAccess::actionAdd("/Traitementspcgs66/add/{$personnepcg66_id}", $ajoutPossible)
	);

	if( empty( $dossierpcg66_id ) ){
		echo '<p class="notice"> Veuillez sélectionner un dossier afin d\'afficher les traitements</p>';
	}
	else if( empty( $listeTraitements ) ) {
		echo $addLink;
		echo '<p class="notice"> Aucun traitement présent pour ce dossier</p>';
	}
	else{
		$pagination = $this->Xpaginator2->paginationBlock( 'Traitementpcg66', Set::merge( $this->request->params['pass'], $this->request->params['named'] ) );

		foreach ( $listeTraitements as $key => $value ) {
			$listeTraitements[$key]['Traitementpcg66']['imprimer'] = (boolean)Hash::get($value, 'Traitementpcg66.imprimer');
		}
		unset($options['Traitementpcg66']['imprimer']);

		$this->Default3->DefaultPaginator->options(
			array( 'url' => $this->request->params['pass'] )
		);

		echo $addLink;

		echo $this->Default3->index(
			$listeTraitements,
			$this->Translator->normalize(
				array(
					'Situationpdo.libelle',
					'Descriptionpdo.name',
					'Traitementpcg66.datedepart',
					'Traitementpcg66.datereception',
					'Traitementpcg66.daterevision',
					'Traitementpcg66.dateecheance',
					'Traitementpcg66.typetraitement',
					'Traitementpcg66.imprimer' => array( 'type' => 'boolean' ),
					'Traitementpcg66.dateenvoicourrier',
					'Traitementpcg66.etattraitementpcg',
					'Traitementpcg66.created',
				) + WebrsaAccess::links(
					array(
						'/Traitementspcgs66/view/#Traitementpcg66.id#',
						'/Traitementspcgs66/edit/#Traitementpcg66.id#',
						'/Traitementspcgs66/printFicheCalcul/#Traitementpcg66.id#' => array('class' => 'print'),
						'/Traitementspcgs66/switch_imprimer/#Traitementpcg66.id#' => array(
							'class' => 'boolean enabled',
							'condition' => "'#Traitementpcg66.typetraitement#' === 'courrier'",
							'condition_group' => 'typetraitement'
						),
						'/False/switch_imprimer' => array(
							'msgid' => '',
							'condition' => "'#Traitementpcg66.typetraitement#' !== 'courrier'",
							'condition_group' => 'typetraitement'
						),
						'/Traitementspcgs66/printModeleCourrier/#Traitementpcg66.id#' => array('class' => 'print'),
						'/Traitementspcgs66/envoiCourrier/#Traitementpcg66.id#' => array('class' => 'email_send'),
						'/Traitementspcgs66/reverseDO/#Traitementpcg66.id#' => array(
							'class' => 'button',
							'condition' => "'#Traitementpcg66.reversedo#' !== '1'",
							'condition_group' => 'reversedo'
						),
						'/Traitementspcgs66/deverseDO/#Traitementpcg66.id#' => array(
							'class' => 'button',
							'condition' => "'#Traitementpcg66.reversedo#' === '1'",
							'condition_group' => 'reversedo'
						),
						'/Traitementspcgs66/clore/#Traitementpcg66.id#' => array('class' => 'button'),
						'/Traitementspcgs66/cancel/#Traitementpcg66.id#' => array(
							'condition' => "'#Traitementpcg66.annule#' !== 'O'",
							'condition_group' => 'annule'
						),
						'/Traitementspcgs66/canceled/#Traitementpcg66.id#' => array(
							'msgid' => 'Annulé',
							'class' => 'cancel',
							'condition' => "'#Traitementpcg66.annule#' === 'O'",
							'condition_group' => 'annule',
							'disabled' => true
						),
						'/Traitementspcgs66/delete/#Traitementpcg66.id#' => array('confirm' => true),
					)
				)
			),
			$defaultParams + array(
				'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, '
					. 'starting on record %start%, ending on %end%'),
				'innerTable' => $this->Translator->normalize(
					array(
						'Traitementpcg66.motifannulation' => array(
							'condition' => '"#Traitementpcg66.motifannulation#" !== ""'
						)
					)
				)
			)
		);
	}

	if( !empty( $personnepcg66 ) ){
		echo '<div class="aere">';
		echo $this->Default->button(
			'backpdo',
			array(
				'controller' => 'dossierspcgs66',
				'action'     => 'edit',
				$personnepcg66['Personnepcg66']['dossierpcg66_id']
			),
			array(
				'id' => 'Back',
				'label' => 'Retour au dossier'
			)
		);
		echo '</div>';
	}


?>
<script>
	$('TableTraitementspcgs66Index').select('td.boolean').each(function(td) {
		var addClassName = 'true', tdAction;
		if ( td.hasClassName( 'true' ) ) {
			addClassName = 'false';
		}

		tdAction = td.up('tr').select('td.action>a.boolean.enabled');
		if ( tdAction.length ) {
			tdAction.first().addClassName(addClassName);
		}
	});
</script>