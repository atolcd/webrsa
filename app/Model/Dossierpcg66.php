<?php

/**
 * Code source de la classe Dossierpcg66.
 *
 * PHP 5.3
 *
 * @package app.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe Dossierpcg66 ...
 *
 * @package app.Model
 */
class Dossierpcg66 extends AppModel {

    public $name = 'Dossierpcg66';
    public $recursive = -1;
    public $virtualFields = array(
        'nbpropositions' => array(
            'type' => 'integer',
            'postgres' => '(
					SELECT COUNT(*)
						FROM decisionsdossierspcgs66
						WHERE
							decisionsdossierspcgs66.dossierpcg66_id = "%s"."id"
				)',
        ),
    );
    public $actsAs = array(
        'Pgsqlcake.PgsqlAutovalidate',
        'Formattable' => array(
            'suffix' => array('user_id')
        ),
        'Enumerable' => array(
            'fields' => array(
                'orgpayeur',
                'iscomplet',
                'haspiecejointe',
                'istransmis'
            )
        )
    );
    public $belongsTo = array(
        'Bilanparcours66' => array(
            'className' => 'Bilanparcours66',
            'foreignKey' => 'bilanparcours66_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Contratinsertion' => array(
            'className' => 'Contratinsertion',
            'foreignKey' => 'contratinsertion_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Decisiondefautinsertionep66' => array(
            'className' => 'Decisiondefautinsertionep66',
            'foreignKey' => 'decisiondefautinsertionep66_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Dossierpcg66pcd' => array(
            'className' => 'Dossierpcg66',
            'foreignKey' => 'dossierpcg66pcd_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Foyer' => array(
            'className' => 'Foyer',
            'foreignKey' => 'foyer_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Originepdo' => array(
            'className' => 'Originepdo',
            'foreignKey' => 'originepdo_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Poledossierpcg66' => array(
            'className' => 'Poledossierpcg66',
            'foreignKey' => 'poledossierpcg66_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Serviceinstructeur' => array(
            'className' => 'Serviceinstructeur',
            'foreignKey' => 'serviceinstructeur_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Typepdo' => array(
            'className' => 'Typepdo',
            'foreignKey' => 'typepdo_id',
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
        )
    );
    public $hasOne = array(
        'Dossierpcg66svt' => array(
            'className' => 'Dossierpcg66',
            'foreignKey' => 'dossierpcg66pcd_id',
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
        'Primoanalyse' => array(
            'className' => 'Primoanalyse',
            'foreignKey' => 'dossierpcg66_id',
            'dependent' => false,
        ),
    );
    public $hasMany = array(
        'Decisiondossierpcg66' => array(
            'className' => 'Decisiondossierpcg66',
            'foreignKey' => 'dossierpcg66_id',
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
        'Personnepcg66' => array(
            'className' => 'Personnepcg66',
            'foreignKey' => 'dossierpcg66_id',
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
                'Fichiermodule.modele = \'Dossierpcg66\'',
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
        'orgpayeur' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Champ obligatoire',
            )
        ),
    );
	
	/**
	 * Modèles utilisés par ce modèle.
	 * 
	 * @var array
	 */
	public $uses = array(
		'WebrsaDossierpcg66'
	);

    /**
     *	@deprecated since version 3.1	Cette function n'est pas utilisée
     */
    public function etatPcg66($dossierpcg66) {
		trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
        $dossierpcg66 = Hash::expand(Hash::filter((array) ( $dossierpcg66 )));

        $typepdo_id = Set::classicExtract($dossierpcg66, 'Dossierpcg66.typepdo_id');
    }
	
    /**
     *   AfterSave
     */
    public function afterSave($created) {
        $return = parent::afterSave($created);

		$this->WebrsaDossierpcg66->updatePositionsPcgsById($this->id);
        $return = $this->_updateDecisionCerParticulier($created) && $return;

        return $return;
    }

    protected function _updateDecisionCerParticulier($created) {
        $success = true;

        $decisiondossierpcg66 = $this->Decisiondossierpcg66->find(
                'first', array(
            'conditions' => array(
                'Decisiondossierpcg66.dossierpcg66_id' => $this->id
            ),
            'contain' => array(
                'Decisionpdo'
            ),
            'order' => array('Decisiondossierpcg66.datevalidation DESC')
                )
        );

        $dossierpcg66 = $this->find(
                'first', array(
            'conditions' => array(
                'Dossierpcg66.id' => $this->id
            ),
            'contain' => false
                )
        );

        if (!empty($decisiondossierpcg66) && isset($decisiondossierpcg66['Decisiondossierpcg66']['validationproposition'])) {
            $dateDecision = $decisiondossierpcg66['Decisiondossierpcg66']['datevalidation'];
            $propositiondecision = $decisiondossierpcg66['Decisionpdo']['decisioncerparticulier'];
            if (( $decisiondossierpcg66['Decisiondossierpcg66']['validationproposition'] == 'O' ) && ( ( ( $decisiondossierpcg66['Decisiondossierpcg66']['retouravistechnique'] == '0' ) && ( $decisiondossierpcg66['Decisiondossierpcg66']['vuavistechnique'] == '0' ) ) || ( ( $decisiondossierpcg66['Decisiondossierpcg66']['retouravistechnique'] == '1' ) && ( $decisiondossierpcg66['Decisiondossierpcg66']['vuavistechnique'] == '1' ) ) )) {

                if ($propositiondecision == 'N') {
                    $success = $this->Contratinsertion->updateAllUnBound(
                                    array(
                                'Contratinsertion.decision_ci' => "'" . $propositiondecision . "'",
                                'Contratinsertion.datevalidation_ci' => null,
                                'Contratinsertion.datedecision' => "'" . $dateDecision . "'",
                                'Contratinsertion.positioncer' => '\'nonvalid\'',
                                    ), array(
                                'Contratinsertion.id' => $dossierpcg66['Dossierpcg66']['contratinsertion_id']
                                    )
                            ) && $success;
                } else {
                    $success = $this->Contratinsertion->updateAllUnBound(
                                    array(
                                'Contratinsertion.decision_ci' => "'" . $propositiondecision . "'",
                                'Contratinsertion.datevalidation_ci' => "'" . $dateDecision . "'",
                                'Contratinsertion.datedecision' => "'" . $dateDecision . "'",
                                'Contratinsertion.positioncer' => '\'encours\'',
                                    ), array(
                                'Contratinsertion.id' => $dossierpcg66['Dossierpcg66']['contratinsertion_id']
                                    )
                            ) && $success;
                }

				if( !empty( $dossierpcg66['Dossierpcg66']['contratinsertion_id'] ) ) {
					$success = $success && $this->Contratinsertion->WebrsaContratinsertion->updatePositionsCersById( $dossierpcg66['Dossierpcg66']['contratinsertion_id'] );
				}
            }
        }

        return $success;
    }

    /**
     * Retourne l'id du dossier à partir de l'id du dosiserpcg66
     *
     * @param integer $dossierpcg66_id
     * @return integer
     */
    public function dossierId($dossierpcg66_id) {
        $querydata = array(
            'fields' => array('Foyer.dossier_id'),
            'joins' => array(
                $this->join('Foyer', array('type' => 'INNER'))
            ),
            'conditions' => array(
                'Dossierpcg66.id' => $dossierpcg66_id
            ),
            'recursive' => -1
        );

        $dossierpcg66 = $this->find('first', $querydata);

        if (!empty($dossierpcg66)) {
            return $dossierpcg66['Foyer']['dossier_id'];
        } else {
            return null;
        }
    }
	
	/**
	 * Préparation des données du formulaire d'ajout ou de modification d'un
	 * Dossier PCG
	 *
	 * @param integer $foyer_id
	 * @param integer $dossierpcg66_id
	 * @return array
	 * @throws InternalErrorException
	 * @throws NotFoundException
	 * @deprecated since version 3.1	Cette function n'est pas utilisée
	 */
	public function prepareFormDataAddEdit($foyer_id, $dossierpcg66_id) {
		trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
		if (!empty($dossierpcg66_id)) {
			$querydataDossierpcg66Actuel['conditions'] = array(
				'Dossierpcg66.id' => $dossierpcg66_id
			);
			$dataDossierpcg66Actuel = $this->find('first', $querydataDossierpcg66Actuel);

			// Il faut que l'enregistrement à modifier existe
			if (empty($dataDossierpcg66Actuel)) {
				throw new NotFoundException();
			}

			$data = $dataDossierpcg66Actuel;
		} else {
			$data = array(
				'Dossierpcg66' => array(
					'id' => null,
					'foyer_id' => $foyer_id,
					'user_id' => null
				)
			);

			$dossierpcg66Pcd = $this->find(
					'first', array(
				'conditions' => array(
					'Dossierpcg66.foyer_id' => $foyer_id
				),
				'recursive' => -1,
				'order' => array('Dossierpcg66.created DESC'),
				'limit' => 1
					)
			);

			$data['Dossierpcg66']['user_id'] = $dossierpcg66Pcd['Dossierpcg66']['user_id'];
		}

		return $data;
	}
}

?>