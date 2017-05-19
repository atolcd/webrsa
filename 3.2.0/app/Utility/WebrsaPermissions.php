<?php
    /**
     * Code source de la classe WebrsaPermissions.
     *
     * PHP 5.3
     *
     * @package app.Utility
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
	App::uses( 'CakeSession', 'Model/Datasource' );
	App::uses( 'ControllerCache', 'Model/Datasource' );
	App::uses( 'SessionAcl', 'SessionAcl.Model/Datasource' );

    /**
	 * La classe WebrsaPermissions contient la logique des permissions de WebRSA,
	 * d'une part au niveau des ACL, d'autre part au niveau de l'accès aux
	 * dossiers RSA, via les adresses du foyer et les zones géographiques auxquelles
	 * est rattaché l'utilisateur connecté.
	 *
	 * Les informations concernant l'utilisateur connecté se trouvent dans la
	 * session et sont accédées via la classe CakeSession.
     *
     * @package app.Utility
     */
	class WebrsaPermissions
	{
		/**
		 * Accès 1 (oui)
		 */
		const ACCES_OUI = 1;

		/**
		 * Accès -1 (non)
		 */
		const ACCES_NON = -1;

		/**
		 * Accès 0 (hérité)
		 */
		const ACCES_HERITE = 0;

		/**
		 * Accès 1 (oui) du parent
		 */
		const HERITE_OUI = 10;

		/**
		 * Accès -1 (non) du parent
		 */
		const HERITE_NON = -10;

		/**
		 * Le chemin vers les données "Acl" dans la session.
		 *
		 * @var string
		 */
		public static $sessionPermissionsKey = 'Auth.Permissions';

		/**
		 * Retourne la liste des idds des structures référentes auxquelles
		 * l'utilisateur est lié.
		 *
		 * @return array
		 */
		protected static function _structuresreferentesUser() {
			return (array)Hash::extract( (array)CakeSession::read( 'Auth.Structurereferente' ), '{n}.id' );
		}

		/**
		 * Vérification, pour l'utilisateur connecté, de l'accès à un contrôleur
		 * et à une action (ACL).
		 *
		 * Cette vérification se fait grâce aux droits stockés dans la session,
		 * sous la clé stockée dans l'attribut $sessionPermissionsKey et tient
		 * compte des attributs $aucunDroit et $commeDroit des contrôleurs.
		 *
		 * @param string $controllerName Le nom du contrôleur
		 * @param string $actionName Le nom de l'action
		 * @return boolean
		 */
		public static function check( $controllerName, $actionName ) {
			$controllerName = Inflector::camelize( $controllerName );
			$return = false;

			if( $controllerName === 'CakeError' ) {
				return true;
			}
			if( ControllerCache::aucunDroit( $controllerName, $actionName ) ) {
				$return = true;
			}
			else {
				$commeDroit = ControllerCache::commeDroit( $controllerName, $actionName );
				if( $commeDroit !== false ) {
					list( $controllerName, $actionName ) = explode( ':', $commeDroit );
				}

				$sessionPermissionsKey = self::$sessionPermissionsKey;
				$permissionAction = SessionAcl::check( "{$sessionPermissionsKey}.controllers/{$controllerName}/{$actionName}" );
				if( !is_null( $permissionAction ) ) {
					$return = $permissionAction;
				}
				else {
					$permissionModule = SessionAcl::check( "{$sessionPermissionsKey}.controllers/{$controllerName}" );

					if( !is_null( $permissionModule ) ) {
						$return = $permissionModule;
					}
				}
			}

			return $return;
		}

		/**
		 * Vérifie, lorsqu'on est limité au niveau des zones géographiques
		 * auxquelles on peut accéder, si l'on peut accéder à la zone désirée.
		 *
		 * @param boolean $filtre_zone_geo Est-on limité au niveau des zones
		 *	géographiques auxquelles on peut accéder ?
		 * @param string $codeinsee Le code INSEE auquel on veut accéder
		 * @param array $mesZonesGeographiques Les zones géographiques auxquelles
		 *	on peut accéder
		 * @return boolean
		 */
		protected static function _checkZoneGeographique( $filtre_zone_geo, $codeinsee, $mesZonesGeographiques ) {
			$return = true;

			if( $filtre_zone_geo ) {
				if( Configure::read( 'CG.cantons' ) ) {
					$Canton = ClassRegistry::init( 'Canton' );
					$cantons = $Canton->find(
						'first',
						array(
							'fields' => array( 'Canton.id' ),
							'joins' => array(
								$Canton->join( 'Zonegeographique' )
							),
							'contain' => false,
							'conditions' => array(
								'Zonegeographique.codeinsee' => $mesZonesGeographiques,
								'Canton.numcom' => $codeinsee,
							)
						)
					);

					$return = !empty( $cantons );
				}
				else {
					$return = in_array( $codeinsee, (array)$mesZonesGeographiques, true );
				}
			}

			return $return;
		}

		/**
		 * Pour tous les conseils généraux, le droit d'accès aux données se base sur
		 * le nom du contrôleur, le nom de l'action (voir la méthode check()) et,
		 * si l'utilisateur est limité au niveau des zones géographiques, au code
		 * INSEE de l'adresse de rang 01.
		 *
		 * Pour le CG 93, si la vérification précédente a échoué, que l'utilisateur
		 * est limité au niveau des zones géographiques, mais que l'action est de
		 * type lecture (valeur 'read' dans l'attribut $crudMap du contrôleur), on
		 * vérifie si néanmoins le code INSEE d'une des adresses de rang 02 ou 03
		 * ne se trouve pas dans les codes INSEE auxquels l'utilisateur a accès.
		 *
		 * @param string $controllerName Le nom du contrôleur
		 * @param string $actionName Le nom de l'action
		 * @param array $dossierData Les données d'un dossier RSA.
		 * @return boolean
		 */
		public static function checkDossier( $controllerName, $actionName, $dossierData ) {
			$controllerName = Inflector::camelize( $controllerName );

			if( !self::check( $controllerName, $actionName ) ) {
				return false;
			}

			$filtre_zone_geo = CakeSession::read( 'Auth.User.filtre_zone_geo' );
			$mesZonesGeographiques = CakeSession::read( 'Auth.Zonegeographique' );

			$codeinsee01 = Hash::get( $dossierData, 'Adressefoyer.01.codeinsee' );
			$codeinsee02 = Hash::get( $dossierData, 'Adressefoyer.02.codeinsee' );
			$codeinsee03 = Hash::get( $dossierData, 'Adressefoyer.03.codeinsee' );

			// Pour le CG 93
			if( Configure::read( 'Cg.departement' ) == 93 ) {
				$isCrudRead = ( ControllerCache::crudMap( $controllerName, $actionName ) == 'read' );

				// on vérifie si l'utilisateur a le droit d'accéder au dossier par-rapport à sa restriction sur les zones géographiques et au code INSEE actuel du foyer
				if( !self::_checkZoneGeographique( $filtre_zone_geo, $codeinsee01, $mesZonesGeographiques ) ) {
					$accesCodeinsee02 = self::_checkZoneGeographique( $filtre_zone_geo, $codeinsee02, $mesZonesGeographiques )
						&& $isCrudRead;

					$accesCodeinsee03 = self::_checkZoneGeographique( $filtre_zone_geo, $codeinsee03, $mesZonesGeographiques )
						&& $isCrudRead;

					// ... ou, si la méthode est de la lecture, et que l'un des codes INSEE précédent est accessible par l'utilisateur
					if( !( $accesCodeinsee02 || $accesCodeinsee03 ) ) {
						return false;
					}
				}
			}
			// Pour le CG 66 lorsque l'utilisateur connecté est référent dans un OA
			else if( Configure::read( 'Cg.departement' ) == 66 && CakeSession::read( 'Auth.User.type' ) == 'externe_ci' ) {
				$structuresreferentesIdsDossier = (array)Hash::filter( (array)Hash::extract( $dossierData, 'Foyer.Personne.{n}.Orientstruct.structurereferente_id' ) );
				$structuresreferentesIdsUser = self::_structuresreferentesUser();
				$intersect = array_intersect( $structuresreferentesIdsUser, $structuresreferentesIdsDossier );

				return (
					$intersect
					&& self::_checkZoneGeographique( $filtre_zone_geo, $codeinsee01, $mesZonesGeographiques )
				);
			}
			// Pour les autres CG, on vérifie si l'utilisateur a le droit d'accéder au dossier par-rapport à sa restriction sur les zones géographiques et au code INSEE actuel du foyer.
			else if( !self::_checkZoneGeographique( $filtre_zone_geo, $codeinsee01, $mesZonesGeographiques ) ) {
				return false;
			}

			return true;
		}

		/**
		 *
		 * @param type $path
		 * @param type $dossierMenu
		 * @return type
		 */
		public static function conditionsDate( $path, $dossierMenu ) {
			$conditions = array();

			$filtre_zone_geo = CakeSession::read( 'Auth.User.filtre_zone_geo' );

			if( Configure::read( 'Cg.departement' ) == 93 && $filtre_zone_geo ) {
				$mesZonesGeographiques = CakeSession::read( 'Auth.Zonegeographique' );

				if( isset( $dossierMenu['Adressefoyer']['01'] ) && in_array( $dossierMenu['Adressefoyer']['01']['codeinsee'], $mesZonesGeographiques ) ) {
					$conditions[] = array( "{$path} >=" =>  $dossierMenu['Adressefoyer']['01']['ddemm'] );
				}

				foreach( array( '02', '03' ) as $rang ) {
					if( isset( $dossierMenu['Adressefoyer'][$rang] ) && in_array( $dossierMenu['Adressefoyer'][$rang]['codeinsee'], $mesZonesGeographiques ) ) {
						$conditions[] = array( "{$path} BETWEEN '{$dossierMenu['Adressefoyer'][$rang]['ddemm']}' AND '{$dossierMenu['Adressefoyer'][$rang]['dfemm']}'" );
					}
				}

				if( !empty( $conditions ) ) {
					$conditions = array( 'OR' => $conditions );
				}
			}

			return $conditions;
		}

		/**
		 * Retourne une condition supplémentaire sur l'id du Dossier suivant le
		 * CG et le type d'utilisateur connecté.
		 *
		 * @param string $dossierIdField
		 * @return string
		 */
		public static function conditionsDossier( $dossierIdField = 'Dossier.id' ) {
			$return = array();

			if( Configure::read( 'Cg.departement' ) == 66 && CakeSession::read( 'Auth.User.type' ) == 'externe_ci' ) {
				$Dossier = ClassRegistry::init( 'Dossier' );

				$sqlDerniereOrientstruct = $Dossier->Foyer->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere( 'personnes.id', 'derniersorientations' );

				$querydata = array(
					'fields' => array( 'foyers.dossier_id' ),
					'alias' => 'foyers',
					'recursive' => -1,
					'joins' => array(
						array_words_replace(
							$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
							array( 'Foyer' => 'foyers', 'Personne' => 'personnes' )
						),
						array_words_replace(
							$Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'INNER' ) ),
							array( 'Personne' => 'personnes', 'Orientstruct' => 'orientsstructs' )
						),
						array_words_replace(
							$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
							array( 'Personne' => 'personnes', 'Prestation' => 'prestations' )
						)
					),
					'conditions' => array(
						"foyers.dossier_id = {$dossierIdField}",
						"orientsstructs.id IN ( {$sqlDerniereOrientstruct} )",
						'orientsstructs.structurereferente_id' => self::_structuresreferentesUser()
					),
				);

				$return = array( $dossierIdField.' IN ( '.$Dossier->Foyer->sq( $querydata ).' )' );
			}

			return $return;
		}

		/**
		 * Vérifie l'accès en modification et suppression des questionnaires D1
		 * et D2 du CG 93.
		 *
		 * On vérifie si les structures référentes auxquelles est attaché l'utilisateur
		 * (clé Auth.Structurereferente en session) sont vides ou correspondent
		 * à la structure référente liée au questionnaire en plus des droits sur
		 * l'action en elle-même (permissions de l'utilisateur).
		 *
		 * @param string|integer $structurereferente_id
		 * @param boolean $permission
		 * @param boolean $string Si vrai, retourne une chaîne qui pourra être évaluée
		 * @return string|boolean
		 */
		public static function checkD1D2( $structurereferente_id, $permission = true, $string = false ) {
			$structuresreferentes = self::_structuresreferentesUser();
			$permission = ( $permission ? '1' : '0' );

			$liste = var_export( $structuresreferentes, true );

			$enabled = "( ( count( $liste ) == 0 || ( in_array( '{$structurereferente_id}', {$liste} ) ) ) && ( '{$permission}' == '1' ) )";

			if( !$string ) {
				$enabled = eval( "return {$enabled};" );
			}

			return $enabled;
		}
	}
?>
