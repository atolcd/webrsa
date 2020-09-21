<?php
	/**
	 * Code source de la classe WebrsaAco.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe WebrsaAco ...
	 *
	 * @package app.Model
	 */
	class WebrsaAco extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaAco';

		/**
		 * On n'utilise pas de table.
		 *
		 * @var string|boolean
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array();

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'WebrsaRecherche' );

		/**
		 * Liste des noms des contrôleurs des modules activables et désactivables.
		 *
		 * @var array
		 */
		public $modules = array(
			'Attributiondroits' => 'Module.Attributiondroits.enabled',
			'Cuis' => 'Module.Cui.enabled',
			'Actionroles' => 'Module.Dashboards.enabled',
			'Dashboards' => 'Module.Dashboards.enabled',
			'Categoriesactionroles' => 'Module.Dashboards.enabled',
			'Roles' => 'Module.Dashboards.enabled',
			'Donneescaf' => 'Module.Donneescaf.enabled',
			'Logtraces' => 'Module.Logtrace.enabled',
			'Synthesedroits' => 'Module.Synthesedroits.enabled',
			'Cantons' => 'CG.cantons',
			'Cataloguesromesv3' => 'Romev3.enabled',
			'Requestgroups' => 'Requestmanager.enabled',
			'Requestsmanager' => 'Requestmanager.enabled',
			'Savesearchs' => 'Module.Savesearch.enabled',
			'Thematiquesrdvs' => 'Rendezvous.useThematique',
			// @todo Changementsadresses, ... ?
		);

		/**
		 * Liste des contrôleurs spécifiques à un ou plusieurs départements mais
		 * dont le nom ne donne pas d'indication.
		 *
		 * @var array
		 */
		public $controllers = array(
			'controllers/Accompagnementsbeneficiaires' => array( 93, '99X' ),
			'controllers/Aidesdirectes' => array( 66, '99X' ),
			'controllers/Actions' => array( 58, 93, 976, '99X' ),
			'controllers/Actionscandidats' => array( 66, '99X' ),
			'controllers/ActionscandidatsPartenaires' => array( 66, '99X' ),
			'controllers/ActionscandidatsPersonnes' => array( 66, '99X' ),
			'controllers/Actionsinsertion' => array( 66, '99X' ),
			'controllers/Ajoutdossiers' => array( 58, 93, 976, '99X' ),
			'controllers/Ajoutdossierscomplets' => array( 66, '99X' ),
			'controllers/Apres' => array( 66, 93, '99X' ),
			'controllers/ApresComitesapres' => array( 93, '99X' ),
			'controllers/Budgetsapres' => array( 93, '99X' ),
			'controllers/Categorietags' => array( 58, 93, 66, '99X' ),
			'controllers/Cohortescomitesapres' => array( 93, '99X' ),
			'controllers/Cohortesrendezvous' => array( 93, '99X' ),
			'controllers/Comitesapres' => array( 93, '99X' ),
			'controllers/ComitesapresParticipantscomites' => array( 93, '99X' ),
			'controllers/Commissionseps' => array( 58, 66, 93, '99X' ),
			'controllers/Communautessrs' => array( 93, '99X' ),
			'controllers/Compositionsregroupementseps' => array( 58, 66, 93, '99X' ),
			'controllers/Creances' => array( 58, 66, 93, '99X' ),
			'controllers/Titrescreances' => array( 58, 66, 93, '99X' ),
			'controllers/Creancesalimentaires' => array( 58, 66, 93, '99X' ),
			'controllers/Contactspartenaires' => array( 66, '99X' ),
			'controllers/Dossierseps' => array( 58, 66, 93, '99X' ),
			'controllers/Dossierssimplifies' => array( 66, 93, 976, '99X' ),
			'controllers/Eps' => array( 58, 66, 93, '99X' ),
			'controllers/Etatsliquidatifs' => array( 93, '99X' ),
			'controllers/Fichedeliaisons' => array( 66, '99X' ),
			'controllers/Fonctionsmembreseps' => array( 58, 66, 93, '99X' ),
			'controllers/Historiqueseps' => array( 58, 66, 93, '99X' ),
			'controllers/Integrationfichiersapre' => array( 93, '99X' ),
			'controllers/Membreseps' => array( 58, 66, 93, '99X' ),
			'controllers/Logicielprimos' => array( 66, '99X' ),
			'controllers/Metiersexerces' => array( 93, '99X' ),
			'controllers/Motiffichedeliaisons' => array( 66, '99X' ),
			'controllers/Motifssortie' => array( 66, '99X' ),
			'controllers/Naturescontrats' => array( 93, '99X' ),
			'controllers/Nonorientationsproseps' => array( 58, 66, 93, '99X' ),
			'controllers/Offresinsertion' => array( 66, '99X' ),
			'controllers/Parametresfinanciers' => array( 93, '99X' ),
			'controllers/Partenaires' => array( 66, '99X' ),
			'controllers/Participantscomites' => array( 93, '99X' ),
			'controllers/Prestsform' => array( 66, '99X' ),
			'controllers/Primoanalyses' => array( 66, '99X' ),
			'controllers/Propositionprimos' => array( 66, '99X' ),
			'controllers/Propospdos' => array( 58, 66, 93, '99X' ),
			'controllers/Rapportstalendscreances' => array( 58, 66, 93, '99X' ),
			'controllers/Rejetstalendscreances' => array( 58, 66, 93, '99X' ),
			'controllers/Recoursapres' => array( 93, '99X' ),
			'controllers/Regressionsorientationseps' => array( 58, '99X' ),
			'controllers/Regroupementseps' => array( 58, 66, 93, '99X' ),
			'controllers/Relancesapres' => array( 93, '99X' ),
			'controllers/Repsddtefp' => array( 93, '99X' ),
			'controllers/Secteursactis' => array( 93, '99X' ),
			'controllers/Secteurscuis' => array( 66, '99X' ),
			'controllers/Signalementseps' => array( 93, '99X' ),
			'controllers/StatutsrdvsTypesrdv' => array( 58, '99X' ),
			'controllers/Suivisaidesapres' => array( 93, '99X' ),
			'controllers/Suivisaidesaprestypesaides' => array( 93, '99X' ),
			'controllers/Tags' => array( 58, 93, 66, '99X' ),
			'controllers/Tiersprestatairesapres' => array( 93, '99X' ),
			'controllers/Traitementstypespdos' => array( 58, 93, 976, '99X' ),
			'controllers/Typesactions' => array( 58, 93, 976, '99X' ),
			'controllers/Valeurstags' => array( 58, 93, 66, '99X' )
		);

		/**
		 * Liste des actions spécifiques à un ou plusieurs départements mais
		 * dont le nom ne donne pas d'indication.
		 *
		 * @var array
		 */
		public $actions = array(
			'controllers/Commissionseps/impressionpvcohorte' => array( 66, '99X' ),
			'controllers/Contratsinsertion/reconduction_cer_plus_55_ans' => array( 66, '99X' ),
			'controllers/Entretiens/impression' => array( 66, '99X' ),
			'controllers/Foyers/corbeille' => array( 66, '99X' ),
			'controllers/Foyers/filelink' => array( 66, '99X' ),
			'controllers/Indicateursmensuels/contratsinsertion' => array( 66, '99X' ),
			'controllers/Indicateursmensuels/nombre_allocataires' => array( 66, '99X' ),
			'controllers/Indicateursmensuels/orientations' => array( 66, '99X' ),
			'controllers/Orientsstructs/impression_changement_referent' => array( 66, '99X' ),
			'controllers/Referents/clotureenmasse' => array( 93, '99X' ),
		);

		/**
		 * Filtre la liste des acos en fonction du département connecté et de la
		 * configuration des différents modules de l'application.
		 *
		 * @param array $acos
		 * @param string|integer $departement
		 * @return array
		 */
		public function filterByDepartement( array $acos, $departement = null ) {
			$departement = (
				null === $departement
				? Configure::read( 'Cg.departement' )
				: $departement
			);

			$tree = array();

			foreach( $acos as $index => $aco ) {
				$tokens = explode( '/', $aco );
				$count = count( $tokens );

				// Filtre par département suivant le nom de l'aco
				$matches = array();
				if( preg_match( '/([0-9ABX]{2,3}\/|[0-9ABX]{2,3}$)/' , $aco, $matches ) && trim($matches[1], '/') != $departement ) {
					unset( $acos[$index] );
				}

				// Filtre par département suivant le contrôleur
				if( true === isset( $tokens[1] ) && true === isset( $this->controllers["controllers/{$tokens[1]}"] ) && false === in_array( $departement, $this->controllers["controllers/{$tokens[1]}"] ) ) {
					unset( $acos[$index] );
				}

				// Filtre par département suivant l'action
				if( true === isset( $tokens[2] ) && true === isset( $this->actions[$aco] ) && false === in_array( $departement, $this->actions[$aco] ) ) {
					unset( $acos[$index] );
				}

				// Filtre par département suivant le nom du contrôleur
				if( true === isset( $tokens[1] ) && true === isset( $this->controllers[$tokens[1]] ) && false === in_array( $departement, $this->controllers[$tokens[1]] ) ) {
					unset( $acos[$index] );
				}

				//  Filtre par modules activables / désactivables
				if( true === isset( $tokens[1] ) && true === isset( $this->modules[$tokens[1]] ) && true !== (bool)Configure::read( $this->modules[$tokens[1]] ) ) {
					unset( $acos[$index] );
				}

				// Filtrer par moteur de recherche spécifique à un ou plusieurs départements
				if(3 === $count && true === isset($this->WebrsaRecherche->searches["{$tokens[1]}.{$tokens[2]}"]['departement'])) {
					$available = (array)$this->WebrsaRecherche->searches["{$tokens[1]}.{$tokens[2]}"]['departement'];
					if(false === in_array($departement, $available)) {
						unset( $acos[$index] );
					}
				}

				// Remplissage de l'arbre afin de pouvoir nettoyer à la fin
				if( true === isset( $acos[$index] ) ) {
					$tree = Hash::insert( $tree, implode( '.', $tokens ), array() );
				}
			}

			// Nettoyage des Acos de second niveau ne contenant pas d'enfant
			foreach( array_keys( $tree ) as $key1 ) {
				foreach( array_keys( $tree[$key1] ) as $key2 ) {
					if( 0 === count( $tree[$key1][$key2] ) ) {
						$acoKey = array_search( "{$key1}/{$key2}", $acos );
						if( false !== $acoKey ) {
							unset( $acos[$acoKey] );
						}
					}
				}
			}

			return array_values( $acos );
		}
	}
?>