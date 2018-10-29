<?php
	/**
	 * Code source de la classe Creance.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Creance ...
	 *
	 * @package app.Model
	 */
	class Creance extends AppModel
	{
		public $name = 'Creance';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $fakeInLists = array(
			'haspiecejointe' => array('0', '1'),
		);

		public $validate = array(
			'foyer_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
			'orgcre' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'','FLU', 'MAN'
						)
					)
				)
			),
			'motiindu' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'10', '11', '12', '20', '21', '30', '31', '32', '33',
							'34', '35', '40', '41', '42', '50', '51', '52', '60',
							'61', '62', '63', '64', '65', '70', '71', '72', '73',
							'74', '75', '76', '80', '81', '82', '90', '91', '92',
							'93', '94', '95', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF',
							'AG', 'AH', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH',
							'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'DD', 'DE', 'DF',
							'DG', 'DH', 'EE', 'EF', 'EG', 'EH', 'FF', 'FG', 'FH',
							'GG', 'GH', 'HH', 'K1', 'K2', 'K3', 'K4'
						)
					)
				)
			),
			'natcre' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'INK', 'ITK', 'INL', 'ITL', 'INM', 'ITM', 'INS', 'ITS', 'ISK', 'ISL', 'ISM', 'ISS'
						)
					)
				)
			),
			'oriindu' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'10', '20', '30', '40', '50', '55', '60', '61', '62', '63', '64', '65', '70', '71', '72', '73', '80',
						)
					)
				)
			),
			'respindu' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'12', '66', '67', '26', '24', '10', '65', '54', '62',
							'13', '64', '51', '52', '50', '61', '20', '41', '31',
							'40', '30', '22', '53', '15', '11', '74', '63', '32',
							'60', '25', '23', '21', '14',
						)
					)
				)
			),
		);

		public $belongsTo = array(
			'Foyer' => array(
				'className' => 'Foyer',
				'foreignKey' => 'foyer_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Creance\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Creance\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		/**
		 * Retourne l'id d'un Dossier à partir de l'id d'une Creance.
		 *
		 * @param integer $creance_id
		 * @return integer
		 */
		public function dossierId( $creance_id ) {
			$qd_creance = array(
				'fields' => array( 'Foyer.dossier_id' ),
				'joins' => array(
					$this->join( 'Foyer', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					'Creance.id' => $creance_id
				),
				'recursive' => -1
			);
			$creance= $this->find('first', $qd_creance);

			if( !empty( $creance ) ) {
				return $creance['Foyer']['dossier_id'];
			}
			else {
				return null;
			}
		}

		/**
		*
		* Methode de recherche pour
		*/
		public function search( $criteres ) {
			/// Conditions de base
			$conditions = array();

			/// Critères
			$mois = Set::extract( $criteres, 'Filtre.moisentrants' );
			$dossierdernier = Set::extract( $criteres, 'Filtre.dossier_dernier' );
			$etatdossier = Set::extract( $criteres, 'Filtre.etat_dossier' );
			$droitdevoirs = Set::extract( $criteres, 'Filtre.droit_devoirs' );
			$orgcre = Set::extract( $criteres, 'Filtre.orig_creance' );
			$creancepositive = Set::extract( $criteres, 'Filtre.creance_positive' );

			/// Mois d'activité du dossier
			if( !empty( $mois ) && dateComplete( $criteres, 'Filtre.moisentrants' ) ) {
				$month = $mois['month'];
				$year = $mois['year'];
				$conditions[] = 'EXTRACT(MONTH FROM Dossier.dtdemrsa ) = '.$month;
				$conditions[] = 'EXTRACT(YEAR FROM Dossier.dtdemrsa ) = '.$year;
			}

			/// Par dernier dossier allocataire
			if( !empty( $dossierdernier ) ) {
				$conditions[] = ' Dossier.id IN ( SELECT derniersdossiersallocataires.dossier_id FROM derniersdossiersallocataires WHERE derniersdossiersallocataires.personne_id = Personne.id )'; 				
			}

			/// Par état
			if( !empty( $etatdossier ) ) {
				$conditions[] = ' Situationdossierrsa.etatdosrsa IN (\''.implode("','",$etatdossier).'\') ';
			}

			/// Par droit et devoirs
			if( !empty( $droitdevoirs ) ) {
				$conditions[] = ' Calculdroitrsa.toppersdrodevorsa ILIKE \'%'.Sanitize::clean( $droitdevoirs, array( 'encode' => false ) ).'%\' ';
			}

			/// Par origine de la créance
			if( !empty( $orgcre ) ) {
				$conditions[] = ' Creance.orgcre ILIKE \'%'.Sanitize::clean( $orgcre, array( 'encode' => false ) ).'%\' ';
			}

			/// Pour les créances positives
			if( !empty( $creancepositive ) ) {
				$conditions[] = ' Creance.mtsolreelcretrans > \'0\' ';
			}

			/// Requête
			$this->Foyer = ClassRegistry::init( 'Foyer' );

			$query = array(
				'fields' => array(
					'"Creance"."id"',
					'"Creance"."dtimplcre"',
					'"Creance"."natcre"',
					'"Creance"."rgcre"',
					'"Creance"."motiindu"',
					'"Creance"."oriindu"',
					'"Creance"."respindu"',
					'"Creance"."ddregucre"',
					'"Creance"."dfregucre"',
					'"Creance"."dtdercredcretrans"',
					'"Creance"."mtsolreelcretrans"',
					'"Creance"."mtinicre"',
					'"Creance"."foyer_id"',
					'"Creance"."moismoucompta"',
					'"Creance"."orgcre"',
					'"Creance"."haspiecejointe"',
					'"Dossier"."id"',
					'"Dossier"."numdemrsa"',
					'"Dossier"."matricule"',
					'"Dossier"."typeparte"',
					'"Calculdroitrsa"."toppersdrodevorsa"',
					'"Calculdroitrsa"."toppersentdrodevorsa"',
					'"Personne"."id"',
					'"Personne"."nom"',
					'"Personne"."prenom"',
					'"Personne"."nir"',
					'"Personne"."dtnai"',
					'"Personne"."qual"',
					'"Personne"."nomcomnai"',
					'"Situationdossierrsa"."etatdosrsa"'
				),
				'recursive' => -1,
				'joins' => array(
					array(
						'table'      => 'foyers',
						'alias'      => 'Foyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Creance.foyer_id = Foyer.id' )
					),
					array(
						'table'      => 'dossiers',
						'alias'      => 'Dossier',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Foyer.dossier_id = Dossier.id' )
					),
					array(
						'table'      => 'situationsdossiersrsa',
						'alias'      => 'Situationdossierrsa',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Situationdossierrsa.dossier_id = Dossier.id' )
					),
					array(
						'table'      => 'personnes',
						'alias'      => 'Personne',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Personne.foyer_id = Foyer.id' )
					),
					array(
						'table'      => 'calculsdroitsrsa',
						'alias'      => 'Calculdroitrsa',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Calculdroitrsa.personne_id = Personne.id' )
					),
					array(
						'table'      => 'prestations',
						'alias'      => 'Prestation',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'Personne.id = Prestation.personne_id',
							'Prestation.natprest = \'RSA\'',
							'( Prestation.rolepers = \'DEM\' )'
						)
					),
				),
				'order' => array( '"Dossier"."numdemrsa"' ),
				'conditions' => $conditions
			);

			return $query;
		}

	}
?>