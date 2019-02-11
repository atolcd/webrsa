<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
		echo $this->Html->script( 'prototype.fabtabulous.js' );
		echo $this->Html->script( 'prototype.tablekit.js' );
		echo $this->Html->script( 'webrsa.custom.prototype.js' );
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	App::uses( 'WebrsaAccess', 'Utility' );
	App::uses( 'WebrsaPermissions', 'Utility' );

	class Accompagnementbeneficiaire93Permissions
	{
		protected static $_dossierMenu = null;

		public static function init( array $dossierMenu ) {
			self::$_dossierMenu = $dossierMenu;
		}

		public static function disabled( $controller, $action ) {
			$permission =
				( false === empty( self::$_dossierMenu )
					&& WebrsaPermissions::checkDossier( $controller, $action, self::$_dossierMenu )
				) ? 'true' : 'false';

			return "( \"#/{$controller}/{$action}#\" != true ) || ( {$permission} !== true )";
		}
	}

	WebrsaAccess::init( $dossierMenu );
	Accompagnementbeneficiaire93Permissions::init( $dossierMenu );

	$this->pageTitle = DefaultUtility::evaluateString( $details, 'Synthèse du suivi<br/>#Personne.qual# #Personne.nom# #Personne.prenom#' );
	echo $this->Html->tag( 'h1', $this->pageTitle );
	$this->pageTitle = str_replace( '<br/>', ' de ', $this->pageTitle );
?>
<div id="tabbedWrapper" class="tabs">
	<div id="accompagnement">
		<?php
			// Premier onglet, accompagnement
			echo $this->Html->tag( 'h2', 'Accompagnement', array( 'class' => 'title' ) );

			echo '<table class="noborder" style="width:100%;"><tr><td class="noborder" style="width: 50%;">';
			echo $this->Html->tag( 'h3', 'Droits' );
			echo $this->Default3->view(
				$details,
				array(
					'Dossier.dtdemrmi' => array(
						'label' => __m( 'Dossier.dtdemrmi' ),
					),
					'Dossier.dtdemrsa' => array(
						'label' => __m( 'Dossier.dtdemrsa' ),
					),
					'Detaildroitrsa.natpf' => array(
						'label' => __m( 'Detaildroitrsa.natpf' ),
						'value' => '#Detaildroitrsa.natpf,ucfirst#'
					),
					'Situationdossierrsa.etatdosrsa' => array(
						'label' => __m( 'Situationdossierrsa.etatdosrsa' ),
						'class' => 'situationdossierrsa etatdosrsa#Situationdossierrsa.etatdosrsa#',
					),
					'Cer93.cmu' => array(
						'label' => __m( 'Cer93.cmu' ),
						'class' => 'boolean #Cer93.cmu#',
					),
					'Cer93.cmuc' => array(
						'label' => __m( 'Cer93.cmuc' ),
						'class' => 'boolean #Cer93.cmuc#',
					),
					'Historiqueetatpe.identifiantpe' => array(
						'label' => __m( 'Historiqueetatpe.identifiantpe' ),
					),
					'Calculdroitrsa.toppersdrodevorsa' => array(
						'label' => __m( 'Calculdroitrsa.toppersdrodevorsa' ),
						'type' => 'boolean'
					),
					'Typeorient.lib_type_orient' => array(
						'label' => __m( 'Typeorient.lib_type_orient' ),
					),
					'Structurereferente.lib_struc' => array(
						'label' => __m( 'Structurereferente.lib_struc' ),
					)
				),
				array(
					'options' => $options,
					'id' => 'TableAccompagnementsbeneficiairesIndexDroits',
					'th' => true,
					'class' => 'details'
				)
			);
			echo '</td>';

			echo '<td class="noborder" style="width: 50%;">';
			echo $this->Html->tag( 'h3', 'Compétences' );
			echo $this->Default3->view(
				$details,
				array(
					'Cer93.nivetu' => array(
						'label' => __m( 'Cer93.nivetu' ),
						'condition' => '"#Cer93.nivetu#" != ""'
					),
					'DspRev.nivetu' => array(
						'label' => __m( 'DspRev.nivetu' ),
						'condition' => '"#DspRev.nivetu#" != ""'
					),
					'Questionnaired1pdv93.nivetu' => array(
						'label' => __m( 'Questionnaired1pdv93.nivetu' ),
						'condition' => '"#Questionnaired1pdv93.nivetu#" != ""'
					),
					'Dsp.nivetu' => array(
						'label' => __m( 'Dsp.nivetu' ),
						'condition' => '("#Dsp.nivetu#" != "") || ( "#Cer93.nivetu#" == "" && "#DspRev.nivetu#" == "" && "#Questionnaired1pdv93.nivetu#" == "" )'
					),
					'Diplomecer93.name' => array(
						'label' => __m( 'Diplomecer93.name' ),
					),
					'Appellationromev3.name' => array(
						'label' => __m( 'Appellationromev3.name' ),
					),
					'Accompagnement.topmoyloco' => array(
						'label' => __m( 'Accompagnement.topmoyloco' ),
					),
					'Accompagnement.toppermicondu' => array(
						'label' => __m( 'Accompagnement.toppermicondu' ),
					),
				),
				array(
					'options' => $options,
					'id' => 'TableAccompagnementsbeneficiairesIndexCompetences',
					'th' => true,
					'class' => 'details'
				)
			);
			echo '</td></tr></table>';

			echo $this->Html->tag( 'h3', 'Suivi' );
			echo $this->Default3->view(
				$details,
				array(
					'Referent.nom_complet' => array(
						'label' => __m( 'Referent.nom_complet' ),
					),
				),
				array(
					'options' => $options,
					'id' => 'TableAccompagnementsbeneficiairesIndexSuivi',
					'th' => true,
					'class' => 'details'
				)
			);

			// Tableau d'actions
			echo $this->Html->tag( 'h3', 'Actions' );

			echo $this->Default3->DefaultForm->create( null, array( 'id' => 'AccompagnementsbeneficiairesActionsSearchForm' ) );
			$this->request->data = array(
				'Search' => array(
					'Action' => array(
						'date_from' => date_sql_to_cakephp( '2009-01-01' )
					)
				)
			);
			echo $this->SearchForm->dateRange(
				'Search.Action.date',
				array(
					'prefix' => 'Search',
					'legend' => __m( 'Search.Action.date' ),
					'minYear_from' => 2009,
					'minYear_to' => 2009,
					'maxYear_from' => date( 'Y' ) + 1,
					'maxYear_to' => date( 'Y' ) + 1
				)
			);
			echo $this->Default3->DefaultForm->input(
				'Search.Action.name',
				array(
					'label' => __m( 'Search.Action.name' ),
					'options' => $options['Action']['name'],
					'empty' => true
				)
			);
			echo $this->Default3->DefaultForm->end();

			echo $this->Default3->index(
				$actions,
				array(
					//----------------------------------------------------------
					// Structure effectuant l'action
					//----------------------------------------------------------
					'Structurereferente.lib_struc' => array(
						'label' => __m( 'Action.lib_struc' )
					),
					//----------------------------------------------------------
					// Nom du référent effectuant l'action
					//----------------------------------------------------------
					'Referent.nom_complet' => array(
						'label' => __m( 'Action.nom_complet' )
					),
					//----------------------------------------------------------
					// Date
					//----------------------------------------------------------
					'Action.date' => array( // Lorsqu'on n'a pas de date
						'label' => __m( 'Action.date' ),
						'class' => 'sortable date-fr filter_date date',
						'condition' => '!in_array( "#Action.name#", array( "Rendezvouscollectif", "Rendezvousindividuel", "Contratinsertion", "Ficheprescription93", "Questionnaired1pdv93", "Questionnaired2pdv93", "Entretien", "DspRev" ) )', // FIXME
						'condition_group' => 'date',
					),
					'Rendezvous.daterdv' => array(
						'type' => 'date',
						'class' => 'sortable date-fr filter_date date',
						'condition' => 'in_array( "#Action.name#", array( "Rendezvouscollectif", "Rendezvousindividuel" ) )',
						'condition_group' => 'date',
					),
					'Contratinsertion.created' => array(
						'type' => 'date',
						'class' => 'sortable date-fr filter_date date',
						'condition' => '"#Action.name#" == "Contratinsertion"',
						'condition_group' => 'date',
					),
					'Ficheprescription93.created' => array(
						'type' => 'date',
						'class' => 'sortable date-fr filter_date date',
						'condition' => '"#Action.name#" == "Ficheprescription93"',
						'condition_group' => 'date',
					),
					'Questionnaired1pdv93.created' => array(
						'type' => 'date',
						'class' => 'sortable date-fr filter_date date',
						'condition' => '"#Action.name#" == "Questionnaired1pdv93"',
						'condition_group' => 'date',
					),
					'Questionnaired2pdv93.created' => array(
						'type' => 'date',
						'class' => 'sortable date-fr filter_date date',
						'condition' => '"#Action.name#" == "Questionnaired2pdv93"',
						'condition_group' => 'date',
					),
					'Entretien.dateentretien' => array(
						'type' => 'date',
						'class' => 'sortable date-fr filter_date date',
						'condition' => '"#Action.name#" == "Entretien"',
						'condition_group' => 'date',
					),
					'DspRev.created' => array(
						'type' => 'date',
						'class' => 'sortable date-fr filter_date date',
						'condition' => '"#Action.name#" == "DspRev"',
						'condition_group' => 'date',
					),
					//----------------------------------------------------------
					// Action
					//----------------------------------------------------------
					'Action.name' => array(
						'label' => __m( 'Action.name' ),
						'class' => '#Action.name#'
					),
					//----------------------------------------------------------
					// Statut
					//----------------------------------------------------------
					'Action.statut' => array(
						'label' => __m( 'Action.statut' ),
						'condition' => '!in_array("#Action.name#", array( "Rendezvousindividuel", "Rendezvouscollectif", "Contratinsertion", "Ficheprescription93", "Questionnaired1pdv93", "Questionnaired2pdv93" ))',
						'condition_group' => 'statut'
					),
					'Statutrdv.libelle' => array(
						'label' => __m( 'Action.statut' ),
						'condition' => 'in_array("#Action.name#", array( "Rendezvousindividuel", "Rendezvouscollectif" ))',
						'condition_group' => 'statut'
					),
					'Cer93.positioncer' => array(
						'label' => __m( 'Action.statut' ),
						'condition' => 'in_array("#Action.name#", array( "Contratinsertion" ))',
						'condition_group' => 'statut'
					),
					'Ficheprescription93.statut' => array(
						'label' => __m( 'Action.statut' ),
						'condition' => 'in_array("#Action.name#", array( "Ficheprescription93" ))',
						'condition_group' => 'statut'
					),
					'Questionnaired1d2pdv93.statut' => array(
						'label' => __m( 'Action.statut' ),
						'value' => 'Validé',
						'condition' => 'in_array("#Action.name#", array( "Questionnaired1pdv93", "Questionnaired2pdv93" ))',
						'condition_group' => 'statut'
					),
					//  ----------------------------------------------------------------
					// Informations
					//----------------------------------------------------------
					'Rendezvous.thematiques_virgules' => array(
						'label' => __m( 'Action.informations' ),
						'condition' => 'in_array("#Action.name#", array( "Rendezvousindividuel", "Rendezvouscollectif" ))',
						'class' => 'informations',
						'condition_group' => 'informations'
					),
					'Contratinsertion.informations' => array(
						'label' => __m( 'Action.informations' ),
						'condition' => '"#Action.name#" === "Contratinsertion"',
						'value' => 'Contrat de #Cer93.duree# mois du #Contratinsertion.dd_ci,date_short# au #Contratinsertion.df_ci,date_short#',
						'class' => 'informations',
						'condition_group' => 'informations'
					),
					'Ficheprescription93.informations' => array(
						'label' => __m( 'Action.informations' ),
						'condition' => '"#Action.name#" === "Ficheprescription93"',
						'value' => '#Thematiquefp93.name#, #Categoriefp93.name#',
						'class' => 'informations',
						'condition_group' => 'informations'
					),
					'Action.informations' => array(
						'label' => __m( 'Action.informations' ),
						'condition' => 'in_array( "#Action.name#", array( "Questionnaired1pdv93", "DspRev" ) )',
						'class' => 'informations',
						'condition_group' => 'informations'
					),
					'Questionnaired2pdv93.situationaccompagnement' => array(
						'label' => __m( 'Action.informations' ),
						'condition' => '"#Action.name#" == "Questionnaired2pdv93"',
						'class' => 'informations',
						'condition_group' => 'informations'
					),
					'Action.informations_entretiens' => array(
						'label' => __m( 'Action.informations' ),
						'condition' => '"#Action.name#" === "Entretien"',
						'value' => '#Objetentretien.name#',
						'class' => 'informations',
						'condition_group' => 'informations'
					),
					//  --------------------------------------------------------
					//  Lien voir
					//  --------------------------------------------------------
					'/Rendezvous/view/#Rendezvous.id#' => array(
						'class' => 'view external',
						'msgid' => 'Voir',
						'condition' => 'in_array( "#Action.name#", array( "Rendezvousindividuel", "Rendezvouscollectif" ) )',
						'condition_group' => 'view',
						'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Rendezvous', 'view' )
					),
					'/Cers93/view/#Contratinsertion.id#' => array(
						'class' => 'view external',
						'msgid' => 'Voir',
						'condition' => '"#Action.name#" == "Contratinsertion"',
						'condition_group' => 'view',
						'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Cers93', 'view' )
					),
					// INFO: pas de visualisation pour la fiche de prescription
					'/Fichesprescriptions93/view/#Ficheprescription93.id#' => array(
						'class' => 'view external',
						'msgid' => 'Voir',
						'condition' => '"#Action.name#" == "Ficheprescription93"',
						'condition_group' => 'view',
						'disabled' => true,
					),
					'/Questionnairesd1pdvs93/view/#Questionnaired1pdv93.id#' => array(
						'class' => 'view external',
						'msgid' => 'Voir',
						'condition' => '"#Action.name#" == "Questionnaired1pdv93"',
						'condition_group' => 'view',
						'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Questionnairesd1pdvs93', 'view' )
					),
					// INFO: pas de visualisation pour le questionnaire D2
					'/Questionnairesd2pdvs93/view/#Questionnaired2pdv93.id#' => array(
						'class' => 'view external',
						'msgid' => 'Voir',
						'condition' => '"#Action.name#" == "Questionnaired2pdv93"',
						'condition_group' => 'view',
						'disabled' => true
					),
					'/Dsps/view_revs/#DspRev.id#' => array(
						'class' => 'view external',
						'msgid' => 'Voir',
						'condition' => '"#Action.name#" == "DspRev"',
						'condition_group' => 'view',
						'disabled' => true !== WebrsaPermissions::checkDossier( 'Dsps', 'view_revs', $dossierMenu )
					),
					'/Entretiens/view/#Entretien.id#' => array(
						'class' => 'view external',
						'msgid' => 'Voir',
						'condition' => '"#Action.name#" == "Entretien"',
						'condition_group' => 'view',
						'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Entretiens', 'view' )
					),
					'/#Action.view#' => array(
						'class' => 'view external',
						'msgid' => 'Voir',
						'condition' => '!in_array( "#Action.name#", array( "Rendezvousindividuel", "Rendezvouscollectif", "Contratinsertion", "Ficheprescription93", "Questionnaired1pdv93", "Questionnaired2pdv93", "DspRev", "Entretien" ) )',
						'condition_group' => 'view',
						'disabled' => true
					),
					//  --------------------------------------------------------
					//  Lien modifier
					//  --------------------------------------------------------
					'/Rendezvous/edit/#Rendezvous.id#' => array(
						'class' => 'edit',
						'msgid' => 'Modifier',
						'condition' => 'in_array( "#Action.name#", array( "Rendezvousindividuel", "Rendezvouscollectif" ) )',
						'condition_group' => 'edit',
						'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Rendezvous', 'edit' )
					),
					'/Cers93/edit/#Contratinsertion.id#' => array(
						'class' => 'edit',
						'msgid' => 'Modifier',
						'condition' => '"#Action.name#" == "Contratinsertion" && (int)substr( "#Cer93.positioncer#", 0, 2 ) <= 1',
						'condition_group' => 'edit',
						'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Cers93', 'edit' )
					),
					'/Cers93/edit_apres_signature/#Contratinsertion.id#' => array(
						'class' => 'edit',
						'msgid' => 'Modifier',
						'condition' => '"#Action.name#" == "Contratinsertion" && (int)substr( "#Cer93.positioncer#", 0, 2 ) > 1',
						'condition_group' => 'edit',
						'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Cers93', 'edit_apres_signature' )
					),
					'/Fichesprescriptions93/edit/#Ficheprescription93.id#' => array(
						'class' => 'edit',
						'msgid' => 'Modifier',
						'condition' => '"#Action.name#" == "Ficheprescription93"',
						'condition_group' => 'edit',
						'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Fichesprescriptions93', 'edit' )
					),
					// INFO: pas de modification pour le questionnaire D1
					'/Questionnairesd1pdvs93/edit/#Questionnaired1pdv93.id#' => array(
						'class' => 'edit',
						'msgid' => 'Modifier',
						'condition' => '"#Action.name#" == "Questionnaired1pdv93"',
						'condition_group' => 'edit',
						'disabled' => true
					),
					'/Questionnairesd2pdvs93/edit/#Questionnaired2pdv93.id#' => array(
						'class' => 'edit',
						'msgid' => 'Modifier',
						'condition' => '"#Action.name#" == "Questionnaired2pdv93"',
						'condition_group' => 'edit',
						'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Questionnairesd2pdvs93', 'edit' )
					),
					'/Dsps/edit/#DspRev.personne_id#/#DspRev.id#' => array(
						'class' => 'edit',
						'msgid' => 'Modifier',
						'condition' => '"#Action.name#" == "DspRev"',
						'condition_group' => 'edit',
						'disabled' => true !== WebrsaPermissions::checkDossier( 'Dsps', 'edit', $dossierMenu )
					),
					'/Entretiens/edit/#Entretien.id#' => array(
						'class' => 'edit',
						'msgid' => 'Modifier',
						'condition' => '"#Action.name#" == "Entretien"',
						'condition_group' => 'edit',
						'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Entretiens', 'edit' )
					),
					'/#Action.edit#' => array(
						'class' => 'edit',
						'msgid' => 'Modifier',
						'condition' => '!in_array( "#Action.name#", array( "Rendezvousindividuel", "Rendezvouscollectif", "Contratinsertion", "Ficheprescription93", "Questionnaired1pdv93", "Questionnaired2pdv93", "DspRev", "Entretien" ) )',
						'condition_group' => 'edit',
						'disabled' => true
					),
				),
				array(
					'class' => 'search sortable',
					'paginate' => false,
					'sort' => false,
					'options' => $options,
					'id' => 'TableAccompagnementsbeneficiairesIndexActions',
					'innerTable' => array(
						'Rendezvous.objetrdv' => array(
							'label' => __m( 'Action.objetrdv' ),
							'condition' => 'in_array( "#Action.name#", array( "Rendezvouscollectif", "Rendezvousindividuel" ) )',
							'format' => 'truncate'
						),
						'Rendezvous.commentairerdv' => array(
							'label' => __m( 'Action.commentairerdv' ),
							'condition' => 'in_array( "#Action.name#", array( "Rendezvouscollectif", "Rendezvousindividuel" ) )',
							'format' => 'truncate'
						),
						'Prestatairehorspdifp93.name' => array(
							'label' => __m( 'Action.prestatairefp' ),
							'condition' => '"#Action.name#" === "Ficheprescription93" && "#Prestatairehorspdifp93.name#" != ""',
						),
						'Prestatairefp93.name' => array(
							'label' => __m( 'Action.prestatairefp' ),
							'condition' => '"#Action.name#" === "Ficheprescription93" && "#Prestatairefp93.name#" != ""',
						),
						'Cer93.sujets_virgules' => array(
							'label' => __m( 'Action.sujets_virgules' ),
							'condition' => '"#Action.name#" === "Contratinsertion"',
						),
						'Cer93.prevu' => array(
							'label' => __m( 'Action.prevu' ),
							'condition' => '"#Action.name#" === "Contratinsertion"',
						),
					)
				)
			);
		?>
	</div>
	<div id="impressions">
		<?php
			echo $this->Html->tag( 'h2', 'Courriers', array( 'class' => 'title' ) );

			echo $this->Default3->DefaultForm->create( null, array( 'id' => 'AccompagnementsbeneficiairesImpressionsSearchForm' ) );
			$this->request->data = array(
				'Search' => array(
					'Impression' => array(
						'date_from' => date_sql_to_cakephp( '2009-01-01' )
					)
				)
			);
			echo $this->SearchForm->dateRange(
				'Search.Impression.date',
				array(
					'prefix' => 'Search',
					'legend' => __m( 'Search.Impression.date' ),
					'minYear_from' => 2009,
					'minYear_to' => 2009,
					'maxYear_from' => date( 'Y' ) + 1,
					'maxYear_to' => date( 'Y' ) + 1
				)
			);
			echo $this->Default3->DefaultForm->input(
				'Search.Impression.name',
				array(
					'label' => __m( 'Search.Impression.name' ),
					'options' => $options['Impression']['name'],
					'empty' => true
				)
			);
			echo $this->Default3->DefaultForm->end();

			echo $this->Default3->index(
				$impressions,
				array(
					'Impression.name' => array(
						'label' => 'Module',
						'class' => 'sortable #Impression.name#',
					),
					'Impression.type' => array(
						'label' => 'Type',
						'class' => 'sortable',
					),
					// ---------------------------------------------------------
					// Crée le
					// ---------------------------------------------------------
					'Apre.datedemandeapre' => array(
						'label' => 'Créé le',
						'condition' => '"#Impression.name#" == "Apre"',
						'condition_group' => 'created',
						'class' => 'sortable date-fr filter_date date',
					),
					'Commissionep.dateseance' => array(
						'label' => 'Créé le',
						'condition' => '"#Impression.name#" == "Commissionep"',
						'condition_group' => 'created',
						'class' => 'sortable date-fr filter_date date',
					),
					'Contratinsertion.created' => array(
						'label' => 'Créé le',
						'condition' => '"#Impression.name#" == "Contratinsertion"',
						'condition_group' => 'created',
						'class' => 'sortable date-fr filter_date date',
					),
					'Ficheprescription93.created' => array(
						'label' => 'Créé le',
						'condition' => '"#Impression.name#" == "Ficheprescription93"',
						'condition_group' => 'created',
						'class' => 'sortable date-fr filter_date date',
					),
					'Orientstruct.date_valid' => array(
						'label' => 'Créé le',
						'condition' => '"#Impression.name#" == "Orientstruct"',
						'condition_group' => 'created',
						'class' => 'sortable date-fr filter_date date',
					),
					'Relancenonrespectsanctionep93.daterelance' => array(
						'label' => 'Créé le',
						'condition' => '"#Impression.name#" == "Relancenonrespectsanctionep93"',
						'condition_group' => 'created',
						'class' => 'sortable date-fr filter_date date',
					),
					'Rendezvous.created' => array(
						'label' => 'Créé le',
						'condition' => '"#Impression.name#" == "Rendezvous"',
						'condition_group' => 'created',
						'class' => 'sortable date-fr filter_date date',
					),
					'Transfertpdv93.created' => array(
						'label' => 'Créé le',
						'condition' => '"#Impression.name#" == "Transfertpdv93"',
						'condition_group' => 'created',
						'class' => 'sortable date-fr filter_date date',
					),
				) + WebrsaAccess::links(
					array(
						// ---------------------------------------------------------
						// Lien impression
						// ---------------------------------------------------------
						'/Apres/impression/#Apre.id#' => array(
							'msgid' => 'Imprimer',
							'class' => 'impression',
							'condition' => '"#Impression.name#" == "Apre"',
							'condition_group' => 'impression',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Apres', 'impression' )
						),
						'/Commissionseps/impressionDecision/#Passagecommissionep.id#' => array(
							'msgid' => 'Imprimer',
							'class' => 'impression',
							'condition' => '"#Impression.name#" == "Commissionep" && "#Impression.type#" == "Décision"',
							'condition_group' => 'impression',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Commissionseps', 'impressionDecision' )
						),
						'/Commissionseps/printConvocationBeneficiaire/#Passagecommissionep.id#' => array(
							'msgid' => 'Imprimer',
							'class' => 'impression',
							'condition' => '"#Impression.name#" == "Commissionep" && "#Impression.type#" == "Convocation"',
							'condition_group' => 'impression',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Commissionseps', 'printConvocationBeneficiaire' )
						),
						'/Cers93/impression/#Contratinsertion.id#' => array(
							'msgid' => 'Imprimer',
							'class' => 'impression',
							'condition' => '"#Impression.name#" == "Contratinsertion" && "#Impression.impression#" == "impression"',
							'condition_group' => 'impression',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Cers93', 'impression' )
						),
						'/Cers93/impressionDecision/#Contratinsertion.id#' => array(
							'msgid' => 'Imprimer',
							'class' => 'impression',
							'condition' => '"#Impression.name#" == "Contratinsertion" && "#Impression.impression#" == "impressionDecision"',
							'condition_group' => 'impression',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Cers93', 'impressionDecision' )
						),
						'/Fichesprescriptions93/impression/#Ficheprescription93.id#' => array(
							'msgid' => 'Imprimer',
							'class' => 'impression',
							'condition' => '"#Impression.name#" == "Ficheprescription93"',
							'condition_group' => 'impression',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Fichesprescriptions93', 'impression' )
						),
						'/Orientsstructs/impression/#Orientstruct.id#' => array(
							'msgid' => 'Imprimer',
							'class' => 'impression',
							'condition' => '"#Impression.name#" == "Orientstruct"',
							'condition_group' => 'impression',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Orientsstructs', 'impression' )
						),
						'/Relancesnonrespectssanctionseps93/impression/#Relancenonrespectsanctionep93.id#' => array(
							'msgid' => 'Imprimer',
							'class' => 'impression',
							'condition' => '"#Impression.name#" == "Relancenonrespectsanctionep93"',
							'condition_group' => 'impression',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Relancesnonrespectssanctionseps93', 'impression' )
						),
						'/Rendezvous/impression/#Rendezvous.id#' => array(
							'msgid' => 'Imprimer',
							'class' => 'impression',
							'condition' => '"#Impression.name#" == "Rendezvous"',
							'condition_group' => 'impression',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Rendezvous', 'impression' )
						),
						'/Cohortestransfertspdvs93/impression/#Orientstruct.id#' => array(
							'msgid' => 'Imprimer',
							'class' => 'impression',
							'condition' => '"#Impression.name#" == "Transfertpdv93"',
							'condition_group' => 'impression',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Cohortestransfertspdvs93', 'impression' )
						),
						// ---------------------------------------------------------
						// Lien voir
						// ---------------------------------------------------------
						'/Apres/view/#Apre.id#' => array(
							'msgid' => 'Voir',
							'class' => 'view external',
							'condition' => '"#Impression.name#" == "Apre"',
							'condition_group' => 'view',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Apres', 'view' )
						),
						'/Historiqueseps/view_passage/#Passagecommissionep.id#' => array(
							'msgid' => 'Voir',
							'class' => 'view external',
							'condition' => '"#Impression.name#" == "Commissionep"',
							'condition_group' => 'view',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Historiqueseps', 'view_passage' )
						),
						'/Cers93/view/#Contratinsertion.id#' => array(
							'msgid' => 'Voir',
							'class' => 'view external',
							'condition' => '"#Impression.name#" == "Contratinsertion"',
							'condition_group' => 'view',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Cers93', 'view' )
						),
						'/Fichesprescriptions93/view/#Ficheprescription93.id#' => array(
							'msgid' => 'Voir',
							'class' => 'view external',
							'condition' => '"#Impression.name#" == "Ficheprescription93"',
							'condition_group' => 'view',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Fichesprescriptions93', 'view' )
						),
						'/Orientsstructs/view/#Orientstruct.id#' => array(
							'msgid' => 'Voir',
							'class' => 'view external',
							'condition' => 'in_array( "#Impression.name#", array( "Orientstruct", "Transfertpdv93" ) )',
							'condition_group' => 'view',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Orientsstructs', 'view' )
						),
						'/Relancesnonrespectssanctionseps93/view/#Relancenonrespectsanctionep93.id#' => array(
							'msgid' => 'Voir',
							'class' => 'view external',
							'condition' => '"#Impression.name#" == "Relancenonrespectsanctionep93"',
							'condition_group' => 'view',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Relancesnonrespectssanctionseps93', 'view' )
						),
						'/Rendezvous/view/#Rendezvous.id#' => array(
							'msgid' => 'Voir',
							'class' => 'view external',
							'condition' => '"#Impression.name#" == "Rendezvous"',
							'condition_group' => 'view',
							'title' => false,
							'disabled' => Accompagnementbeneficiaire93Permissions::disabled( 'Rendezvous', 'view' )
						),
					)
				) + array(
					// ---------------------------------------------------------
					// Lien liste (les permissions ne figurent pas dans WebrsaAccess)
					// ---------------------------------------------------------
					'/Apres/index/#Apre.personne_id#' => array(
						'msgid' => 'Liste',
						'class' => 'index external',
						'condition' => '"#Impression.name#" == "Apre"',
						'condition_group' => 'index',
						'title' => false
					),
					'/Historiqueseps/index/#Dossierep.personne_id#' => array(
						'msgid' => 'Liste',
						'class' => 'index external',
						'condition' => '"#Impression.name#" == "Commissionep"',
						'condition_group' => 'index',
						'title' => false
					),
					'/Cers93/index/#Contratinsertion.personne_id#' => array(
						'msgid' => 'Liste',
						'class' => 'index external',
						'condition' => '"#Impression.name#" == "Contratinsertion"',
						'condition_group' => 'index',
						'title' => false
					),
					'/Fichesprescriptions93/index/#Ficheprescription93.personne_id#' => array(
						'msgid' => 'Liste',
						'class' => 'index external',
						'condition' => '"#Impression.name#" == "Ficheprescription93"',
						'condition_group' => 'index',
						'title' => false
					),
					'/Orientsstructs/index/#Orientstruct.personne_id#' => array(
						'msgid' => 'Liste',
						'class' => 'index external',
						'condition' => 'in_array( "#Impression.name#", array( "Orientstruct", "Transfertpdv93" ) )',
						'condition_group' => 'index',
						'title' => false
					),
					'/Relancesnonrespectssanctionseps93/index/#Personne.id#' => array(
						'msgid' => 'Liste',
						'class' => 'index external',
						'condition' => '"#Impression.name#" == "Relancenonrespectsanctionep93"',
						'condition_group' => 'index',
						'title' => false
					),
					'/Rendezvous/index/#Rendezvous.personne_id#' => array(
						'msgid' => 'Liste',
						'class' => 'index external',
						'condition' => '"#Impression.name#" == "Rendezvous"',
						'condition_group' => 'index',
						'title' => false
					)
				),
				array(
					'class' => 'search sortable',
					'paginate' => false,
					'options' => $options,
					'id' => 'TableAccompagnementsbeneficiairesIndexImpressions'
				)
			);
		?>
	</div>
	<div id="fichiersmodules">
		<?php
			// TODO: les mettre dans leurs propres vues (appelées en ajax à la demande)
			echo $this->Html->tag( 'h2', 'Fichiers liés', array( 'class' => 'title' ) );

			echo $this->Default3->DefaultForm->create( null, array( 'id' => 'AccompagnementsbeneficiairesFichiersmodulesSearchForm' ) );
			$this->request->data = array(
				'Search' => array(
					'Fichiermodule' => array(
						'date_from' => date_sql_to_cakephp( '2009-01-01' )
					)
				)
			);
			echo $this->SearchForm->dateRange(
				'Search.Fichiermodule.date',
				array(
					'prefix' => 'Search',
					'legend' => __m( 'Search.Fichiermodule.date' ),
					'minYear_from' => 2009,
					'minYear_to' => 2009,
					'maxYear_from' => date( 'Y' ) + 1,
					'maxYear_to' => date( 'Y' ) + 1
				)
			);
			echo $this->Default3->DefaultForm->input(
				'Search.Fichiermodule.modele',
				array(
					'label' => __m( 'Search.Fichiermodule.modele' ),
					'options' => $options['Fichiermodule']['modele'],
					'required' => false,
					'empty' => true
				)
			);
			echo $this->Default3->DefaultForm->end();

			echo $this->Default3->index(
				$fichiersmodules,
				array(
					'Fichiermodule.modele' => array(
						'label' => 'Module',
						'class' => '#Fichiermodule.modele#'
					),
					'Fichiermodule.name' => array(
						'label' => 'Nom',
						'class' => 'sortable',
					),
					'Fichiermodule.mime' => array(
						'label' => 'Type',
						'class' => 'sortable',
					),
					'Fichiermodule.created' => array(
						'label' => 'Créé le',
						'class' => 'sortable date-fr filter_date date',
					),
					'/#Fichiermodule.controller#/download/#Fichiermodule.id#' => array(
						'msgid' => 'Télécharger',
						'title' => 'Télécharger le fichier lié'
					),
					'/#Fichiermodule.controller#/#Fichiermodule.view_action#/#Fichiermodule.fk_value#' => array(
						'class' => 'view external',
						'disabled' => 'in_array( "#Fichiermodule.modele#", array( "PersonneReferent", "Orientstruct" ) )',
						'msgid' => 'Voir',
						'title' => 'Voir l\'enregistrement auquel le fichier est lié'
					),
					'/#Fichiermodule.controller#/filelink/#Fichiermodule.fk_value#' => array(
						'class' => 'external',
						'msgid' => 'Liste',
						'title' => 'Liste des fichiers liés'
					)
				),
				array(
					'class' => 'search sortable',
					'paginate' => false,
					'options' => $options,
					'id' => 'TableAccompagnementsbeneficiairesIndexFichiersmodules'
				)
			);
		?>
	</div>
<script type="text/javascript">
	//<![CDATA[
	/**
	 * Vérifie si une ligne doit être affichée en vérifiant s'il existe au moins
	 * une cellule (td) possédant la classe passée en paramètre.
	 *
	 * @param {String} target Le nom de la classe à trouver
	 * @param {type} row
	 * @returns {Boolean}
	 */
	function conditionAction( target, row ) {
		try {
			target = $(target).value;
			return ( target === '' ) || $(row).select( 'td.' + target ).length > 0;
		} catch( e ) {
			console.log( e );
			return false;
		}
	}

	/**
	 * Vérifie si une ligne doit être affichée en vérifiant si la plage de dates
	 * a été activée et si la date contenue dans la première colonne de classe
	 * filter_date est bien dans la plage de dates.
	 *
	 * @param {String} checkbox L'id de la case à cocher
	 * @param {String} from Le préfixe des ids des select pour la date de début
	 * @param {String} to Le préfixe des ids des select pour la date de fin
	 * @param {type} row
	 * @returns {Boolean}
	 */
	function conditionDateRange( checkbox, from, to, row ) {
		var checked = $(checkbox).checked, text, value;

		// ... à 00:00:00
		from = Webrsa.Date.fromCakeSelects(from);

		// ... à 23:59:59
		to = Webrsa.Date.fromCakeSelects(to);
		to.setHours( 23 );
		to.setMinutes( 59 );
		to.setSeconds( 59 );

		try {
			text = $(row).select( 'td.date.filter_date' )[0].innerHTML;
			value = Webrsa.Date.fromText(text);

		return checked === false
			|| value === null
			|| ( from.getTime() <= value && to.getTime() >= value );

		} catch( e ) {
			console.log( e );
			return false;
		}
	}

	/**
	 * Vérifie le nombre de lignes d'une table (dans tbody). Si aucune ligne
	 * n'est affichée, alors la table sera cachée et la notice "Aucun enregistrement"
	 * sera affichée; dans le cas contraire, la notice sera supprimée si elle
	 * existe et la table sera montrée.
	 *
	 * @param {String} table L'id de la table à traiter
	 * @returns {undefined}
	 */
	function computeTableVisibility( table ) {
		var rows,
			messageId = table + 'EmptyMessage',
			shown = 0, message;

		// Si la table existe
		if( null !== $(table) ) {
			rows = $(table).select( 'tbody tr' );

			$(rows).each( function( row ) {
				if( false === $(row).up('table').hasClassName('innerTable') && $(row).visible() ) {
					shown++;

					$(row).removeClassName( 'odd' );
					$(row).removeClassName( 'even' );

					$(row).addClassName( ( shown % 2 === 0 ? 'even' : 'odd' ) );
				}
			} );

			// Si aucun enregistrement n'est à afficher, afficher un message et cacher le tableau, etc...
			message = $(messageId);
			// On supprimer le message s'il existe
			if( null !== message ) {
				$(message).remove();
			}

			// Si aucune ligne n'est à afficher
			if( 0 === shown ) {
				message = new Element( 'p', { 'class': 'notice', 'id': messageId } ).update( 'Aucun enregistrement' );
				$(table).insert( { 'before' : message } );
				$(table).hide();
			}
			// S'il existe des lignes à afficher
			else {
				$(table).show();
			}
		}
	}

	/**
	 * Filtre des lignes de la table d'actions par type d'action suivant la valeur
	 * du champ de liste déroulante (et de la plage de dates le cas échéant).
	 *
	 * @param {String} table L'id de la table à traiter
	 * @returns {undefined}
	 */
	function filterActionsTableByAction( table ) {
		var rows, show;

		// Si la table existe
		if( null !== $(table) ) {
			rows = $(table).select( 'tbody tr' );

			$(rows).each( function( row ) {
				if( false === $(row).up('table').hasClassName('innerTable') ) {
					show = conditionAction( 'SearchActionName', row )
						&& conditionDateRange( 'SearchActionDate', 'SearchActionDateFrom', 'SearchActionDateTo', row );

					if( show ) {
						$(row).show();
					}
					else {
						$(row).hide();
					}
				}
			} );

			computeTableVisibility( table );
		}
	}

	/**
	 * Filtre des lignes de la table d'actions par plage de dates (et par type
	 * d'action suivant la valeur du champ de liste déroulante le cas échéant).
	 *
	 * @param {String} table L'id de la table à traiter
	 * @returns {undefined}
	 */
	function filterActionsTableByDateRange( table ) {
		var rows, show;

		// Si la table existe
		if( null !== $(table) ) {
			rows = $(table).select( 'tbody tr' );

			$(rows).each( function( row ) {
				if( false === $(row).up('table').hasClassName('innerTable') ) {
					show = conditionAction( 'SearchActionName', row )
						&& conditionDateRange( 'SearchActionDate', 'SearchActionDateFrom', 'SearchActionDateTo', row );

					if( show ) {
						$(row).show();
					}
					else {
						$(row).hide();
					}
				}
			} );

			computeTableVisibility( table );
		}
	}

	// -------------------------------------------------------------------------
	// Initialisation des filtres à appliquer sur la table d'actions, observation
	// des champs de formulaire.
	// -------------------------------------------------------------------------
	$('SearchActionName').observe( 'change', function() {
		filterActionsTableByAction( 'TableAccompagnementsbeneficiairesIndexActions' );
		return false;
	} );

	[ 'SearchActionDate', 'SearchActionDateFromYear', 'SearchActionDateFromMonth', 'SearchActionDateFromDay', 'SearchActionDateToYear', 'SearchActionDateToMonth', 'SearchActionDateToDay'].each(
		function( field ) {
			$(field).observe( 'change', function() {
				filterActionsTableByDateRange( 'TableAccompagnementsbeneficiairesIndexActions' );
				return false;
			} );
		}
	);

	// -------------------------------------------------------------------------

	/**
	 * Filtre des lignes de la table d'actions par type d'action suivant la valeur
	 * du champ de liste déroulante (et de la plage de dates le cas échéant).
	 *
	 * @param {String} table L'id de la table à traiter
	 * @returns {undefined}
	 */
	function filterImpressionsTableByImpression( table ) {
		var rows, show;

		// Si la table existe
		if( null !== $(table) ) {
			rows = $(table).select( 'tbody tr' );

			$(rows).each( function( row ) {
				if( false === $(row).up('table').hasClassName('innerTable') ) {
					show = conditionAction( 'SearchImpressionName', row )
						&& conditionDateRange( 'SearchImpressionDate', 'SearchImpressionDateFrom', 'SearchImpressionDateTo', row );

					if( show ) {
						$(row).show();
					}
					else {
						$(row).hide();
					}
				}
			} );

			computeTableVisibility( table );
		}
	}

	/**
	 * Filtre des lignes de la table d'actions par plage de dates (et par type
	 * d'action suivant la valeur du champ de liste déroulante le cas échéant).
	 *
	 * @param {String} table L'id de la table à traiter
	 * @returns {undefined}
	 */
	function filterImpressionsTableByDateRange( table ) {
		var rows, show;

		// Si la table existe
		if( null !== $(table) ) {
			rows = $(table).select( 'tbody tr' );

			$(rows).each( function( row ) {
				if( false === $(row).up('table').hasClassName('innerTable') ) {
					show = conditionAction( 'SearchImpressionName', row )
						&& conditionDateRange( 'SearchImpressionDate', 'SearchImpressionDateFrom', 'SearchImpressionDateTo', row );

					if( show ) {
						$(row).show();
					}
					else {
						$(row).hide();
					}
				}
			} );

			computeTableVisibility( table );
		}
	}

	// -------------------------------------------------------------------------
	// Initialisation des filtres à appliquer sur la table d'impressions, observation
	// des champs de formulaire.
	// @fixme
	//	- filtre par date
	// -------------------------------------------------------------------------
	$('SearchImpressionName').observe( 'change', function() {
		filterImpressionsTableByImpression( 'TableAccompagnementsbeneficiairesIndexImpressions' );
		return false;
	} );

	[ 'SearchImpressionDate', 'SearchImpressionDateFromYear', 'SearchImpressionDateFromMonth', 'SearchImpressionDateFromDay', 'SearchImpressionDateToYear', 'SearchImpressionDateToMonth', 'SearchImpressionDateToDay'].each(
		function( field ) {
			$(field).observe( 'change', function() {
				filterImpressionsTableByDateRange( 'TableAccompagnementsbeneficiairesIndexImpressions' );
				return false;
			} );
		}
	);

	// -------------------------------------------------------------------------

	// -------------------------------------------------------------------------

	/**
	 * Filtre des lignes de la table d'actions par type d'action suivant la valeur
	 * du champ de liste déroulante (et de la plage de dates le cas échéant).
	 *
	 * @param {String} table L'id de la table à traiter
	 * @returns {undefined}
	 */
	function filterFichiersmodulesTableByFichiermodule( table ) {
		var rows, show;

		// Si la table existe
		if( null !== $(table) ) {
			rows = $(table).select( 'tbody tr' );

			$(rows).each( function( row ) {
				if( false === $(row).up('table').hasClassName('innerTable') ) {
					show = conditionAction( 'SearchFichiermoduleModele', row )
						&& conditionDateRange( 'SearchFichiermoduleDate', 'SearchFichiermoduleDateFrom', 'SearchFichiermoduleDateTo', row );

					if( show ) {
						$(row).show();
					}
					else {
						$(row).hide();
					}
				}
			} );

			computeTableVisibility( table );
		}
	}

	/**
	 * Filtre des lignes de la table d'actions par plage de dates (et par type
	 * d'action suivant la valeur du champ de liste déroulante le cas échéant).
	 *
	 * @param {String} table L'id de la table à traiter
	 * @returns {undefined}
	 */
	function filterFichiersmodulesTableByDateRange( table ) {
		var rows, show;

		// Si la table existe
		if( null !== $(table) ) {
			rows = $(table).select( 'tbody tr' );

			$(rows).each( function( row ) {
				if( false === $(row).up('table').hasClassName('innerTable') ) {
					show = conditionAction( 'SearchFichiermoduleModele', row )
						&& conditionDateRange( 'SearchFichiermoduleDate', 'SearchFichiermoduleDateFrom', 'SearchFichiermoduleDateTo', row );

					if( show ) {
						$(row).show();
					}
					else {
						$(row).hide();
					}
				}
			} );

			computeTableVisibility( table );
		}
	}

	// -------------------------------------------------------------------------
	// Initialisation des filtres à appliquer sur la table d'impressions, observation
	// des champs de formulaire.
	// @fixme
	//	- filtre par date
	// -------------------------------------------------------------------------
	$('SearchFichiermoduleModele').observe( 'change', function() {
		filterFichiersmodulesTableByFichiermodule( 'TableAccompagnementsbeneficiairesIndexFichiersmodules' );
		return false;
	} );

	[ 'SearchFichiermoduleDate', 'SearchFichiermoduleDateFromYear', 'SearchFichiermoduleDateFromMonth', 'SearchFichiermoduleDateFromDay', 'SearchFichiermoduleDateToYear', 'SearchFichiermoduleDateToMonth', 'SearchFichiermoduleDateToDay'].each(
		function( field ) {
			$(field).observe( 'change', function() {
				filterFichiersmodulesTableByDateRange( 'TableAccompagnementsbeneficiairesIndexFichiersmodules' );
				return false;
			} );
		}
	);

	// -------------------------------------------------------------------------

	initSortableTables();

	makeTabbed( 'tabbedWrapper', 2 );

	// Au chargement de la page, on trie les tableaux par date décroissant
	document.observe( "dom:loaded", function() {
		if( null !== $( 'TableAccompagnementsbeneficiairesIndexActions' ) ) {
			TableKit.Sortable.sort( 'TableAccompagnementsbeneficiairesIndexActions', 3, -1 );
		}
		if( null !== $( 'TableAccompagnementsbeneficiairesIndexFichiersmodules' ) ) {
			TableKit.Sortable.sort( 'TableAccompagnementsbeneficiairesIndexFichiersmodules', 4, -1 );
		}
		if( null !== $( 'TableAccompagnementsbeneficiairesIndexImpressions' ) ) {
			TableKit.Sortable.sort( 'TableAccompagnementsbeneficiairesIndexImpressions', 3, -1 );
		}
	} );
	//]]>
</script>