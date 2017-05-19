<?php

/**
 * Code source de la classe Decisiondossierpcg66.
 *
 * PHP 5.3
 *
 * @package app.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses( 'AppModel', 'Model' );

/**
 * La classe Decisiondossierpcg66 ...
 *
 * @package app.Model
 */
class Decisiondossierpcg66 extends AppModel
{

    public $name = 'Decisiondossierpcg66';

    public $actsAs = array(
        'Gedooo.Gedooo',
        'ModelesodtConditionnables' => array(
            66 => array(
                'PCG66/propositiondecision.odt',
            )
        ),
		'Validation2.Validation2Formattable',
		'Validation2.Validation2RulesFieldtypes',
		'Postgres.PostgresAutovalidate',
    );

	/**
	 * Les modèles qui seront utilisés par ce modèle.
	 *
	 * @var array
	 */
	public $uses = array('WebrsaDecisiondossierpcg66');

    public $validate = array(
        'etatop' => array(
            NOT_BLANK_RULE_NAME => array(
                'rule' => array( NOT_BLANK_RULE_NAME ),
                'message' => 'Champ obligatoire'
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
}

?>