<?php	
	/**
	 * Code source de la classe Traitementpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Traitementpdo ...
	 *
	 * @package app.Model
	 */
	class Traitementpdo extends AppModel
	{
		public $name = 'Traitementpdo';

		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'hascourrier',
					'hasrevenu',
					'haspiecejointe',
					'hasficheanalyse',
					'regime',
					'aidesubvreint',
					'dureedepart',
					'dureefinperiode'
				)
			),
			'Autovalidate2',
			'Gedooo.Gedooo'
		);

		public $validate = array(
			'propopdo_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'descriptionpdo_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'traitementtypepdo_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'datereception' => array(
				'date' => array(
					'rule' => 'date',
					'required' => false,
					'allowEmpty' => false,
					'message' => 'Merci de rentrer une date valide'
				)
			),
			'datedepart' => array(
				'date' => array(
					'rule' => 'date',
					'required' => false,
					'allowEmpty' => false,
					'message' => 'Merci de rentrer une date valide'
				)
			),
			'daterevision' => array(
				'date' => array(
					'rule' => 'date',
					'required' => false,
					'allowEmpty' => false,
					'message' => 'Merci de rentrer une date valide'
				)
			),
			'dateecheance' => array(
				'date' => array(
					'rule' => 'date',
					'required' => false,
					'allowEmpty' => false,
					'message' => 'Merci de rentrer une date valide'
				)
			),
			'regime' => array(
				array(
					'rule' => 'notEmpty',
					'required' => false,
					'allowEmpty' => false,
					'message' => 'Champ obligatoire'
				)
			),
			'dtdebutactivite' => array(
				'date' => array(
					'rule' => 'date',
					'required' => false,
					'allowEmpty' => false,
					'message' => 'Merci de rentrer une date valide'
				)
			),
			'nrmrcs' => array(
				array(
					'rule' => 'alphaNumeric',
					'required' => false,
					'allowEmpty' => false
				)
			),
			'raisonsocial' => array(
				array(
					'rule' => 'notEmpty',
					'required' => false,
					'allowEmpty' => false,
					'message' => 'Champ obligatoire'
				)
			),
			'dtdebutperiode' => array(
				'date' => array(
					'rule' => 'date',
					'required' => false,
					'allowEmpty' => false,
					'message' => 'Merci de rentrer une date valide'
				)
			),
			'datefinperiode' => array(
				'date' => array(
					'rule' => 'date',
					'required' => false,
					'allowEmpty' => false,
					'message' => 'Merci de rentrer une date valide'
				)
			),
			'dtprisecompte' => array(
				'date' => array(
					'rule' => 'date',
					'required' => false,
					'allowEmpty' => false,
					'message' => 'Merci de rentrer une date valide'
				)
			),
			'dtecheance' => array(
				'date' => array(
					'rule' => 'date',
					'required' => false,
					'allowEmpty' => false,
					'message' => 'Merci de rentrer une date valide'
				)
			),
			'chaffvnt' => array(
				array(
					'rule' => 'numeric',
					'required' => false,
					'allowEmpty' => false
				)
			),
			'chaffsrv' => array(
				array(
					'rule' => 'numeric',
					'required' => false,
					'allowEmpty' => false
				)
			),
			'benefoudef' => array(
				array(
					'rule' => 'numeric',
					'required' => false,
					'allowEmpty' => false
				)
			),
			'amortissements' => array(
				array(
					'rule' => 'numeric',
					'required' => false,
					'allowEmpty' => false
				)
			),
			'autrecorrection' => array(
				array(
					'rule' => 'numeric',
					'required' => false,
					'allowEmpty' => false
				)
			),
			'dureeecheance' => array(
				array(
					'rule' => 'notEmpty',
					'required' => false,
					'allowEmpty' => false
				)
			),
			'dureedepart' => array(
				array(
					'rule' => 'notEmpty',
					'required' => false,
					'allowEmpty' => false
				)
			)
		);

		public $belongsTo = array(
			'Propopdo' => array(
				'className' => 'Propopdo',
				'foreignKey' => 'propopdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Descriptionpdo' => array(
				'className' => 'Descriptionpdo',
				'foreignKey' => 'descriptionpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Traitementtypepdo' => array(
				'className' => 'Traitementtypepdo',
				'foreignKey' => 'traitementtypepdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Fichiertraitementpdo' => array(
				'className' => 'Fichiertraitementpdo',
				'foreignKey' => 'traitementpdo_id',
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
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Traitementpdo\'',
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

		public $hasAndBelongsToMany = array(
			'Courrierpdo' => array(
				'className' => 'Courrierpdo',
				'joinTable' => 'courrierspdos_traitementspdos',
				'foreignKey' => 'traitementpdo_id',
				'associationForeignKey' => 'courrierpdo_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'CourrierpdoTraitementpdo'
			),
		);

		public function beforeSave($options = array()) {
			if ((!isset($this->data['Traitementpdo']['daterevision']) || empty($this->data['Traitementpdo']['daterevision']) ) && (!isset($this->data['Traitementpdo']['dateecheance']) || empty($this->data['Traitementpdo']['dateecheance']))) {
				$this->data['Traitementpdo']['clos'] = 1;
			}
			return parent::beforeSave($options);
		}

		public function sauvegardeTraitement($data) {
			$passageEpd = false;
			//Sauvegarde des couriers liés à un traitement si présents
			if( isset( $data['Courrierpdo'] ) ){
				$dataCourrierIds = Set::extract( $data, '/Courrierpdo[checked=1]/id' );
				if( count( $dataCourrierIds ) != 0 ){
					$dataContenutextareacourrierpdo = $data['Contenutextareacourrierpdo'];
					unset( $data['Courrierpdo'], $data['Contenutextareacourrierpdo'] );
				}
			}

			$dossierep = 0;
			if (isset($data['Traitementpdo']['id']))
				$dossierep = $this->Saisinepdoep66->find(
					'count',
					array(
						'conditions'=>array(
							'Saisinepdoep66.traitementpdo_id'=>$data['Traitementpdo']['id']
						)
					)
				);

			if ($dossierep==0 && $data['Traitementpdo']['traitementtypepdo_id']==Configure::read( 'traitementEnCoursId' )) {
				$descriptionpdo = $this->Descriptionpdo->find(
					'first',
					array(
						'conditions'=>array(
							'Descriptionpdo.id'=>$data['Traitementpdo']['descriptionpdo_id']
						),
						'contain'=>false
					)
				);
				$passageEpd = ($descriptionpdo['Descriptionpdo']['declencheep']==1) ? true : false;
			}

			$success = true;

			$has = array('hascourrier', 'hasrevenu', 'haspiecejointe', 'hasficheanalyse');
			foreach ($has as $field) {
				if (empty($data['Traitementpdo'][$field]))
					unset($data['Traitementpdo'][$field]);
			}
			$success = $this->saveAll( $data, array( 'validate' => 'first', 'atomic' => false ) ) && $success;

			$traitementpdo_id = $this->id;
			if( !empty( $dataCourrierIds ) ){
				foreach( $dataCourrierIds as $dataCourrierId ){
					$dataCourrierpdoTraitementpdo = array( 'CourrierpdoTraitementpdo' => array( 'courrierpdo_id' => $dataCourrierId, 'traitementpdo_id' => $traitementpdo_id ) );
					$this->CourrierpdoTraitementpdo->create( $dataCourrierpdoTraitementpdo );
					$success = $this->CourrierpdoTraitementpdo->save() && $success;

					if( $success ){
						foreach( array_keys( $dataContenutextareacourrierpdo ) as $key ) {
							$dataContenutextareacourrierpdo[$key]['courrierpdo_traitementpdo_id'] = $this->CourrierpdoTraitementpdo->id;
						}
						$success = $this->CourrierpdoTraitementpdo->Contenutextareacourrierpdo->saveAll( $dataContenutextareacourrierpdo, array( 'atomic' => false ) ) && $success;
					}
				}
			}

			if ( isset( $data['Traitementpdo']['traitmentpdoIdClore'] ) && !empty( $data['Traitementpdo']['traitmentpdoIdClore'] ) ) {
				foreach( $data['Traitementpdo']['traitmentpdoIdClore'] as $id => $clore ) {
					if ( $clore==1 ) {
						$success = $this->updateAllUnBound(array('Traitementpdo.clos'=>1),array('"Traitementpdo"."id"'=>$id)) && $success;
					}
				}
			}

			if ($passageEpd) {
				$propopdo = $this->Propopdo->find(
					'first',
					array(
						'conditions'=>array(
							'Propopdo.id' => $data['Traitementpdo']['propopdo_id']
						)
					)
				);

				$dataDossierEp = array(
					'Dossierep' => array(
						'personne_id' => $propopdo['Propopdo']['personne_id'],
						'themeep' => 'saisinespdoseps66'
					)
				);

				$this->Saisinepdoep66->Dossierep->create( $dataDossierEp );
				$success = $this->Saisinepdoep66->Dossierep->save() && $success;

				$dataSaisineepdpdo66 = array(
					'Saisinepdoep66' => array(
						'traitementpdo_id' => $this->id,
						'dossierep_id' => $this->Saisinepdoep66->Dossierep->id
					)
				);
				$this->Saisinepdoep66->create( $dataSaisineepdpdo66 );
				$success = $this->Saisinepdoep66->save() && $success;
			}

			return $success;
		}

		/**
		* Retourne l'id technique du dossier RSA auquel ce traitement est lié.
		*/

		public function dossierId( $traitementpdo_id ){
			$result = $this->find(
				'first',
				array(
					'fields' => array( 'Foyer.dossier_id' ),
					'conditions' => array(
						'Traitementpdo.id' => $traitementpdo_id
					),
					'contain' => false,
					'joins' => array(
						array(
							'table'      => 'propospdos',
							'alias'      => 'Propopdo',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Propopdo.id = Traitementpdo.propopdo_id' )
						),
						array(
							'table'      => 'personnes',
							'alias'      => 'Personne',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Propopdo.personne_id = Personne.id' )
						),
						array(
							'table'      => 'foyers',
							'alias'      => 'Foyer',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Personne.foyer_id = Foyer.id' )
						),
					)
				)
			);

			if( !empty( $result ) ) {
				return $result['Foyer']['dossier_id'];
			}
			else {
				return null;
			}
		}
	}
?>