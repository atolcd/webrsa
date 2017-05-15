<?php
	/**
	 * Code source de la classe WebrsaStructurereferentelieeBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe WebrsaStructurereferentelieeBehavior fournit des méthodes
	 * utilitaires permettant d'ajouter des champs ou des conditions à un querydata
	 * lorsque le modèle métier auquel il est attaché est lié à une structure
	 * référente ou à un référent.
	 *
	 * @todo liens indirects, comme Questionnaired1pdv93, Questionnaired2pdv93, ...
	 *
	 * @package app.Model.Behavior
	 */
	class WebrsaStructurereferentelieeBehavior extends ModelBehavior
	{
		/**
		 * "Live" cache par nom de modèle et de méthode.
		 *
		 * @var array
		 */
		protected $_cache = array();

		/**
		 * Retourne un champ virtuel aliasé ou une condition indiquant si la
		 * structure référente à laquelle sont liés les enregistrements du modèle
		 * fait partie des structures référentes passées en paramètre.
		 *
		 * @param Model $Model Le modèle auquel le behavior est attaché
		 * @param string $fieldName Le champ par lequel la table est liée à la
		 *	table structuresreferentes
		 * @param array $structuresreferentes_ids Les ids des structures référentes
		 *	à vérifier
		 * @param array $params La clé "alias" (par défaut Referent.horszone)
		 *	permet de spécifier un alias pouyr le champ; une valeur false ou null
		 *	permet de ne pas en spécifier
		 * @return string
		 */
		public function structurereferenteHorszone( Model $Model, $fieldName, array $structuresreferentes_ids, array $params = array() ) {
			$params += array(
				'alias' => 'Referent.horszone'
			);

			if( false === empty( $structuresreferentes_ids ) ) {
				list( $modelName, $fieldName ) = model_field( $fieldName );
				$Dbo = $Model->getDataSource();

				$result = $Dbo->conditions(
					array(
						'NOT' => array( "{$modelName}.{$fieldName}" => $structuresreferentes_ids )
					),
					true,
					false,
					$Model
				);
			}
			else {
				$result = 'FALSE';
			}

			if( false === empty( $params['alias'] ) ) {
				list( $modelName, $fieldName ) = model_field( $params['alias'] );
				return "( {$result} ) AS \"{$modelName}__{$fieldName}\"";
			}

			return $result;
		}

		/**
		 *
		 * Retourne un champ virtuel aliasé ou une condition indiquant si le
		 * référent auquel sont liés les enregistrements du modèle appartient aux
		 * structures référentes passées en paramètre.
		 *
		 * @param Model $Model Le modèle auquel le behavior est attaché
		 * @param string $fieldName Le champ par lequel la table est liée à la
		 *	table referents
		 * @param array $structuresreferentes_ids Les ids des structures référentes
		 *	à vérifier
		 * @param array $params La clé "alias" (par défaut Referent.horszone)
		 *	permet de spécifier un alias pouyr le champ; une valeur false ou null
		 *	permet de ne pas en spécifier.
		 * @return string
		 */
		public function referentHorszone( Model $Model, $fieldName, array $structuresreferentes_ids, array $params = array() ) {
			$params += array(
				'alias' => 'Referent.horszone'
			);

			if( false === empty( $structuresreferentes_ids ) ) {
				list( $modelName, $fieldName ) = model_field( $fieldName );
				$Dbo = $Model->getDataSource();
				$Referent = ClassRegistry::init( 'Referent' );

				$subQuery = array(
					'fields' => array( 'Referent.id' ),
					'conditions' => array(
						"Referent.id = {$modelName}.{$fieldName}",
						'NOT' => array(
							'Referent.structurereferente_id' => $structuresreferentes_ids
						)
					),
					'contain' => false
				);
				$sql = words_replace( $Referent->sq( $subQuery ), array( 'Referent' => 'referents' ) );

				$result = "\"{$modelName}\".\"{$fieldName}\" IN ( {$sql} )";
			}
			else {
				$result = 'FALSE';
			}

			if( false === empty( $params['alias'] ) ) {
				list( $modelName, $fieldName ) = model_field( $params['alias'] );
				return "( {$result} ) AS \"{$modelName}__{$fieldName}\"";
			}

			return $result;
		}

		/**
		 * Complète un querydata avec un champ virtuel indiquant si l'enregistrement
		 * est lié (via la structure référente ou le référent) aux structures
		 * référentes passées en paramètres.
		 *
		 * @param Model $Model Le modèle auquel le behavior est attaché.
		 * @param array $query Le querydata à compléter
		 * @param array $structuresreferentes_ids Les ids des structures référentes
		 *	à vérifier
		 * @param array $params La clé "alias" (par défaut Referent.horszone)
		 *	permet de spécifier un alias pouyr le champ; une valeur false ou null
		 *	permet de ne pas en spécifier; les clés "structurereferente_id" et
		 *	"referent_id" permettent de spécifier quel(s) champ(s) contiennent
		 *	les clés étrangères vers les tables structuresreferentes et referents.
		 * @return array
		 */
		public function completeQueryHorsZone( Model $Model, array $query, $structuresreferentes_ids = null, array $params = array() ) {
			$structuresreferentes_ids = (array)$structuresreferentes_ids;
			$params += array(
				'structurereferente_id' => null,
				'referent_id' => null,
				'alias' => 'Referent.horszone'
			);

			if( true !== empty( $params['structurereferente_id'] ) ) {
				$query['fields'][] = $this->structurereferenteHorszone( $Model, "{$Model->alias}.{$params['structurereferente_id']}", $structuresreferentes_ids, $params );
			}
			else if( true !== empty( $params['referent_id'] ) ) {
				$query['fields'][] = $this->referentHorszone( $Model, "{$Model->alias}.{$params['referent_id']}", $structuresreferentes_ids, $params );
			}
			else if( false === empty( $params['alias'] ) ) {
				list( $modelName, $fieldName ) = model_field( $params['alias'] );
				$query['fields'][] = "NULL AS \"{$modelName}__{$fieldName}\"";
			}

			return $query;
		}

		/**
		 * Retourne les clés étrangères vers les tables structuresreferentes et
		 * referents, dans les clés "structurereferente_id" et "referent_id".
		 *
		 * @param Model $Model Le modèle auquel le behavior est attaché.
		 * @return array
		 */
		public function links( Model $Model ) {
			if( false === isset( $this->_cache[$Model->name][__FUNCTION__] ) ) {
				if( false === $Model->Behaviors->attached( 'Postgres.PostgresTable' ) ) {
					$Model->Behaviors->attach( 'Postgres.PostgresTable' );
				}

				$foreignKeys = $Model->getPostgresForeignKeysFrom();
				$links = Hash::combine( $foreignKeys, '{s}.From.column', '{s}.To.table' );

				$this->_cache[$Model->name][__FUNCTION__] = array(
					'structurereferente_id' => array_search( 'structuresreferentes', $links ),
					'referent_id' => array_search( 'referents', $links )
				);
			}

			return $this->_cache[$Model->name][__FUNCTION__];
		}
	}
?>