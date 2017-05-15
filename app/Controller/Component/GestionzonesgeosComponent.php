<?php
	/**
	 * Fichier source de la classe GestionzonesgeosComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe GestionzonesgeosComponent fournit des méthodes permettant d'obtenir la liste des codes
	 * INSSE et des cantons auxquels l'utilisateur a droit, suivant la configuration de l'application.
	 *
	 * Ces listes sont mises en cache dans la session, car elles dépendent de l'utilisateur.
	 *
	 * @package app.Controller.Component
	 */
	class GestionzonesgeosComponent extends Component
	{
		/**
		 * On a besoin d'un esession.
		 *
		 * @var array
		 */
		public $components = array( 'Session', 'Workflowscers93' );

		/**
		 * Retourne la liste des codes INSEE accessibles à l'utilisateur connecté, soit en faisant une requête
		 * (suivant la configuration de Zonesegeographiques.CodesInsee) dont les résultats sont mis en cache
		 * dans la session, soit en retournant la liste mise en cache.
		 *
		 * @return array
		 */
		public function listeCodesInsee() {
			$Controller = $this->_Collection->getController();

			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			if( !$this->Session->check( 'Cache.mesCodesInsee' ) ) {
				if( Configure::read( 'Zonesegeographiques.CodesInsee' ) ) {
					$listeCodesInseeLocalites = $Controller->User->Zonegeographique->listeCodesInseeLocalites(
						$mesCodesInsee,
						$this->Session->read( 'Auth.User.filtre_zone_geo' )
					);
				}
				else {
					$listeCodesInseeLocalites = $Controller->User->Zonegeographique->listeCodesInseeLocalites(
						ClassRegistry::init( 'Adresse' )->listeCodesInsee(),
						$this->Session->read( 'Auth.User.filtre_zone_geo' )
					);
				}
				$this->Session->write( 'Cache.mesCodesInsee', $listeCodesInseeLocalites );

				return $listeCodesInseeLocalites;
			}
			else {
				return $this->Session->read( 'Cache.mesCodesInsee' );
			}
		}

		/**
		 * Retourn la liste des cantons si la variable CG.cantons est à vrai dans le webrsa.inc.
		 * Si les cantons ne sont pas utilisés, un array vide sera retourné.
		 *
		 * @return array
		 */
		public function listeCantons() {
			$cantons = array();

			if( Configure::read( 'CG.cantons' ) ) {
				if ( !$this->Session->check( 'Cache.cantons' ) ) {
					$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
					$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

					$Canton = ClassRegistry::init( 'Canton' );
					$cantons = $Canton->selectList(
						$this->Session->read( 'Auth.User.filtre_zone_geo' ),
						array_keys( $mesCodesInsee )
					);

					$this->Session->write( 'Cache.cantons', $cantons );
				}
				else {
					$cantons = $this->Session->read( 'Cache.cantons' );
				}
			}

			return $cantons;
		}

		/**
		 * Retourn la liste des sites COV si la variable Cg.departement vaut 58
		 * dans le webrsa.inc. Cette liste est stockée dans la session de
		 * l'utilisateur.
		 *
		 * Si les sites COV ne sont pas utilisés, un array vide sera retourné.
		 *
		 * @fixme Dans Allocataire::options() ?
		 *
		 * @return array
		 */
		public function listeSitescovs58() {
			$sitescovs58 = array();

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$sessionKey = 'Cache.sitescovs58';

				if ( !$this->Session->check( $sessionKey ) ) {
					$Sitecov58 = ClassRegistry::init( 'Sitecov58' );
					$sitescovs58 = $Sitecov58->find( 'list' );

					$this->Session->write( $sessionKey, $sitescovs58 );
				}
				else {
					$sitescovs58 = $this->Session->read( $sessionKey );
				}
			}

			return $sitescovs58;
		}

		/**
		 * Envoie à la vue de la liste des cantons si la variable CG.cantons est à vrai dans le webrsa.inc.
		 * Si les cantons ne sont pas utilisés, cette variable sera néanmoins envoyée, mais sa valeur sera un
		 * tableau vide.
		 *
		 * @param string $varname Le nom de la variable envoyée à la vue.
		 * @return void
		 */
		public function setCantonsIfConfigured( $varname = 'cantons' ) {
			$Controller = $this->_Collection->getController();
			$Controller->set( $varname, $this->listeCantons() );
		}

		/**
		 * Ajoute des conditions à un querydata suivant les restrictions concernant
		 * les zones géographiques de l'utilisateur stockées dans la session.
		 *
		 * Lorsque l'on utilise les cantons, la restriction se fera à partir de
		 * ceux-ci.
		 *
		 * Si des conditions alternatives sont passées en paramètre, la restriction
		 * se fera sur les conditions générées OU sur les conditions alternatives.
		 *
		 * @see Auth.User.filtre_zone_geo (dans la session)
		 * @see Auth.Zonegeographique (dans la session)
		 * @see CG.cantons (Configure)
		 *
		 * @param array $querydata
		 * @param array $orConditions Les conditions alternatives
		 * @return array
		 */
		public function qdConditions( array $querydata, array $orConditions = array() ) {
			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			$filtre_zone_geo = $this->Session->read( 'Auth.User.filtre_zone_geo' );

			if( $filtre_zone_geo ) {
				// Si on utilise la table des cantons plutôt que la table zonesgeographiques
				if( Configure::read( 'CG.cantons' ) ) {
					$conditions = ClassRegistry::init( 'Canton' )->queryConditionsByZonesgeographiques( array_keys( $mesCodesInsee ) );
				}
				else {
					$mesCodesInsee = ( !empty( $mesCodesInsee ) ? $mesCodesInsee : array( null ) );
					$conditions = '( Adresse.numcom IN ( \''.implode( '\', \'', $mesCodesInsee ).'\' ) )';
				}

				if( !empty( $orConditions ) ) {
					$conditions = array(
						'OR' => array(
							$conditions,
							$orConditions
						)
					);
				}

				$querydata['conditions'][] = $conditions;
			}

			return $querydata;
		}

		/**
		 * Ajoute des conditions à un querydata suivant les restrictions concernant
		 * les zones géographiques de l'utilisateur stockées dans la session.
		 *
		 * Lorsque le paramètre $champStructurereferente est fourni, que le département
		 * configuré est le 93 et que l'utilisateur est lié à une structure référente,
		 * alors les conditions ajoutés sont soit la restriction sur les zones géographiques,
		 * soit la restriction sur le SR de l'utilisateur connecté et le champ passé en paramètre.
		 *
		 * Dans ce cas, un champ virtuel est ajouté au querydata (<Model>.horszone)
		 * qui lorsqu'il est à vrai signifie que l'enregistrement n'est pas sur les
		 * zones géographiques de l'utilisateur.
		 *
		 * @param array $query
		 * @param string $champStructurereferente
		 * @return array
		 */
		public function completeQuery( array $query, $champStructurereferente = null ) {
			$orConditions = array();

			if( !empty( $champStructurereferente ) && Configure::read( 'Cg.departement' ) == 93 ) {
				$structurereferente_id = $this->Workflowscers93->getUserStructurereferenteId( false );
				list( $modelName, $fieldName ) = model_field( $champStructurereferente );

				if( !empty( $structurereferente_id ) ) {
					$Model = ClassRegistry::init( $modelName );
					$Dbo = $Model->getDataSource();
					$conditions = (array)Hash::get( $this->qdConditions( array() ), 'conditions' );
					if( empty( $conditions ) ) {
						$conditions = array( '1 = 1' );
					}
					$conditions = $Dbo->conditions(
						array(
							'NOT' => $conditions,
							array( $champStructurereferente => $structurereferente_id )
						),
						true,
						false,
						$Model
					);

					$query['fields']["{$modelName}.horszone"] = "( {$conditions} ) AS \"{$modelName}__horszone\"";
					$orConditions = array( $champStructurereferente => $structurereferente_id );
				}
				else {
					$query['fields']["{$modelName}.horszone"] = "( FALSE ) AS \"{$modelName}__horszone\"";
				}
			}

			return $this->qdConditions( $query, $orConditions );
		}

		/**
		 * The beforeRedirect method is invoked when the controller's redirect method is called but before
		 * any further action. If this method returns false the controller will not continue on to redirect the
		 * request.
		 * The $url, $status and $exit variables have same meaning as for the controller's method.
		 *
		 * @param Controller $controller
		 * @param mixed $url
		 * @param type $status
		 * @param boolean $exit
		 */
		public function beforeRedirect( Controller $controller, $url, $status = null, $exit = true ) {
			parent::beforeRedirect( $controller, $url, $status , $exit );
		}
	}
?>