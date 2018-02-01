<?php
	/**
	 * Code source de la classe Personnepcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Personnepcg66 ...
	 *
	 * @package app.Model
	 */
	class Personnepcg66 extends AppModel
	{
		public $name = 'Personnepcg66';

		public $actsAs = array(
			'Allocatairelie',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $virtualFields = array(
			'nbtraitements' => array(
				'type'      => 'integer',
				'postgres'  => '(
					SELECT COUNT(*)
						FROM traitementspcgs66
						WHERE
							traitementspcgs66.personnepcg66_id = "%s"."id"
				)',
			),
		);

		public $belongsTo = array(
			'Dossierpcg66' => array(
				'className' => 'Dossierpcg66',
				'foreignKey' => 'dossierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			// Début ROME V3
			'Categorieromev3' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'categorieromev3_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			// Fin ROME V3
			// Début ROME V2
			'Categoriesecteurromev2' => array(
				'className' => 'Coderomesecteurdsp66',
				'foreignKey' => 'categoriegeneral',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Categoriemetierromev2' => array(
				'className' => 'Coderomemetierdsp66',
				'foreignKey' => 'categoriedetail',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			// Fin ROME V2
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		public $hasMany = array(
			'Traitementpcg66' => array(
				'className' => 'Traitementpcg66',
				'foreignKey' => 'personnepcg66_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		public $hasAndBelongsToMany = array(
			'Situationpdo' => array(
				'className' => 'Situationpdo',
				'joinTable' => 'personnespcgs66_situationspdos',
				'foreignKey' => 'personnepcg66_id',
				'associationForeignKey' => 'situationpdo_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Personnepcg66Situationpdo'
			),
			'Statutpdo' => array(
				'className' => 'Statutpdo',
				'joinTable' => 'personnespcgs66_statutspdos',
				'foreignKey' => 'personnepcg66_id',
				'associationForeignKey' => 'statutpdo_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Personnepcg66Statutpdo'
			)
		);

		/**
		 * Liste des traitements non clos liés à n'importe quel dossier du Foyer
		 *
		 * @param integer|array $personneId
		 * @param string $action
		 * @param array $data
		 * @param array $traitementspcgsouverts
		 * @return array
		 */
		public function listeTraitementpcg66NonClos( $personneId = 'Personne.id', $action, $data = array(), $traitementspcgsouverts = array() ) {
			$traitementsNonClos = array();

			// Liste des id des foyers concernés (on peut envoyer un array d'ids de personnes)
			$qdFoyerIds = array(
				'fields' => array(
					'Personne.foyer_id'
				),
				'contain' => false,
				'conditions' => array(
					'Personne.id' => (array)$personneId
				)
			);
			$foyerIds = Hash::extract(
				$this->Personne->find( 'all', $qdFoyerIds ),
				'{n}.Personne.foyer_id'
			);

			// Sous-requête permettant de trouver toutes les personnes de ces foyers
			$qdSqPersonnesIds = array(
				'alias' => 'personnes',
				'fields' => array(
					'personnes.id'
				),
				'contain' => false,
				'conditions' => array(
					'personnes.foyer_id' => (array)$foyerIds
				)
			);
			$sqPersonnesIds = $this->Personne->sq( $qdSqPersonnesIds );

			// Liste des dossiers PCG et personnes PCG de ces foyers
			$qdPersonnesDossiersPcgsIds = array(
				'fields' => array(
					'Personnepcg66.id',
					'Personnepcg66.dossierpcg66_id'
				),
				'contain' => false,
				'conditions' => array(
					"Personnepcg66.personne_id IN ( {$sqPersonnesIds} )"
				)
			);
			$tmpResults = $this->find( 'all', $qdPersonnesDossiersPcgsIds );

			$dossierspcgs66Ids = (array)Hash::extract( $tmpResults, '{n}.Personnepcg66.dossierpcg66_id' );
			$personnespcgs66Ids = (array)Hash::extract( $tmpResults, '{n}.Personnepcg66.id' );

			$traitementspcgs66 = array();
			if( !empty( $dossierspcgs66Ids ) ) {
				$query = array(
					'fields' => array(
						'Traitementpcg66.id',
						'Traitementpcg66.datedepart',
						'Traitementpcg66.dateecheance',
						'Personnepcg66.dossierpcg66_id',
						'Personnepcg66.id',
						'Personnepcg66.personne_id',
						$this->Personne->sqVirtualField( 'nom_complet' ),
						'Descriptionpdo.name',
						'Situationpdo.libelle',
						'Dossierpcg66.datereceptionpdo',
						'Typepdo.libelle',
						$this->Dossierpcg66->User->sqVirtualField( 'nom_complet' )
					),
					'contain' => false,
					'joins' => array(
						$this->Traitementpcg66->join( 'Personnepcg66', array( 'type' => 'INNER' ) ),
						$this->Traitementpcg66->Personnepcg66->join( 'Dossierpcg66', array( 'type' => 'INNER' ) ),
						$this->Traitementpcg66->Personnepcg66->Dossierpcg66->join( 'Typepdo', array( 'type' => 'INNER' ) ),
						$this->Traitementpcg66->Personnepcg66->Dossierpcg66->join( 'User', array( 'type' => 'INNER' ) ),
						$this->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Traitementpcg66->join( 'Descriptionpdo', array( 'type' => 'INNER' ) ),
						$this->Traitementpcg66->join( 'Situationpdo', array( 'type' => 'LEFT OUTER' ) )
					),
					'conditions' => array(
						'Traitementpcg66.personnepcg66_id' => $personnespcgs66Ids,
						'Traitementpcg66.clos' => 'N'
					)
				);

				if( $action == 'edit' && isset( $data['Traitementpcg66']['id'] ) && !empty( $data['Traitementpcg66']['id'] ) ) {
					$query['conditions'][] = array(
						'Traitementpcg66.id NOT' => $data['Traitementpcg66']['id']
					);
				}

                // On enlève les IDs des traitements ouverts déjà pris en compte
                if( !empty( $traitementspcgsouverts ) ) {
                    $traitementspcgsouverts = Hash::extract( $traitementspcgsouverts, '{n}.Traitementpcg66.id' );
					$query['conditions'][] = array(
						'Traitementpcg66.id NOT' => $traitementspcgsouverts
					);
                }

				$traitementspcgs66 = $this->Traitementpcg66->find( 'all', $query );

				if( !empty( $traitementspcgs66 ) ) {
					foreach( $traitementspcgs66 as $i => $traitementpcg66 ) {
						$datedepart = $traitementpcg66['Traitementpcg66']['datedepart'];
						if( !empty( $datedepart ) ) {
							$date = ', le '.date_short( $datedepart ).'';
						}
						else {
							$date = '';
						}

						$echeance = Hash::get($traitementpcg66, 'Traitementpcg66.dateecheance');
						$echeance = $echeance ? ' au '.date_short($echeance) : '';

                        // Variable présente dans le formulaire d'ajout/édition des traitements PCGS
                        $traitementsNonClos['Traitementpcg66']['traitementnonclos']["{$traitementpcg66['Traitementpcg66']['id']}"] = $traitementpcg66['Situationpdo']['libelle'].' géré par '.$traitementpcg66['User']['nom_complet'].' du '.date_short( $traitementpcg66['Dossierpcg66']['datereceptionpdo'] ).$echeance;

                        // Variable présente dans le formulaire d'ajout/édition des décisions d'un dossier PCG
						$traitementsNonClos['Traitementpcg66']['traitementnonclosdecision']["{$traitementpcg66['Traitementpcg66']['id']}"] = $traitementpcg66['Personne']['nom_complet'].' : '.$traitementpcg66['Descriptionpdo']['name'].' - '.$traitementpcg66['Situationpdo']['libelle'].' géré par '.$traitementpcg66['User']['nom_complet'].' du '.date_short( $traitementpcg66['Dossierpcg66']['datereceptionpdo'] ).$echeance;
					}
				}
			}

			return $traitementsNonClos;
		}

	}
?>