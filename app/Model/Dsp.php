<?php
	/**
	 * Code source de la classe Dsp.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	// FIXME: possible de faire plus "proprement" qu'avec des define ?
	define( 'ANNOBTNIVDIPMAX_MIN_YEAR', ( date( 'Y' ) - 100 ) );
	define( 'ANNOBTNIVDIPMAX_MAX_YEAR', date( 'Y' ) );
	define( 'ANNOBTNIVDIPMAX_MESSAGE', 'Veuillez entrer une année comprise entre '.ANNOBTNIVDIPMAX_MIN_YEAR.' et '.ANNOBTNIVDIPMAX_MAX_YEAR.' .' );
	/**
	 * La classe Dsp ...
	 *
	 * @package app.Model
	 */
	class Dsp extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Dsp';
		protected $_modules = array( 'caf' );
		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			// Début ROME V2
			'Libderact66Metier' => array(
				'className' => 'Coderomemetierdsp66',
				'foreignKey' => 'libderact66_metier_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Libsecactderact66Secteur' => array(
				'className' => 'Coderomesecteurdsp66',
				'foreignKey' => 'libsecactderact66_secteur_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Libactdomi66Metier' => array(
				'className' => 'Coderomemetierdsp66',
				'foreignKey' => 'libactdomi66_metier_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Libsecactdomi66Secteur' => array(
				'className' => 'Coderomesecteurdsp66',
				'foreignKey' => 'libsecactdomi66_secteur_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Libemploirech66Metier' => array(
				'className' => 'Coderomemetierdsp66',
				'foreignKey' => 'libemploirech66_metier_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Libsecactrech66Secteur' => array(
				'className' => 'Coderomesecteurdsp66',
				'foreignKey' => 'libsecactrech66_secteur_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			// Fin ROME V2
			// Début ROME V3
			'Deractromev3' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'deractromev3_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Deractdomiromev3' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'deractdomiromev3_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Actrechromev3' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'actrechromev3_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			// Fin ROME V3
		);
		public $hasMany = array(
			'Detaildifsoc' => array(
				'className' => 'Detaildifsoc',
				'foreignKey' => 'dsp_id',
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
			'Detailaccosocfam' => array(
				'className' => 'Detailaccosocfam',
				'foreignKey' => 'dsp_id',
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
			'Detailaccosocindi' => array(
				'className' => 'Detailaccosocindi',
				'foreignKey' => 'dsp_id',
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
			'Detaildifdisp' => array(
				'className' => 'Detaildifdisp',
				'foreignKey' => 'dsp_id',
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
			'Detailnatmob' => array(
				'className' => 'Detailnatmob',
				'foreignKey' => 'dsp_id',
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
			'Detaildiflog' => array(
				'className' => 'Detaildiflog',
				'foreignKey' => 'dsp_id',
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
			'Detailmoytrans' => array(
				'className' => 'Detailmoytrans',
				'foreignKey' => 'dsp_id',
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
			'Detaildifsocpro' => array(
				'className' => 'Detaildifsocpro',
				'foreignKey' => 'dsp_id',
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
			'Detailprojpro' => array(
				'className' => 'Detailprojpro',
				'foreignKey' => 'dsp_id',
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
			'Detailfreinform' => array(
				'className' => 'Detailfreinform',
				'foreignKey' => 'dsp_id',
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
			'Detailconfort' => array(
				'className' => 'Detailconfort',
				'foreignKey' => 'dsp_id',
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
			'DspRev' => array(
				'className' => 'DspRev',
				'foreignKey' => 'dsp_id',
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
			'Populationb3pdv93' => array(
				'className' => 'Populationb3pdv93',
				'foreignKey' => 'dsp_id',
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
		public $validate = array(
			'annobtnivdipmax' => array(
				'rule' => array( 'inclusiveRange', ANNOBTNIVDIPMAX_MIN_YEAR, ANNOBTNIVDIPMAX_MAX_YEAR ),
				'message' => ANNOBTNIVDIPMAX_MESSAGE,
				'allowEmpty' => true
			),
			'personne_id' => array( // FIXME: Autovalidate2 ne le fait pas ? -> contratsinsertion/edit/10630
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			)
		);

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Conditionnable',
			'Autovalidate2',
			'Enumerable' => array(
				'fields' => array(
					'sitpersdemrsa' => array(
						'values' => array( '0101', '0102', '0103', '0104', '0105', '0106', '0107', '0108', '0109' )
					),
					'nivetu' => array(
						array( '1201', '1202', '1203', '1204', '1205', '1206', '1207' )
					),
					'nivdipmaxobt' => array(
						'values' => array( '2601', '2602', '2603', '2604', '2605', '2606' )
					),
					'hispro' => array(
						'values' => array( '1901', '1902', '1903', '1904' )
					),
					'cessderact' => array(
						'values' => array( '2701', '2702' )
					),
					'duractdomi' => array(
						'values' => array( '2104', '2105', '2106', '2107' )
					),
					'inscdememploi' => array(
						'values' => array( '4301', '4302', '4303', '4304' )
					),
					'accoemploi' => array(
						'values' => array( '1801', '1802', '1803' )
					),
					'natlog' => array(
						'values' => array( '0901', '0902', '0903', '0904', '0905', '0906', '0907', '0908', '0909', '0910', '0911', '0912', '0913' )
					),
					'demarlog' => array(
						'values' => array( '1101', '1102', '1103' )
					),
					'topisogroouenf' => array( 'type' => 'booleannumber', 'domain' => 'default', ),
					'topdrorsarmiant' => array( 'type' => 'booleannumber', 'domain' => 'default' ),
					'topcouvsoc' => array( 'type' => 'booleannumber', 'domain' => 'default' ),
					'topqualipro' => array( 'type' => 'booleannumber', 'domain' => 'default' ),
					'topcompeextrapro' => array( 'type' => 'booleannumber', 'domain' => 'default' ),
					'topengdemarechemploi' => array( 'type' => 'booleannumber', 'domain' => 'default' ),
					'topdomideract' => array( 'type' => 'booleannumber', 'domain' => 'default' ),
					'topisogrorechemploi' => array( 'type' => 'booleannumber', 'domain' => 'default' ),
					'topprojpro' => array( 'type' => 'booleannumber', 'domain' => 'default' ),
					'topcreareprientre' => array( 'type' => 'booleannumber', 'domain' => 'default' ),
					'topmoyloco' => array( 'type' => 'booleannumber', 'domain' => 'default' ),
					'toppermicondub' => array( 'type' => 'booleannumber', 'domain' => 'default' ),
					'topautrpermicondu' => array( 'type' => 'booleannumber', 'domain' => 'default' ),
					'accosocfam' => array( 'type' => 'nov', 'domain' => 'default' ),
					'accosocindi' => array( 'type' => 'nov', 'domain' => 'default' ),
					'soutdemarsoc' => array( 'type' => 'nov', 'domain' => 'default' ),
					'concoformqualiemploi' => array( 'type' => 'nos', 'domain' => 'default' ),
					'drorsarmianta2' => array( 'type' => 'nos', 'domain' => 'default' ),
					'statutoccupation' => array( 'values' => array( 'proprietaire', 'locataire' ) ),
					'suivimedical'
				)
			),
			'Formattable' => array(
				'suffix' => array(
					'libderact66_metier_id',
					'libactdomi66_metier_id',
					'libemploirech66_metier_id',
				)
			)
		);
		
		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('WebrsaDsp');
		
		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * Export de la liste des champs disponibles pour le moteur de recherche
		 * dans le fichier app/tmp/Dsp__searchQuery__cgXX.csv.
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les méthodes qui ne font rien.
		 */
		public function prechargement() {
			$success = true;

			$query = $this->WebrsaDsp->searchQuery();
			$success = $success && !empty( $query );

			// Export des champs disponibles
			App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
			$fileName = TMP.DS.'logs'.DS.__CLASS__.'__searchQuery__cg'.Configure::read( 'Cg.departement' ).'.csv';
			ConfigurableQueryFields::exportQueryFields( $query, 'dsps', $fileName );

			$results = $this->WebrsaDsp->options();
			$success = $success && !empty( $results );

			return $success;
		}
		
		/**
		 * Vérification que les champs spécifiés dans le paramétrage par les clés
		 * Dsps.index.fields, Dsps.index.innerTable et Dsps.exportcsv dans le
		 * webrsa.inc existent bien dans la requête de recherche renvoyée par
		 * la méthode search().
		 *
		 * @param array $params Paramètres supplémentaires (clé 'query' possible)
		 * @return array
		 * @todo Utiliser AbstractWebrsaRecherche
		 */
		public function checkParametrage( array $params = array() ) {
			$keys = array( 'Dsps.index.fields', 'Dsps.index.innerTable', 'Dsps.exportcsv' );
			$query = $this->WebrsaDsp->search( array() );

			App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
			$return = ConfigurableQueryFields::getErrors( $keys, $query );

			return $return;
		}
	}
?>