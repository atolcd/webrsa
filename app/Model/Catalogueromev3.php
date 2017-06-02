<?php
	/**
	 * Code source de la classe Catalogueromev3.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Catalogueromev3 ...
	 *
	 * @package app.Model
	 */
	class Catalogueromev3 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Catalogueromev3';

		/**
		 * Ce modèle n'utilise pas de table.
		 *
		 * @var integer
		 */
		public $useTable = false;

		/**
		 * Liste des modèles de paramétrages des codes ROME V3.
		 *
		 * @var array
		 */
		public $modelesParametrages = array(
			'Familleromev3',
			'Domaineromev3',
			'Metierromev3',
			'Appellationromev3',
			//'Correspondanceromev2v3'
		);

		/**
		 * Retourne des listes d'options dépendantes pour le catalogue ROME V3.
		 *
		 * @todo Voir les méthodes options de ces différents modèles
		 *
		 * @return array
		 */
		public function dependantSelects() {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$return = Cache::read( $cacheKey );

			if( $return === false ) {
				$return = array();
				$Familleromev3 = ClassRegistry::init( 'Familleromev3' );

				// 1. Codes familles
				$query = array(
					'fields' => array(
						'Familleromev3.id',
						'( "Familleromev3"."code" || \' - \' || "Familleromev3"."name" ) AS "Familleromev3__name"',
					),
					'order' => array( 'Familleromev3.code ASC' )
				);
				$famillesromesv3 = $Familleromev3->find( 'all', $query );
				$return[$this->alias]['familleromev3_id'] = Hash::combine( $famillesromesv3, '{n}.Familleromev3.id', '{n}.Familleromev3.name' );

				// 2. Codes domaines
				$query = array(
					'fields' => array(
						'( "Familleromev3"."id" || \'_\' || "Domaineromev3"."id" ) AS "Domaineromev3__id"',
						'( "Familleromev3"."code" || "Domaineromev3"."code"  || \' - \' || "Domaineromev3"."name" ) AS "Domaineromev3__name"',
					),
					'joins' => array(
						$Familleromev3->Domaineromev3->join( 'Familleromev3', array( 'type' => 'INNER' ) )
					),
					'order' => array(
						'Familleromev3.code ASC',
						'Domaineromev3.code ASC'
					)
				);
				$domainesromesv3 = $Familleromev3->Domaineromev3->find( 'all', $query );
				$return[$this->alias]['domaineromev3_id'] = Hash::combine( $domainesromesv3, '{n}.Domaineromev3.id', '{n}.Domaineromev3.name' );

				// 3. Codes métiers
				$query = array(
					'fields' => array(
						'( "Metierromev3"."domaineromev3_id" || \'_\' || "Metierromev3"."id" ) AS "Metierromev3__id"',
						'( "Familleromev3"."code" || "Domaineromev3"."code" || "Metierromev3"."code" || \' - \' || "Metierromev3"."name" ) AS "Metierromev3__name"',
					),
					'joins' => array(
						$Familleromev3->Domaineromev3->Metierromev3->join( 'Domaineromev3', array( 'type' => 'INNER' ) ),
						$Familleromev3->Domaineromev3->join( 'Familleromev3', array( 'type' => 'INNER' ) )
					),
					'order' => array(
						'Familleromev3.code ASC',
						'Domaineromev3.code ASC',
						'Metierromev3.code ASC'
					)
				);
				$metiersromesv3 =$Familleromev3->Domaineromev3->Metierromev3->find( 'all', $query );
				$return[$this->alias]['metierromev3_id'] = Hash::combine( $metiersromesv3, '{n}.Metierromev3.id', '{n}.Metierromev3.name' );

				// 4. Appellations
				$query = array(
					'fields' => array(
						'( "Appellationromev3"."metierromev3_id" || \'_\' || "Appellationromev3"."id" ) AS "Appellationromev3__id"',
						'Appellationromev3.name',
					),
					'joins' => array(
						$Familleromev3->Domaineromev3->Metierromev3->Appellationromev3->join( 'Metierromev3', array( 'type' => 'INNER' ) ),
						$Familleromev3->Domaineromev3->Metierromev3->join( 'Domaineromev3', array( 'type' => 'INNER' ) ),
						$Familleromev3->Domaineromev3->join( 'Familleromev3', array( 'type' => 'INNER' ) )
					),
					'order' => array(
						'Familleromev3.code ASC',
						'Domaineromev3.code ASC',
						'Metierromev3.code ASC',
						'Appellationromev3.name ASC'
					)
				);
				$appellationsromesv3 =$Familleromev3->Domaineromev3->Metierromev3->Appellationromev3->find( 'all', $query );
				$return[$this->alias]['appellationromev3_id'] = Hash::combine( $appellationsromesv3, '{n}.Appellationromev3.id', '{n}.Appellationromev3.name' );

				Cache::write( $cacheKey, $return );
				ModelCache::write( $cacheKey, $this->modelesParametrages );
			}

			return $return;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les méthodes qui ne font rien.
		 */
		public function prechargement() {
			$results = $this->dependantSelects();
			return !empty( $results );
		}
	}
?>