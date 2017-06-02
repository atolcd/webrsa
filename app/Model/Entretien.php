<?php
	/**
	 * Code source de la classe Entretien.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Entretien ...
	 *
	 * @package app.Model
	 */
	class Entretien extends AppModel
	{
		public $name = 'Entretien';

		public $actsAs = array(
			'Allocatairelie',
			'Formattable' => array(
				'suffix' => array( 'structurereferente_id', 'referent_id' ),
			),
			'Autovalidate2',
			'Enumerable' => array(
				'fields' => array(
					'typeentretien',
					'haspiecejointe'
				)
			),
			'Gedooo.Gedooo',
			'ModelesodtConditionnables' => array(
				66 => array(
					'default' => '%s/impression.odt',
				)
			)
		);

		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Entretien\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Referent' => array(
				'className' => 'Referent',
				'foreignKey' => 'referent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typerdv' => array(
				'className' => 'Typerdv',
				'foreignKey' => 'typerdv_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Objetentretien' => array(
				'className' => 'Objetentretien',
				'foreignKey' => 'objetentretien_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Rendezvous' => array(
				'className' => 'Rendezvous',
				'foreignKey' => 'rendezvous_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
            'Actioncandidat' => array(
				'className' => 'Actioncandidat',
				'foreignKey' => 'actioncandidat_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $validate = array(
			'dateentretien' => array(
				'rule' => 'date'
			),
		);

		/**
		 * Revoi la requete pour récuperer toutes les données pour l'affichage d'un Entretien
		 *
		 * @param integer $entretien_id
		 * @return array
		 */
		public function queryView( $entretien_id ){
			$query = array(
				'fields' => array_merge(
					$this->fields()
				),
				'recursive' => -1,
				'contain' => false,
				'conditions' => array(),
				'joins' => array()
			);

			$query['conditions']['Entretien.id'] = $entretien_id;

			return $query;
		}

		/**
		 * Requète d'impression
		 *
		 * @param type $entretien_id
		 * @return type
		 */
		public function queryImpression( $entretien_id ){
			$queryView = $this->queryView( $entretien_id );
			$queryPersonne = $this->queryPersonne( 'Entretien.personne_id' );

			$query['fields'] = array_merge( $queryView['fields'], $queryPersonne['fields'] );
			$query['joins'] = array_merge( $queryView['joins'], array( $this->join( 'Personne' ) ), $queryPersonne['joins'] );
			$query['conditions'] = $queryView['conditions'];

			return $query;
		}

		/**
		 * Permet d'obtenir les informations lié à un Allocataire d'un Entretien
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function queryPersonne( $personne_id ){
			$query = ClassRegistry::init( 'Allocataire' )->searchQuery();

			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Titresejour.dftitsej',
					'Departement.name',
					'( '.$this->Personne->Foyer->vfNbEnfants().' ) AS "Foyer__nb_enfants"'
				)
			);

			$query['joins'][] = $this->Personne->Foyer->Adressefoyer->Adresse->join( 'Departement', array( 'type' => 'LEFT OUTER' ) );
			$query['joins'][] = $this->Personne->join( 'Titresejour', array( 'type' => 'LEFT OUTER' ) );

			$query['conditions']['Personne.id'] = $personne_id;

			return $query;
		}

		public function personneId( $entretien_id ){
			$result = $this->find( 'first', array( 'fields' => 'personne_id', 'contain' => false, 'conditions' => array( 'id' => $entretien_id ) ) );
			return $result['Entretien']['personne_id'];
		}

		/**
		 * Renvoi la requete de base pour l'affichage des informations liés aux entretiens d'une personne
		 * @param integer $personne_id
		 * @return array
		 */
		public function queryEntretiens( $personne_id = null ) {
			$query = array(
				'fields' => array(
					'Entretien.id',
					'Entretien.personne_id',
					'Entretien.dateentretien',
					'Entretien.arevoirle',
					'Entretien.typeentretien',
					'Structurereferente.lib_struc',
					$this->Referent->sqVirtualField( 'nom_complet' ),
					'Objetentretien.name',
					'Actioncandidat.name',
					'Entretien.commentaireentretien',
				),
				'contain' => array(
					'Structurereferente',
					'Referent',
					'Objetentretien',
					'Actioncandidat'
				),
				'conditions' => array(
					'Entretien.personne_id' => $personne_id,
					'Entretien.dateentretien > (NOW()::date - INTERVAL \'3 years\')'
				),
				'order' => array(
					'Entretien.dateentretien DESC', 'Entretien.id DESC'
				)
			);
			return $query;
		}

		/**
		 * Options pour les entretiens
		 * @return array
		 */
		public function options(){
			$options = $this->enums();

			$options[$this->alias]['typerdv_id'] = $this->Typerdv->find( 'list' );
			$options[$this->alias]['objetentretien_id'] = $this->Objetentretien->find( 'list' );

			return $options;
		}
	}
?>