<?php
	/**
	 * Code source de la classe WebrsaRechercheDossierpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheDossierpcg66 ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheDossierpcg66 extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheDossierpcg66';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryDossierspcgs66.search.fields',
			'ConfigurableQueryDossierspcgs66.search.innerTable',
			'ConfigurableQueryDossierspcgs66.exportcsv',
			'ConfigurableQueryDossierspcgs66.search_gestionnaire.fields',
			'ConfigurableQueryDossierspcgs66.search_gestionnaire.innerTable',
			'ConfigurableQueryDossierspcgs66.exportcsv_gestionnaire'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Dossierpcg66',
			'Canton',
		);

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$cgDepartement = Configure::read( 'Cg.departement' );

			$types += array(
				// INNER
				'Foyer' => 'INNER',
				'Dossier' => 'INNER',
				'Situationdossierrsa' => 'LEFT OUTER',
				
				// LEFT
				'Personne' => 'LEFT OUTER',
				'User' => 'LEFT OUTER',
				'Personnepcg66' => 'LEFT OUTER',
				'Traitementpcg66' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
				'Decisiondossierpcg66' => 'LEFT OUTER',
				'Decisionpdo' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'Detailcalculdroitrsa' => 'LEFT OUTER',
				'Poledossierpcg66' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Categoriesecteurromev2' => 'LEFT OUTER',
				'Categoriemetierromev2' => 'LEFT OUTER',
				'Categorieromev3' => 'LEFT OUTER',
				'Familleromev3' => 'LEFT OUTER',
				'Domaineromev3' => 'LEFT OUTER',
				'Metierromev3' => 'LEFT OUTER',
				'Appellationromev3' => 'LEFT OUTER',
				
				'Calculdroitrsa' => 'LEFT OUTER',
				'Prestation' => 'LEFT OUTER',
				'Serviceinstructeur' => 'LEFT OUTER',
				'Originepdo' => 'LEFT OUTER',
				'Typepdo' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Dossierpcg66' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Dossierpcg66,
							$this->Dossierpcg66->Foyer->Personne->PersonneReferent,
							$this->Dossierpcg66->User,
							$this->Dossierpcg66->Poledossierpcg66,
							$this->Dossierpcg66->Decisiondossierpcg66,
							$this->Dossierpcg66->Serviceinstructeur,
							$this->Dossierpcg66->Originepdo,
							$this->Dossierpcg66->Typepdo,
							$this->Dossierpcg66->Personnepcg66->Traitementpcg66,
							$this->Dossierpcg66->Decisiondossierpcg66->Decisionpersonnepcg66->Decisionpdo,
							$this->Dossierpcg66->Personnepcg66->Categorieromev3,
							$this->Dossierpcg66->Personnepcg66->Categorieromev3->Familleromev3,
							$this->Dossierpcg66->Personnepcg66->Categorieromev3->Domaineromev3,
							$this->Dossierpcg66->Personnepcg66->Categorieromev3->Metierromev3,
							$this->Dossierpcg66->Personnepcg66->Categorieromev3->Appellationromev3,
							$this->Dossierpcg66->Personnepcg66->Categoriemetierromev2,
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Dossierpcg66.id',
						'Dossierpcg66.foyer_id',
						'Decisiondossierpcg66.id',

						'Personnepcg66.noms_complet' => '(\'<ul>\' || ARRAY_TO_STRING(ARRAY('
						. 'SELECT \'<li>\' || "p"."nom" || \' \' || "p"."prenom" || \'</li>\' AS "Personnepcg66__noms_complet" '
						. 'FROM "dossierspcgs66" AS "pcg" '
						. 'LEFT JOIN "public"."personnespcgs66" AS "pers_pcg" '
						. 'ON ("pers_pcg"."dossierpcg66_id" = "pcg"."id") '
						. 'LEFT JOIN "public"."personnes" AS "p" '
						. 'ON ("pers_pcg"."personne_id" = "p"."id") '
						. 'WHERE "pcg"."id" = "Dossierpcg66"."id" ), \'\') || \'</ul>\') '
						. 'AS "Personnepcg66__noms_complet"',

						'Decisiondossierpcg66.org_id' => '(\'<ul>\' || ARRAY_TO_STRING(ARRAY('
						. 'SELECT \'<li>\' || "Orgtransmisdossierpcg66"."name" || \'</li>\' AS "Orgtransmisdossierpcg66__name" '
						. 'FROM "decisionsdossierspcgs66" AS "Decisiondossierpcg66" '
						. 'LEFT JOIN "public"."decsdospcgs66_orgsdospcgs66" AS "Decdospcg66Orgdospcg66" '
						. 'ON ("Decdospcg66Orgdospcg66"."decisiondossierpcg66_id" = "Decisiondossierpcg66"."id") '
						. 'LEFT JOIN "public"."orgstransmisdossierspcgs66" AS "Orgtransmisdossierpcg66" '
						. 'ON ("Decdospcg66Orgdospcg66"."orgtransmisdossierpcg66_id" = "Orgtransmisdossierpcg66"."id") '
						. 'WHERE "Decisiondossierpcg66"."dossierpcg66_id" = "Dossierpcg66"."id" '
						. 'ORDER BY "Decisiondossierpcg66"."created" DESC), \'\') || \'</ul>\') '
						. 'AS "Decisiondossierpcg66__org_id"',

						'Situationpdo.libelles' => '(\'<ul>\' || ARRAY_TO_STRING(ARRAY('
						. 'SELECT \'<li>\' || d.libelle || \'</li>\' '
						. 'FROM dossierspcgs66 a '
						. 'INNER JOIN personnespcgs66 b ON a.id = b.dossierpcg66_id '
						. 'INNER JOIN personnespcgs66_situationspdos c ON c.personnepcg66_id = b.id '
						. 'INNER JOIN situationspdos d ON c.situationpdo_id = d.id '
						. 'WHERE a.id = "Dossierpcg66"."id" '
						. 'ORDER BY d.libelle'
						. '), \'\') || \'</ul>\') '
						. 'AS "Situationpdo__libelles"',

						'Statutpdo.libelles' => '(\'<ul>\' || ARRAY_TO_STRING(ARRAY('
						. 'SELECT \'<li>\' || d.libelle || \'</li>\' '
						. 'FROM dossierspcgs66 a '
						. 'INNER JOIN personnespcgs66 b ON a.id = b.dossierpcg66_id '
						. 'INNER JOIN personnespcgs66_statutspdos c ON c.personnepcg66_id = b.id '
						. 'INNER JOIN statutspdos d ON c.statutpdo_id = d.id '
						. 'WHERE a.id = "Dossierpcg66"."id" '
						. 'ORDER BY d.libelle'
						. '), \'\') || \'</ul>\') '
						. 'AS "Statutpdo__libelles"',

						'Traitementpcg66.dateecheances' => '(\'<ul>\' || ARRAY_TO_STRING(ARRAY('
						. 'SELECT \'<li>\' || to_char(c.dateecheance, \'DD/MM/YYYY\') || \'</li>\' '
						. 'FROM "dossierspcgs66" a '
						. 'INNER JOIN personnespcgs66 b ON a.id = b.dossierpcg66_id '
						. 'INNER JOIN traitementspcgs66 c ON c.personnepcg66_id = b.id '
						. 'WHERE a.id = "Dossierpcg66"."id" '
						. 'AND c.dateecheance IS NOT NULL '
						. 'ORDER BY c.created'
						. '), \'\') || \'</ul>\') '
						. 'AS "Traitementpcg66__dateecheances"',
						
						'Dossierpcg66.listetraitements' => '(\'<ul>\' || ARRAY_TO_STRING(ARRAY('
						. 'SELECT \'<li>\' || c."typetraitement" || \'</li>\' AS "Traitementpcg66__typetraitement" '
						. 'FROM "dossierspcgs66" a '
						. 'INNER JOIN personnespcgs66 b ON a.id = b.dossierpcg66_id '
						. 'INNER JOIN traitementspcgs66 c ON c.personnepcg66_id = b.id '
						. 'WHERE a.id = "Dossierpcg66"."id" '
						. 'ORDER BY c.created'
						. '), \'\') || \'</ul>\') '
						. 'AS "Dossierpcg66__listetraitements"',

						'Fichiermodule.nb_fichiers_lies' => '(SELECT COUNT("fichiermodule"."id") '
						. 'FROM "fichiersmodules" AS "fichiermodule" '
						. 'WHERE "fichiermodule"."modele" = \'Foyer\' '
						. 'AND "fichiermodule"."fk_value" = "Foyer"."id") '
						. 'AS "Fichiermodule__nb_fichiers_lies"',

						'Personnepcg66.nbtraitements' => '(SELECT COUNT(*) '
						. 'FROM dossierspcgs66 a '
						. 'INNER JOIN "personnespcgs66" b ON a.id = b.dossierpcg66_id '
						. 'INNER JOIN "traitementspcgs66" c ON c.personnepcg66_id = b.id '
						. 'WHERE a.id = "Dossierpcg66"."id") '
						. 'AS "Personnepcg66__nbtraitements"',
						
						'(SELECT COUNT(*) '
						. 'FROM decisionsdossierspcgs66 '
						. 'WHERE decisionsdossierspcgs66.dossierpcg66_id = "Dossierpcg66"."id") '
						. 'AS "Decisiondossierpcg66__count"',
					)
				);

				// 2. Jointures
				$joinTraitementpcg66 = $this->Dossierpcg66->Personnepcg66->join('Traitementpcg66', array('type' => $types['Traitementpcg66']));
				$sqJoinTraitement = str_replace('Traitementpcg66', 'traitement', $this->Dossierpcg66->Personnepcg66->Traitementpcg66->sq(
					array(
						'fields' => 'id',
						'conditions' => array(
							$joinTraitementpcg66['conditions'],
							'Traitementpcg66.typetraitement' => 'documentarrive',
							'Traitementpcg66.datereception IS NOT NULL',
						),
						'order' => array('Traitementpcg66.created' => 'DESC'),
						'recursive' => -1,
						'limit' => 1
					)
				));
				$joinTraitementpcg66['conditions'] = array(
					$joinTraitementpcg66['conditions'],
					'Traitementpcg66.id IN ('.$sqJoinTraitement.')'
				);

				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Dossierpcg66->Foyer->Dossier->Detaildroitrsa->join('Detailcalculdroitrsa', array(
							'type' => $types['Detailcalculdroitrsa'],
							'conditions' => array(
								'Detailcalculdroitrsa.id IN ('
								. 'SELECT "detailscalculsdroitsrsa"."id" AS detailscalculsdroitsrsa__id '
								. 'FROM detailscalculsdroitsrsa AS detailscalculsdroitsrsa '
								. 'WHERE "detailscalculsdroitsrsa"."detaildroitrsa_id" = "Detaildroitrsa"."id" '
								. 'ORDER BY "detailscalculsdroitsrsa"."ddnatdro" DESC '
								. 'LIMIT 1)'
							)
						)),
						$this->Dossierpcg66->join('Decisiondossierpcg66', 
							array(
								'type' => $types['Decisiondossierpcg66'],
								'conditions' => array(
									'Decisiondossierpcg66.id IN ('
									. 'SELECT "decisionsdossierspcgs66"."id" '
									. 'FROM decisionsdossierspcgs66 '
									. 'WHERE "decisionsdossierspcgs66"."dossierpcg66_id" = "Dossierpcg66"."id" '
									. 'ORDER BY "decisionsdossierspcgs66"."created" DESC '
									. 'LIMIT 1)'
								)
							)
						),
						$this->Dossierpcg66->join('User', array('type' => $types['User'])),
						$this->Dossierpcg66->join('Poledossierpcg66', array('type' => $types['Poledossierpcg66'])),
						$this->Dossierpcg66->join('Personnepcg66', 
							array(
								'type' => $types['Personnepcg66'],
								'conditions' => array(
									'Personnepcg66.id IN ('
									. 'SELECT "personnespcgs66"."id" '
									. 'FROM personnespcgs66 '
									. 'WHERE "personnespcgs66"."dossierpcg66_id" = "Dossierpcg66"."id" '
									. 'ORDER BY "personnespcgs66"."created" '
									. 'LIMIT 1)'
								)
							)
						),
						$this->Dossierpcg66->join('Serviceinstructeur', array('type' => $types['Serviceinstructeur'])),
						$this->Dossierpcg66->join('Originepdo', array('type' => $types['Originepdo'])),
						$this->Dossierpcg66->join('Typepdo', array('type' => $types['Typepdo'])),
						$this->Dossierpcg66->Personnepcg66->join('Categorieromev3', array('type' => $types['Categorieromev3'])),
						$this->Dossierpcg66->Personnepcg66->Categorieromev3->join('Familleromev3', array('type' => $types['Familleromev3'])),
						$this->Dossierpcg66->Personnepcg66->Categorieromev3->join('Domaineromev3', array('type' => $types['Domaineromev3'])),
						$this->Dossierpcg66->Personnepcg66->Categorieromev3->join('Metierromev3', array('type' => $types['Metierromev3'])),
						$this->Dossierpcg66->Personnepcg66->Categorieromev3->join('Appellationromev3', array('type' => $types['Appellationromev3'])),
						$this->Dossierpcg66->Personnepcg66->join('Categoriemetierromev2', array('type' => $types['Categoriemetierromev2'])),
						$joinTraitementpcg66,
						$this->Dossierpcg66->Decisiondossierpcg66->join('Decisionpdo', array('type' => $types['Decisionpdo'])),
					)
				);
				
				// Conditions
				$query['conditions'][] = array('Prestation.rolepers' => 'DEM');

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}
		
		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			$query = $this->Allocataire->searchConditions( $query, $search );

			/**
			 * Generateur de conditions
			 */
			$paths = array(
				'Dossierpcg66.originepdo_id',
				'Dossierpcg66.typepdo_id',
				'Dossierpcg66.orgpayeur',
				'Dossierpcg66.poledossierpcg66_id',
				'Dossierpcg66.user_id',
				'Dossierpcg66.etatdossierpcg',
				'Dossierpcg66.nbpropositions',
				'Decisiondossierpcg66.useravistechnique_id',
				'Decisiondossierpcg66.userproposition_id',
				'Decisiondossierpcg66.decisionpdo_id',
				'Categorieromev3.familleromev3_id',
				'Dossierpcg66.serviceinstructeur_id',
			);

			$pathsToExplode = array(
				'Categorieromev3.domaineromev3_id',
				'Categorieromev3.metierromev3_id',
				'Categorieromev3.appellationromev3_id',
			);

			$pathsDate = array(
				'Dossierpcg66.datereceptionpdo',
				'Dossierpcg66.dateaffectation',
				'Decisiondossierpcg66.datevalidation',
				'Decisiondossierpcg66.datetransmissionop'
			);

			foreach( $paths as $path ) {
				$value = Hash::get( $search, $path );
				if( $value !== null && $value !== '' ) {
					$query['conditions'][$path] = $value;
				}
			}

			foreach( $pathsToExplode as $path ) {
				$value = Hash::get( $search, $path );
				if( $value !== null && $value !== '' && strpos($value, '_') > 0 ) {
					list(,$value) = explode('_', $value);
					$query['conditions'][$path] = $value;
				}
			}

			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, $pathsDate );

			/**
			 * Conditions spéciales
			 */
			$nbproposition = Hash::get($search, 'Decisiondossierpcg66.nbproposition');
			if ( $nbproposition ) {
				$query['conditions'][] = array(
					'(SELECT COUNT(*) '
					. 'FROM decisionsdossierspcgs66 '
					. 'WHERE "decisionsdossierspcgs66"."dossierpcg66_id" = "Dossierpcg66"."id")' => $nbproposition
				);
			}

			$org_id = Hash::get($search, 'Decisiondossierpcg66.org_id');
			if ( $org_id ) {
				$query['conditions'][] = array(
					'Decisiondossierpcg66.id IN ('
					. 'SELECT "decsdospcgs66_orgsdospcgs66"."decisiondossierpcg66_id" AS decsdospcgs66_orgsdospcgs66__decisiondossierpcg66_id '
					. 'FROM decsdospcgs66_orgsdospcgs66 AS decsdospcgs66_orgsdospcgs66 '
					. 'INNER JOIN "public"."orgstransmisdossierspcgs66" AS orgstransmisdossierspcgs66 '
					. 'ON ("decsdospcgs66_orgsdospcgs66"."orgtransmisdossierpcg66_id" = "orgstransmisdossierspcgs66"."id") '
					. 'WHERE "decsdospcgs66_orgsdospcgs66"."orgtransmisdossierpcg66_id" IN ('.implode(', ', $org_id).'))'
				);
			}

			$situationpdo_id = Hash::get($search, 'Traitementpcg66.situationpdo_id');
			if ( $situationpdo_id ) {
				$query['conditions'][] = array(
					 'Personnepcg66.id IN ('
					. 'SELECT "personnespcgs66_situationspdos"."personnepcg66_id" AS personnespcgs66_situationspdos__personnepcg66_id '
					. 'FROM personnespcgs66_situationspdos AS personnespcgs66_situationspdos '
					. 'INNER JOIN "public"."situationspdos" AS situationspdos '
					. 'ON ("personnespcgs66_situationspdos"."situationpdo_id" = "situationspdos"."id") '
					. 'WHERE "personnespcgs66_situationspdos"."situationpdo_id" IN ('.implode(', ', $situationpdo_id).'))'
				);
			}

			$statutpdo_id = Hash::get($search, 'Traitementpcg66.statutpdo_id');
			if ( $statutpdo_id ) {
				$query['conditions'][] = array(
					 '"Personnepcg66"."id" IN ('
					. 'SELECT "personnespcgs66_statutspdos"."personnepcg66_id" AS personnespcgs66_statutspdos__personnepcg66_id '
					. 'FROM personnespcgs66_statutspdos AS personnespcgs66_statutspdos '
					. 'INNER JOIN "public"."statutspdos" AS statutspdos '
					. 'ON ("personnespcgs66_statutspdos"."statutpdo_id" = "statutspdos"."id") '
					. 'WHERE "personnespcgs66_statutspdos"."statutpdo_id" IN ('.implode(', ', $statutpdo_id).'))'
				);
			}

			if ( Hash::get($search, 'Dossierpcg66.dossierechu') ) {
				$query['conditions'][] = 'EXISTS('.$this->Dossierpcg66->Personnepcg66->Traitementpcg66->WebrsaTraitementpcg66->sqTraitementpcg66Echu('Personnepcg66.id').')';
			}

			if ( Hash::get($search, 'Traitementpcg66.courriersansmodele') !== null ) {
				$operateur = Hash::get($search, 'Traitementpcg66.courriersansmodele') ? 'IN' : 'NOT IN';
				
				$querySq = array(
					'alias' => 'traitementspcgs66_sq',
					'fields' => 'traitementspcgs66_sq.personnepcg66_id',
					'joins' => array(
						array(
							'alias' => 'Modeletraitementpcg66',
							'table' => 'modelestraitementspcgs66',
							'conditions' => 'Modeletraitementpcg66.traitementpcg66_id = traitementspcgs66_sq.id',
							'type' => 'LEFT'
						)
					),
					'contain' => false,
					'conditions' => array(
						'traitementspcgs66_sq.typetraitement' => 'courrier',
						'Modeletraitementpcg66.id IS NULL',
						'traitementspcgs66_sq.personnepcg66_id = Personnepcg66.id'
					)
				);

				$sq = $this->Dossierpcg66->Personnepcg66->Traitementpcg66->sq($querySq);

				$sq = str_replace('"traitementspcgs66_sq', '"t', $sq);
				$sq = str_replace('"Modeletraitementpcg66"', '"m"', $sq);
				$sq = str_replace('traitementspcgs66_sq', '"t"', $sq);
				$sq = str_replace('Modeletraitementpcg66', '"m"', $sq);
	
				$query['conditions'][] = "Personnepcg66.id {$operateur} ( {$sq} )";			
			}

			return $query;
		}
	}
?>