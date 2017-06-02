<?php
	/**
	 * Code source de la classe WebrsaDsp.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractLogic', 'Model');
	App::uses('WebrsaLogicAccessInterface', 'Model/Interface');

	/**
	 * La classe WebrsaDsp possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaDsp extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaDsp';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Dsp', 'DspRev');

		/**
		 * Préfixes des champs liés au catalogue ROME v3.
		 *
		 * @var array
		 */
		public $prefixesRomev3 = array( 'deract', 'deractdomi', 'actrech' );

		/**
		 * Suffixes des champs liés au catalogue ROME v3.
		 *
		 * @var array
		 */
		public $suffixesRomev3 = array( 'famille', 'domaine', 'metier', 'appellation' );

		/**
		 * Liste des alias vers Entreeromev3
		 *
		 * @var array
		 */
		public $romev3LinkedModels = array( 'Deractromev3', 'Deractdomiromev3', 'Actrechromev3' );

		/**
		 * Liste des champs intéressants de Entreeromev3
		 *
		 * @var array
		 */
		public $romev3Fields = array( 'familleromev3_id', 'domaineromev3_id', 'metierromev3_id', 'appellationromev3_id' );

		/**
		 * Liste des modèles liés contenant des valeurs de cases à cocher ainsi
		 * qu'un éventuel champ "autre", pour tous les CG, ainsi que des modèles
		 * liés à un CG en particulier.
		 *
		 * @var array
		 */
		public $checkboxes = array(
			// Tous CG
			'all' => array(
				// Rencontrez vous des difficultés sociales ?
			   'Detaildifsoc' => array(
				   'name' => 'difsoc',
				   'text' => 'libautrdifsoc'
			   ),
			   // Dans quel domaine accompagnement familial ?
			   'Detailaccosocfam' => array(
				   'name' => 'nataccosocfam',
				   'text' => 'libautraccosocfam'
			   ),
			   // Dans quel domaine accompagnement individuel ?
			   'Detailaccosocindi' => array(
				   'name' => 'nataccosocindi',
				   'text' => 'libautraccosocindi'
			   ),
			   // Parmi les propositions suivantes certaines constituent elles des obstacles à une recherche d'emploi ?
			   'Detaildifdisp' => array(
				   'name' => 'difdisp',
				   'text' => false
			   ),
			   // Etes-vous mobile ?
			   'Detailnatmob' => array(
				   'name' => 'natmob',
				   'text' => false
			   ),
			   // Difficultés logement ?
			   'Detaildiflog' => array(
				   'name' => 'diflog',
				   'text' => 'libautrdiflog'
			   )
			),
			// CG 58
			58 => array(
				// Quel moyen de transport ?
				'Detailmoytrans' => array(
					'name' => 'moytrans',
					'text' => 'libautrmoytrans'
				),
				// Quelles sont les difficultés sociales décelées par le professionnel ?
				'Detaildifsocpro' => array(
					'name' => 'difsocpro',
					'text' => 'libautrdifsocpro'
				),
				// Quelles sont les difficultés sociales décelées par le professionnel ?
				'Detailprojpro' => array(
					'name' => 'projpro',
					'text' => 'libautrprojpro'
				),
				// Frein(s) à la formation :
				'Detailfreinform' => array(
					'name' => 'freinform',
					'text' => false
				),
				// Confort :
				'Detailconfort' => array(
					'name' => 'confort',
					'text' => false
				)
			)
		);

		/**
		 * Contient la liste des valeurs "Aucun(e)", pour l'ensemble des CG et
		 * pour chaque CG en particulier des modèles liés par cases à cocher.
		 * Si cette valeur n'existe pas, alors est vaudra null.
		 *
		 * @todo Grouper avec $checkboxes.
		 *
		 * @var array
		 */
		public $checkboxesValuesNone = array(
			// Tous CG
			'all' => array(
				'Detaildifsoc' => '0401',
				'Detailaccosocfam' => null,
				'Detailaccosocindi' => null,
				'Detaildifdisp' => '0501',
				'Detailnatmob' => '2504',
				'Detaildiflog' => '1001'
			),
			// CG 58
			58 => array(
				'Detailmoytrans' => null,
				'Detaildifsocpro' => null,
				'Detailprojpro' => null,
				'Detailfreinform' => null,
				'Detailconfort' => null
			)
		);

		/**
		 * Liste des modèles liés contenant des cases à cocher et utilisés dans
		 * le formulaire de recherche.
		 *
		 * @var array
		 */
		public $searchCheckboxes = array(
			'Detaildifsoc',
			'Detailaccosocindi',
			'Detaildifdisp',
		);

		/**
		 * Liste des alias codes ROME V2 (CG 66).
		 *
		 * @var array
		 */
		public $modelesRomeV2 = array(
			'Libsecactderact66Secteur',
			'Libderact66Metier',
			'Libsecactdomi66Secteur',
			'Libactdomi66Metier',
			'Libsecactrech66Secteur',
			'Libemploirech66Metier'
		);

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			return $query;
		}

		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$query = array(
				'fields' => array(
					'DspRev.id',
					'DspRev.personne_id',
				),
				'conditions' => $conditions,
				'contain' => false
			);
			$bases = $this->DspRev->find('all', array('contain' => false, 'conditions' => $conditions));
			$ids = (array)Hash::extract($bases, '{n}.DspRev.id');
			$personne_ids = array_unique((array)Hash::extract($bases, '{n}.DspRev.personne_id'));

			$results = array();
			foreach ($personne_ids as $personne_id) {
				$query = $this->completeVirtualFieldsForAccess($this->getViewQuery());
				$query['conditions'] = array('DspRev.personne_id' => $personne_id);
				$query['order'] = array('DspRev.created DESC', 'DspRev.id DESC');

				$histos = $this->DspRev->find('all', $query);

				$count = count($histos);
				$histos[$count-1]['diff'] = 0;
				$prev = $histos[$count-1];

				for ($i = $count-2 ; $i >= 0 ; $i--) {
					$delta = $this->getDiffs($prev, $histos[$i]);
					$diff = count(Hash::flatten($delta));
					$prev = $histos[$i];
					$histos[$i]['diff'] = $diff;
				}

				foreach ($histos as $histo) {
					if (in_array(Hash::get($histo, 'DspRev.id'), $ids)) {
						$results[] = $histo;
					}
				}
			}

			return $results;
		}

		/**
		 * Retourne un querydata contenant tous les champs et les associations à
		 * utiliser dans les pages de visualisation d'une DspRev, dans la page
		 * d'historique des DspRev, dans la page de différences entre deux versions
		 * des DspRev.
		 *
		 * @return array
		 */
		public function getViewQuery() {
			$cacheKey = Inflector::underscore($this->DspRev->useDbConfig).'_'.Inflector::underscore($this->DspRev->alias).'_'.Inflector::underscore(__FUNCTION__);
			$query = Cache::read($cacheKey);

			if ($query === false) {
				$query = array(
					'fields' => $this->DspRev->fields(),
					'contain' => array(
						'Personne',
						'DetaildifsocRev',
						'DetailaccosocfamRev',
						'DetailaccosocindiRev',
						'DetaildifdispRev',
						'DetailnatmobRev',
						'DetaildiflogRev',
						'DetailmoytransRev',
						'DetaildifsocproRev',
						'DetailprojproRev',
						'DetailfreinformRev',
						'DetailconfortRev',
						'Fichiermodule'
					),
					'joins' => array()
				);

				foreach (array_keys($this->DspRev->belongsTo) as $alias) {
					if (in_array($alias, $query['contain'])) {
						$query['fields'] = array_merge($query['fields'], $this->DspRev->{$alias}->fields());
					}
					// Codes ROME V2
					elseif (preg_match('/66(Metier|Secteur)/', $alias)) {
						$key = array_search("{$this->DspRev->alias}.{$this->DspRev->belongsTo[$alias]['foreignKey']}", $query['fields']);
						if ($key !== -1) {
							unset($query['fields'][$key]);
						}

						$field = $this->DspRev->{$alias}->getVirtualField('intitule');
						$query['fields'][] = "({$field}) \"{$alias}__intitule\"";
						$query['joins'][] = $this->DspRev->join($alias, array('type' => 'LEFT OUTER'));
					}
				}

				if (Configure::read('Romev3.enabled')) {
					foreach ($this->romev3LinkedModels as $alias) {
						$aliasRev = "{$alias}Rev";
						$replacements = array();

						$query['joins'][] = $this->DspRev->join($aliasRev);

						$fields = array();
						foreach ($this->suffixesRomev3 as $suffix) {
							$prefix = preg_replace('/^(.*)romev3Rev$/', "\\1", $aliasRev);

							$linked = Inflector::camelize("{$suffix}romev3");
							$linkedAlias = "{$prefix}{$suffix}romev3Rev";
							$replacements[$linked] = $linkedAlias;

							$query['joins'][] = array_words_replace($this->DspRev->{$aliasRev}->join($linked), $replacements);

							switch($suffix) {
								case 'famille':
									$fields[] = "(\"{$linkedAlias}\".\"code\" || ' - ' || \"{$linkedAlias}\".\"name\") AS \"{$linkedAlias}__name\"";
									break;
								case 'domaine':
									$fields[] = "(\"{$prefix}familleromev3Rev\".\"code\" || \"{$linkedAlias}\".\"code\" || ' - ' || \"{$linkedAlias}\".\"name\") AS \"{$linkedAlias}__name\"";
									break;
								case 'metier':
									$fields[] = "(\"{$prefix}familleromev3Rev\".\"code\" || \"{$prefix}domaineromev3Rev\".\"code\" || \"{$linkedAlias}\".\"code\" || ' - ' || \"{$linkedAlias}\".\"name\") AS \"{$linkedAlias}__name\"";
									break;
								case 'appellation':
									$fields[] = "{$linkedAlias}.name";
									break;
							}
						}
						$query['fields'] = Hash::merge($query['fields'], $fields);
					}
				}

				Cache::write($cacheKey, $query);
			}

			return $query;
		}

		/**
		 * Permet d'obtenir les différences entre deux versions des DspRev obtenues
		 * grâce au query se trouvant dans la méthode getViewQuery().
		 *
		 * @param array $old
		 * @param array $new
		 * @return array
		 */
		public function getDiffs($old, $new) {
			$return = array();

			// Suppression des champs de clés primaires et étrangères des résultats des Dsps actuelles
			foreach ($new as $Model => $values) {
				if ($Model != 'DspRev' && preg_match('/Rev$/', $Model)) {
					foreach ($new[$Model] as $key1 => $value1) {
						if (is_array($new[$Model][$key1])) {
							$new[$Model][$key1] = Hash::remove($new[$Model][$key1], "id");
							$new[$Model][$key1] = Hash::remove($new[$Model][$key1], "dsp_rev_id");
						}
					}
				}
			}

			// Suppression des champs de clés primaires et étrangères des résultats des Dsps précédentes
			foreach ($old as $Model => $values) {
				if ($Model != 'DspRev' && preg_match('/Rev$/', $Model)) {
					foreach ($old[$Model] as $key2 => $value2) {
						if (is_array($old[$Model][$key2])) {
							$old[$Model][$key2] = Hash::remove($old[$Model][$key2], "id");
							$old[$Model][$key2] = Hash::remove($old[$Model][$key2], "dsp_rev_id");
						}
					}
				}
			}

			// Suppression des champs de clés primaires et étrangères des codes ROME V3 liés
			if (Configure::read('Romev3.enabled')) {
				foreach ($this->romev3LinkedModels as $alias) {
					$foreignKey = Inflector::underscore($alias).'_id';
					unset($old["DspRev"][$foreignKey]);
					unset($new["DspRev"][$foreignKey]);

					foreach (array_keys($this->Dsp->Deractromev3->schema()) as $fieldName) {
						unset($old["{$alias}Rev"][$fieldName]);
						unset($new["{$alias}Rev"][$fieldName]);
					}
				}
			}

			// -----------------------------------------------------------------

			foreach ($new as $Model => $values) {
				$return[$Model] = Set::diff($new[$Model], $old[$Model]);
				unset($return[$Model]['id']);
				unset($return[$Model]['created']);
				unset($return[$Model]['modified']);

				if ($Model != 'DspRev' && !empty($new[$Model]) && !empty($return[$Model]) && preg_match('/Rev$/', $Model)) {
					foreach ($new[$Model] as $key1 => $value1) {
						foreach ($old[$Model] as $key2 => $value2) {
							$compare = Set::diff($value1, $value2);
							if (empty($compare) && ($key1 != $key2)) {
								$return[$Model] = Hash::remove($return[$Model], $key1);
							}
						}
					}
				}

				if (empty($return[$Model])) {
					$return = Hash::remove($return, $Model);
				}
			}

			// Suppression des fausses différences trouvées au niveau des libellés vides
			foreach ($this->getCheckboxes() as $alias => $params) {
				if ($params['text'] !== false) {
					$alias = "{$alias}Rev";
					$path = "{$alias}.{n}.{$params['text']}";

					if (Hash::extract($return, $path) === array(null)) {
						$return = Hash::remove($return, $path);
					}
				}
				if (empty($return[$Model])) {
					$return = Hash::remove($return, $Model);
				}
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
			$results = $this->getViewQuery();
			$success = !empty($results);

			return $success;
		}

		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 *
		 * @see WebrsaAccess::getParamsList
		 * @param integer $personne_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($personne_id, array $params = array()) {
			$results = array();

			if (in_array('ajoutPossible', $params)) {
				$results['ajoutPossible'] = $this->ajoutPossible($personne_id);
			}

			return $results;
		}

		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 *
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function ajoutPossible($personne_id) {
			return true;
		}

		/**
		 * Filtre les options disponibles en fonction du CG.
		 * Utilisé pour limiter certaines valeurs au CG 58 et aux autres CG.
		 *
		 * @param array $options
		 * @return array
		 */
		public function getFilteredOptions( $options ) {
			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$values = array( '0502', '0503', '0504' );
			}
			else {
				$values = array( '0507', '0508', '0509', '0510', '0511', '0512', '0513', '0514' );
			}

			foreach( $values as $value ) {
				unset( $options['Detaildifdisp']['difdisp'][$value] );
			}

			return $options;
		}

		/**
		 * Permet de récupérer les dernières DSP d'une personne, en attendant
		 * l'index unique sur personne_id.
		 *
		 * @param string $personneIdFied
		 * @return string
		 */
		public function sqDerniereDsp( $personneIdFied = 'Personne.id' ) {
			$query = array(
				'alias' => 'dsps',
				'fields' => array( 'dsps.id' ),
				'conditions' => array(
					"dsps.personne_id = {$personneIdFied}"
				),
				'contain' => false,
				'order' => array( 'dsps.id DESC' ),
				'limit' => 1
			);

			return $this->Dsp->sq( $query );
		}

		/**
		 * Retourne la liste des valeurs "Aucun(e)" pour chacun des modèles liés
		 * par cases à cocher, suivant le CG connecté.
		 *
		 * @return array
		 */
		public function getCheckboxesValuesNone() {
			return Hash::merge(
				(array)Hash::get( $this->checkboxesValuesNone, 'all' ),
				(array)Hash::get( $this->checkboxesValuesNone, Configure::read( 'Cg.departement' ) )
			);
		}

		/**
		 * Retourne les modèles liés par cases à cocher ainsi que pour chacun
		 * d'eux, les champs intitulé et libellé autre, suivant le CG connecté.
		 *
		 * @return array
		 */
		public function getCheckboxes() {
			return Hash::merge(
				(array)Hash::get( $this->checkboxes, 'all' ),
				(array)Hash::get( $this->checkboxes, Configure::read( 'Cg.departement' ) )
			);
		}

		/**
		 * Retourne les champs virtuels des modèles liés par cases à cocher.
		 *
		 * @see getCheckboxes()
		 * @return array
		 */
		public function getCheckboxesVirtualFields() {
			$checkboxes = $this->getCheckboxes();
			$return = array();

			foreach( $checkboxes as $modelName => $params ) {
				$return[] = "{$modelName}.{$params['name']}";
			}

			return $return;
		}

		/**
		 * Tentative de sauvegarde de certains champs des Dsp, soit par la création
		 * d'une Dsp (si l'allocataire ne possède ni Dsp, ni DspRev), soit par
		 * création d'une DspRev avec reprise des données de la Dsp (s'il ne
		 * possédait aucune DspRev), soit par création d'une nouvelle version des
		 * DspRev avec reprise des données de la dernière DspRev (s'il existait
		 * déjà une DspRev).
		 *
		 * Si on n'envoie que des données null ou chaîne vide, on n'essairea pas
		 * d'enregistrer des données mais la méthode retournera true.
		 *
		 * @param integer $personne_id
		 * @param array $data ATTENTION: le nom de modèle sera toujours Dsp
		 * @return boolean
		 * @throws RuntimeException
		 */
		public function updateDerniereDsp( $personne_id, array $data ) {
			$fields = array_keys( (array)Hash::get( $data, 'Dsp' ) );
			$success = true;

			$query = array(
				'fields' => array(
					'Dsp.id',
					'DspRev.id',
				),
				'contain' => false,
				'joins' => array(
					$this->Dsp->Personne->join(
						'Dsp',
						array(
						'type' => 'LEFT OUTER',
						'conditions' => array(
							'Dsp.id IN ( '.$this->sqDerniereDsp( 'Personne.id' ).' )'
						)
							)
					),
					$this->Dsp->Personne->join(
						'DspRev',
						array(
						'type' => 'LEFT OUTER',
						'conditions' => array(
							'DspRev.id IN ( '.$this->Dsp->Personne->DspRev->sqDerniere( 'Personne.id' ).' )'
						)
							)
					),
				),
				'conditions' => array(
					'Personne.id' => $personne_id
				)
			);

			foreach( $fields as $field ) {
				$query['fields'][] = "Dsp.{$field}";
				$query['fields'][] = "DspRev.{$field}";
			}

			$oldRecord = $this->Dsp->Personne->find( 'first', $query );

			$newRecord = array();
			$newModelName = null;

			// Si on a des DspRev
			if( !empty( $oldRecord['DspRev']['id'] ) ) {
				// Si on a des différences dans les données
				$equal = true;
				foreach( $fields as $field ) {
					$equal = ( $oldRecord['DspRev'][$field] == $data['Dsp'][$field] ) && $equal;
				}
				if( !$equal ) {
					$oldModelName = 'DspRev';
					$newModelName = 'DspRev';
					$linkedSuffix = '';
				}
			}
			// Pas de DspRev mais une Dsp
			else if( !empty( $oldRecord['Dsp']['id'] ) ) {
				// Si on a des différences dans les données
				$equal = true;
				foreach( $fields as $field ) {
					$equal = ( $oldRecord['Dsp'][$field] == $data['Dsp'][$field] ) && $equal;
				}
				if( !$equal ) {
					$oldModelName = 'Dsp';
					$newModelName = 'DspRev';
					$linkedSuffix = 'Rev';
				}
			}
			// S'il faut créer un enregistrement de Dsp parce que l'on a des données non vides
			else {
				$allNull = true;
				foreach( $fields as $field ) {
					$allNull = empty( $data['Dsp'][$field] ) && $allNull;
				}
				if( !$allNull ) {
					$oldModelName = 'Dsp';
					$newModelName = 'Dsp';
					$linkedSuffix = '';
				}
			}

			if( $newModelName !== null ) {
				// Début
				$removePaths = array(
					"{$oldModelName}.id",
					"{$oldModelName}.created",
					"{$oldModelName}.modified",
				);
				$replacements = array( 'Dsp' => 'DspRev' );

				$query = array(
					'contain' => array(),
					'conditions' => array(
						"{$oldModelName}.id" => $oldRecord[$oldModelName]['id']
					)
				);

				// Modèles liés aux modèle Dsp/DspRev
				foreach( $this->Dsp->Personne->{$oldModelName}->hasMany as $alias => $params ) {
					if( strstr( $alias, 'Detail' ) !== false ) {
						$query['contain'][] = $alias;

						$removePaths[] = "{$alias}.{n}.id";
						$removePaths[] = "{$alias}.{n}.{$params['foreignKey']}";

						$replacements[$alias] = "{$alias}{$linkedSuffix}";
					}
				}
				$newRecord = $this->Dsp->Personne->{$oldModelName}->find( 'first', $query );

				foreach( $removePaths as $removePath ) {
					$newRecord = Hash::remove( $newRecord, $removePath );
				}

				$newRecord = array_words_replace( $newRecord, $replacements );
				$newRecord[$newModelName]['personne_id'] = $personne_id;
				$newRecord[$newModelName]['dsp_id'] = Hash::get( $oldRecord, 'Dsp.id' );

				foreach( $fields as $field ) {
					$newRecord[$newModelName][$field] = Hash::get( $data, "Dsp.{$field}" );
				}

				// Pour les DspRev, le champ haspiecejointe est obligatoire
				if( $newModelName === 'DspRev' && !isset( $newRecord[$newModelName]['haspiecejointe'] ) ) {
					$newRecord[$newModelName]['haspiecejointe'] = '0';
				}

				// Si on utilise les codes ROME v.3, il y aura de la copie de données à faire
				foreach( $this->romev3LinkedModels as $linkedModelName ) {
					$linkedFieldName = Inflector::underscore( $linkedModelName ).'_id';
					$value = Hash::get( $newRecord, "{$newModelName}.{$linkedFieldName}" );

					if( !empty( $value ) ) {
						$query = array( 'conditions' => array( "{$linkedModelName}.id" => $value ), 'contain' => false );
						$record = $this->Dsp->{$linkedModelName}->find( 'first', $query );

						if( empty($record) ) {
							$msg = sprintf( 'Impossible de trouver l\'enregistrement de %s d\'id %d.', $linkedModelName, $value );
							throw new RuntimeException( $msg );
						}

						foreach( array( 'id', 'created', 'modified' ) as $field ) {
							unset( $record[$linkedModelName][$field] );
						}

						$this->Dsp->{$linkedModelName}->create( $record );
						$success = $this->Dsp->{$linkedModelName}->save( null, array( 'atomic' => false ) ) && $success;

						$newRecord[$newModelName][$linkedFieldName] = $this->Dsp->{$linkedModelName}->id;
					}
				}

				$success = $this->Dsp->saveResultAsBool(
								$this->Dsp->Personne->{$newModelName}->saveAll(
						$newRecord,
						array( 'atomic' => false, 'deep' => true )
								)
						) && $success;
			}

			return $success;
		}

		/**
		 * Retourne les options à utiliser dans le moteur de recherche, le
		 * formulaire d'ajout / de modification, etc.. suivant le CG connecté.
		 *
		 * Il est possible d'aliaser le modèle Dsp.
		 *
		 * $params par défaut: array(
		 *	 'find' => true,
		 *	 'allocataire' => false,
		 *	 'alias' => 'Dsp'
		 * )
		 *
		 * @param array $params
		 * @return array
		 */
		public function options( array $params = array() ) {
			$params = $params + array( 'find' => true, 'allocataire' => false, 'alias' => 'Dsp', 'enums' => true );

			$cacheKey = Inflector::underscore( $this->Dsp->useDbConfig ).'_'.Inflector::underscore( $this->Dsp->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $params ) );
			$return = Cache::read( $cacheKey );

			if( $return === false ) {
				$Catalogueromev3 = ClassRegistry::init( 'Catalogueromev3' );

				$return = array();

				if( $params['enums'] ) {
					$return = Hash::merge( $return, $this->Dsp->enums() );
					foreach( array_keys( $this->getCheckboxes() ) as $modelDetail ) {
						$return = Hash::merge( $return, $this->Dsp->{$modelDetail}->enums() );
					}
				}

				if( $params['allocataire'] ) {
					$return = Hash::merge(
						$return,
						ClassRegistry::init( 'Allocataire' )->options()
					);
				}

				if( $params['find'] ) {
					foreach( $this->romev3LinkedModels as $alias ) {
						$return = Hash::merge(
							$return,
							$this->Dsp->{$alias}->options()
						);
					}
				}

				// Codes ROME V2
				if( Configure::read( 'Cg.departement' ) == 66 ) {
					// Coderomesecteurdsp66
					$query = array(
						'fields' => array(
							'Libsecactderact66Secteur.id',
							'Libsecactderact66Secteur.intitule'
						),
						'contain' => false,
						'order' => array( 'Libsecactderact66Secteur.code' )
					);
					$results = $this->Dsp->Libsecactderact66Secteur->find( 'all', $query );
					$results = array( $this->Dsp->Libsecactderact66Secteur->name => (array)Hash::combine( $results, '{n}.Libsecactderact66Secteur.id', '{n}.Libsecactderact66Secteur.intitule' ) );
					$return = Hash::merge( $return, $results );

					// Coderomemetierdsp66
					$query = array(
						'fields' => array(
							'( "Libderact66Metier"."coderomesecteurdsp66_id" || \'_\' || "Libderact66Metier"."id" ) AS "Libderact66Metier__id"',
							'Libderact66Metier.intitule'
						),
						'contain' => false,
						'order' => array( 'Libderact66Metier.code' )
					);
					$results = $this->Dsp->Libderact66Metier->find( 'all', $query );
					$results = array( $this->Dsp->Libderact66Metier->name => (array)Hash::combine( $results, '{n}.Libderact66Metier.id', '{n}.Libderact66Metier.intitule' ) );
					$return = Hash::merge( $return, $results );
				}

				$return = $this->getFilteredOptions( $return );

				if( $params['alias'] !== 'Dsp' && isset( $return['Dsp'] ) ) {
					$return[$params['alias']] = $return['Dsp'];
					unset( $return['Dsp'] );
				}

				Cache::write( $cacheKey, $return );

				$models = $Catalogueromev3->modelesParametrages;
				if( Configure::read( 'Cg.departement' ) == 66 ) {
					$models = array_merge(
						$models,
						array(
							$this->Dsp->Libderact66Metier->name,
							$this->Dsp->Libsecactderact66Secteur->name
						)
					);
				}
				ModelCache::write( $cacheKey, $models );
			}

			$correspondances = array(
				'Detaildifsoc' => 'difsoc',
				'Detailaccosocindi' => 'nataccosocindi',
				'Detaildifdisp' => 'difdisp'
			);
			foreach( $correspondances as $alias => $fieldName ) {
				if( isset( $return[$alias][$fieldName] ) && !empty( $return[$alias][$fieldName] ) ) {
					$return['Donnees'][$fieldName] =& $return[$alias][$fieldName];
				}
			}

			return $return;
		}
	}