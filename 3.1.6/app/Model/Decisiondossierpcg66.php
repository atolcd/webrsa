<?php

/**
 * Code source de la classe Decisiondossierpcg66.
 *
 * PHP 5.3
 *
 * @package app.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe Decisiondossierpcg66 ...
 *
 * @package app.Model
 */
class Decisiondossierpcg66 extends AppModel {

    public $name = 'Decisiondossierpcg66';
    public $recursive = -1;
    public $actsAs = array(
        'Postgres.PostgresAutovalidate',
        'Formattable' => array(
            'suffix' => array(
                'orgtransmisdossierpcg66_id'
            )
        ),
        'Enumerable' => array(
//            'fields' => array(
//                'avistechnique',
//                'validationproposition',
//                'etatop',
//                'typersa',
//                'recidive',
//                'phase',
//                'defautinsertion',
//                'haspiecejointe',
//                'instrencours'
//            )
        ),
        'Gedooo.Gedooo',
        'ModelesodtConditionnables' => array(
            66 => array(
                'PCG66/propositiondecision.odt',
            )
        )
    );
    public $belongsTo = array(
        'Dossierpcg66' => array(
            'className' => 'Dossierpcg66',
            'foreignKey' => 'dossierpcg66_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Decisionpdo' => array(
            'className' => 'Decisionpdo',
            'foreignKey' => 'decisionpdo_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Compofoyerpcg66' => array(
            'className' => 'Compofoyerpcg66',
            'foreignKey' => 'compofoyerpcg66_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Decisionpcg66' => array(
            'className' => 'Decisionpcg66',
            'foreignKey' => 'decisionpcg66_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Orgtransmisdossierpcg66' => array(
            'className' => 'Orgtransmisdossierpcg66',
            'foreignKey' => 'orgtransmisdossierpcg66_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Useravistechnique' => array(
            'className' => 'User',
            'foreignKey' => 'useravistechnique_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Userproposition' => array(
            'className' => 'User',
            'foreignKey' => 'userproposition_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
    public $hasAndBelongsToMany = array(
        'Decisiontraitementpcg66' => array(
            'className' => 'Decisiontraitementpcg66',
            'joinTable' => 'decisionsdossierspcgs66_decisionstraitementspcgs66',
            'foreignKey' => 'decisiondossierpcg66_id',
            'associationForeignKey' => 'decisiontraitementpcg66_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => '',
            'with' => 'Decisiondossierpcg66Decisiontraitementpcg66'
        ),
        'Decisionpersonnepcg66' => array(
            'className' => 'Decisionpersonnepcg66',
            'joinTable' => 'decisionsdossierspcgs66_decisionspersonnespcgs66',
            'foreignKey' => 'decisiondossierpcg66_id',
            'associationForeignKey' => 'decisionpersonnepcg66_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => '',
            'with' => 'Decisiondossierpcg66Decisionpersonnepcg66'
        ),
        'Typersapcg66' => array(
            'className' => 'Typersapcg66',
            'joinTable' => 'decisionsdossierspcgs66_typesrsapcgs66',
            'foreignKey' => 'decisiondossierpcg66_id',
            'associationForeignKey' => 'typersapcg66_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => '',
            'with' => 'Decisiondossierpcg66Typersapcg66'
        ),
        'Notificationdecisiondossierpcg66' => array(
            'className' => 'Orgtransmisdossierpcg66',
            'joinTable' => 'decisionsdossierspcgs66_orgstransmisdossierspcgs66',
            'foreignKey' => 'decisiondossierpcg66_id',
            'associationForeignKey' => 'orgtransmisdossierpcg66_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => '',
            'with' => 'Decdospcg66Orgdospcg66'
        )
    );
    public $hasMany = array(
        'Fichiermodule' => array(
            'className' => 'Fichiermodule',
            'foreignKey' => false,
            'dependent' => false,
            'conditions' => array(
                'Fichiermodule.modele = \'Decisiondossierpcg66\'',
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
    public $validate = array(
        'etatop' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Champ obligatoire'
            )
        )
    );
	
	public $virtualFields = array(
			'dernier' => array(
				'type'      => 'boolean',
				'postgres'  => '"%s"."id" IN (
					SELECT a.id FROM decisionsdossierspcgs66 AS a
					WHERE a.dossierpcg66_id = "%s"."dossierpcg66_id"
					AND (a.etatdossierpcg IS NULL OR a.etatdossierpcg != \'annule\')
					ORDER BY a.created DESC
					LIMIT 1)'
			),
	);
	
	/**
	 * Les modèles qui seront utilisés par ce modèle.
	 *
	 * @var array
	 */
	public $uses = array('WebrsaDecisiondossierpcg66');

    public function beforeSave($options = array()) {
        $return = parent::beforeSave($options);

        if (Configure::read('nom_form_pdo_cg') == 'cg66') {
            $validationdecision = Set::extract($this->data, 'Decisionpropopdo.validationdecision');

            $etat = 'attinstr';

            if (!is_numeric($validationdecision))
                $etat = 'attval';
            elseif (is_numeric($validationdecision) && $validationdecision == 1)
                $etat = 'dossiertraite';

            $this->data['Decisionpropopdo']['etatdossierpdo'] = $etat;
        }

        return $return;
    }

    /**
     * Retourne l'id technique du dossier RSA auquel ce traitement est lié.
     */
    public function dossierId($decisiondossierpcg66_id) {
        $result = $this->find(
                'first', array(
            'fields' => array('Foyer.dossier_id'),
            'conditions' => array(
                'Decisiondossierpcg66.id' => $decisiondossierpcg66_id
            ),
            'contain' => false,
            'joins' => array(
                array(
                    'table' => 'dossierspcgs66',
                    'alias' => 'Dossierpcg66',
                    'type' => 'INNER',
                    'foreignKey' => false,
                    'conditions' => array('Dossierpcg66.id = Decisiondossierpcg66.dossierpcg66_id')
                ),
                array(
                    'table' => 'foyers',
                    'alias' => 'Foyer',
                    'type' => 'INNER',
                    'foreignKey' => false,
                    'conditions' => array('Dossierpcg66.foyer_id = Foyer.id')
                )
            )
                )
        );

        if (!empty($result)) {
            return $result['Foyer']['dossier_id'];
        } else {
            return null;
        }
    }
	
	/**
	 * Fonction permettant de récupérer les décisions qui ont été uniquement
	 * transmises à l'OP
	 * 
	 * @deprecated since version 3.1
	 */
	public function sqDatetransmissionOp($dossierpcg66Id = 'Dossierpcg66.id') {
		return $this->sq(
						array(
							'alias' => 'decisionsdossierspcgs66',
							'fields' => array('decisionsdossierspcgs66.id'),
							'joins' => array(
								array_words_replace(
										$this->Decisiondossierpcg66->join('Dossierpcg66'), array(
									'Decisiondossierpcg66' => 'decisionsdossierspcgs66',
									'Dossierpcg66' => 'dossierspcgs66'
										)
								)
							),
							'conditions' => array(
								'decisionsdossierspcgs66.dossierpcg66_id = ' . $dossierpcg66Id,
								'decisionsdossierspcgs66.etatop' => 'transmis',
								'decisionsdossierspcgs66.datetransmissionop IS NOT NULL'
							),
							'order' => array('decisionsdossierspcgs66.datetransmissionop DESC'),
							'contain' => false,
							'limit' => 1
						)
		);
	}
	
	/**
	 * Change un etat de dossier PCG dans le cas ou la position est 'decisionvalid'.
	 * Ajoute une date d'impression.
	 * Renvoi vrai si le dossier PCG a déjà été imprimmé.
	 * 
	 * @param mixed $ids
	 * @return boolean
	 * @deprecated since version 3.1	(utilisé dans Cohortesdossierspcgs66Controller::notificationsCohorte)
	 */
	public function updateDossierpcg66Dateimpression($ids) {
		$query = array(
			'fields' => array( 'Dossierpcg66.id' ),
			'conditions' => array(
				'Decisiondossierpcg66.id' => $ids,
				'Dossierpcg66.etatdossierpcg' => 'decisionvalid',
			),
			'contain' => false,
			'joins' => array(
				$this->Dossierpcg66->join( 'Decisiondossierpcg66', array( 'type' => 'INNER' ) )
			)
		);
		$results = $this->Dossierpcg66->find( 'all', $query );

		return ( count( $results ) === 0 ) || $this->Decisiondossierpcg66->Dossierpcg66->updateAllUnBound(
			array(
				'Dossierpcg66.dateimpression' => "'" . date('Y-m-d') . "'",
				'Dossierpcg66.etatdossierpcg' => '\'atttransmisop\''
			), 
			array(
				'Dossierpcg66.id' => Hash::extract( $results, '{n}.Dossierpcg66.id' )
			)
		);
	}
}

?>