<?php
	/**
	 * Code source de la classe Adresse.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Adresse ...
	 *
	 * @package app.Model
	 */
	class Adresse extends AppModel
	{
		public $name = 'Adresse';

		public $virtualFields = array(
			'localite' => array(
				'type'      => 'string',
				'postgres'  => '( COALESCE( "%s"."codepos", \'\' ) || \' \' || COALESCE( "%s"."nomcom", \'\' ) )'
			),
			'complete' => array(
				'type'      => 'string',
				// TODO: nl2br -> vue
				'postgres'  => 'COALESCE( "%s"."numvoie", \'\' ) || \' \' || COALESCE( "%s"."libtypevoie", \'\' ) || \' \' || COALESCE( "%s"."nomvoie", \'\' ) || E\'\n\' || COALESCE( "%s"."codepos", \'\' ) || \' \' || COALESCE( "%s"."nomcom", \'\' )'
			),
		);

		public $validate = array(
			'libtypevoie' => array(
				'notEmpty' => array( 'rule' => 'notEmpty', 'message' => 'Champ obligatoire' )
			),
			'nomvoie' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'codepos' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'nomcom' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'pays' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
		);

		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 * 
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array(
			'pays' => array('FRA', 'HOR'),
			'typeres' => array('E', 'O', 'S'),
			'rgadr' => array('01', '02', '03'),
		);

        public $belongsTo = array(
			'Departement' => array(
				'className' => 'Departement',
				'foreignKey' => false,
				'conditions' => array(
					'SUBSTRING( "Adresse"."codepos" FROM 1 FOR 2 ) = "Departement"."numdep"'
				),
				'dependent' => false
			)
        );

		public $hasMany = array(
			'Adressefoyer' => array(
				'className' => 'Adressefoyer',
				'foreignKey' => 'adresse_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);
		
		/**
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
            'Canton' => array(
				'className' => 'Canton',
				'joinTable' => 'adresses_cantons',
				'foreignKey' => 'adresse_id',
				'associationForeignKey' => 'canton_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'AdresseCanton'
			),
		);

		/**
		 * Valeurs acceptées pour le champ libtypevoie.
		 *
		 * @var array
		 */
		public $libtypevoie = array(
			'ABBAYE',
			'ANCIEN CHEMIN',
			'AGGLOMERATION',
			'AIRE',
			'ALLEE',
			'ANSE',
			'ARCADE',
			'ANCIENNE ROUTE',
			'AUTOROUTE',
			'AVENUE',
			'BASTION',
			'BAS CHEMIN',
			'BOUCLE',
			'BOULEVARD',
			'BEGUINAGE',
			'BERGE',
			'BOIS',
			'BARRIERE',
			'BOURG',
			'BASTIDE',
			'BUTTE',
			'CALE',
			'CAMP',
			'CARREFOUR',
			'CARRIERE',
			'CARRE',
			'CARREAU',
			'CAVEE',
			'CENTRE COMMERCIAL',
			'CAMPAGNE',
			'CHEMIN',
			'CHEMINEMENT',
			'CHEZ',
			'CHARMILLE',
			'CHALET',
			'CHAPELLE',
			'CHAUSSEE',
			'CHATEAU',
			'CHEMIN VICINAL',
			'CITE',
			'CLOITRE',
			'CLOS',
			'COL',
			'COLLINE',
			'CORNICHE',
			'COTE',
			'COTEAU',
			'COTTAGE',
			'COUR',
			'CAMPING',
			'COURS',
			'CASTEL',
			'CONTOUR',
			'CENTRE',
			'DARSE',
			'DEGRE',
			'DIGUE',
			'DOMAINE',
			'DESCENTE',
			'ECLUSE',
			'EGLISE',
			'ENCEINTE',
			'ENCLOS',
			'ENCLAVE',
			'ESCALIER',
			'ESPLANADE',
			'ESPACE',
			'ETANG',
			'FAUBOURG',
			'FONTAINE',
			'FORUM',
			'FORT',
			'FOSSE',
			'FOYER',
			'FERME',
			'GALERIE',
			'GARE',
			'GARENNE',
			'GRAND BOULEVARD',
			'GRAND ENSEMBLE',
			'GROUPE',
			'GROUPEMENT',
			'GRAND RUE',
			'GRANDE RUE',
			'GRILLE',
			'GRIMPETTE',
			'HAMEAU',
			'HAUT CHEMIN',
			'HIPPODROME',
			'HALLE',
			'HLM',
			'ILE',
			'IMMEUBLE',
			'IMPASSE',
			'JARDIN',
			'JETEE',
			'LIEU DIT',
			'LEVEE',
			'LOTISSEMENT',
			'MAIL',
			'MANOIR',
			'MARCHE',
			'MAS',
			'METRO',
			'MAISON FORESTIERE',
			'MOULIN',
			'MONTEE',
			'MUSEE',
			'NOUVELLE ROUTE',
			'PETITE AVENUE',
			'PALAIS',
			'PARC',
			'PASSAGE',
			'PASSE',
			'PATIO',
			'PAVILLON',
			'PORCHE',
			'PETIT CHEMIN',
			'PERIPHERIQUE',
			'PETITE IMPASSE',
			'PARKING',
			'PLACE',
			'PLAGE',
			'PLAN',
			'PLACIS',
			'PASSERELLE',
			'PLAINE',
			'PLATEAU',
			'PASSAGE A NIVEAU',
			'POINTE',
			'PONT',
			'PORTIQUE',
			'PORT',
			'POTERNE',
			'POURTOUR',
			'PRE',
			'PROMENADE',
			'PRESQU\'ILE',
			'PETITE ROUTE',
			'PARVIS',
			'PERISTYLE',
			'PETITE ALLEE',
			'PORTE',
			'PETITE RUE',
			'QUAI',
			'QUARTIER',
			'RUE',
			'RACCOURCI',
			'RAIDILLON',
			'REMPART',
			'RESIDENCE',
			'RUELLE',
			'ROC',
			'ROCADE',
			'ROQUET',
			'RAMPE',
			'ROND POINT',
			'ROTONDE',
			'ROUTE',
			'SENT',
			'SENTIER',
			'SQUARE',
			'STATION',
			'STADE',
			'TOUR',
			'TERRE PLEIN',
			'TRAVERSE',
			'TERRAIN',
			'TERTRE',
			'TERRASSE',
			'VALLEE',
			'VALLON',
			'VIEUX CHEMIN',
			'VENELLE',
			'VIA',
			'VILLA',
			'VILLAGE',
			'VOIE',
			'VIEILLE ROUTE',
			'ZONE D\'ACTIVITE',
			'ZONE D\'AMENAGEMENT CONCERTE',
			'ZONE D\'AMENAGEMENT DIFFERE',
			'ZONE ARTISANALE',
			'ZONE INDUSTRIELLE',
			'ZONE',
			'ZONE A URBANISER EN PRIORITE'
		);
		
		/**
		 * Liste des champs où la valeur du notEmpty/allowEmpty est configurable
		 * 
		 * @var array
		 */
		public $configuredAllowEmptyFields = array(
			'libtypevoie',
			'nomvoie'
		);

		/**
		 * Surcharge du constructeur pour ajouter la règle de validation inList du
		 * champ libtypevoie.
		 *
		 * @param mixed $id Set this ID for this model on startup, can also be an array of options, see above.
		 * @param string $table Name of database table to use.
		 * @param string $ds DataSource connection name.
		 */
		public function __construct( $id = false, $table = null, $ds = null ) {
			parent::__construct( $id, $table, $ds );

			$this->validate['libtypevoie']['inList'] = array(
				'rule'      => array( 'inList', $this->libtypevoie ),
				'message'   => 'Veuillez choisir une valeur.',
				'allowEmpty' => !ValidateAllowEmptyUtility::isRequired('Adresse.libtypevoie')
			);
		}

		/**
		 * Surcharge de la méthode enums pour que les valeurs du champ libtypevoie
		 * ne soient pas transformées en ENUM::LIBTYPEVOIE::
		 *
		 * @return array
		 */
		public function enums() {
			$enums = parent::enums();

			$enums[$this->alias]['libtypevoie'] = array_combine( $this->libtypevoie, $this->libtypevoie );

			return $enums;
		}

		public function listeCodesInsee() {
			$querydata = array(
				'fields' => array(
					"DISTINCT {$this->name}.numcom",
					"{$this->name}.nomcom",
				),
				'joins' => array(
					$this->join( 'Adressefoyer', array( 'type' => 'INNER', 'conditions' => array( 'Adressefoyer.rgadr' => '01' ) ) )
				),
				'conditions' => array(
					"{$this->name}.nomcom IS NOT NULL",
					"{$this->name}.nomcom <> ''",
					"{$this->name}.numcom IS NOT NULL",
					"{$this->name}.numcom <> ''"
				),
				'order' => array(
					"{$this->name}.numcom ASC",
					"{$this->name}.nomcom ASC"
				),
				'recursive' => -1
			);

			$results = $this->find( 'all', $querydata );

			return Hash::combine( $results, '{n}.Adresse.numcom', array( '%s %s', '{n}.Adresse.numcom', '{n}.Adresse.nomcom' ) );
		}
		
		/**
		 * En cas de sauvegarde sur Adresse, on doit recalculer le canton (si Canton activé)
		 * @param boolean $created
		 */
		public function afterSave($created) {
			parent::afterSave($created);
			
			if ( Configure::read( 'Canton.useAdresseCanton' ) ) {
				$this->AdresseCanton->updateByConditions( array( 'Adresse.id' => $this->id ) );
			}
		}
	}
?>