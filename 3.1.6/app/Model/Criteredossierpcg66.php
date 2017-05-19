<?php

/**
 * Code source de la classe Criteredossierpcg66.
 *
 * PHP 5.3
 *
 * @package app.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe Criteredossierpcg66 ...
 *
 * @package app.Model
 * @deprecated since version 3.0.0
 * @see WebrsaRechercheDossierpcg66
 */
class Criteredossierpcg66 extends AppModel {

    public $name = 'Criteredossierpcg66';
    public $useTable = false;
    public $actsAs = array('Conditionnable');

    /**
     * Complète le querydata de recherche avec des champs virtuels et les
     * renseignements du référent de parcours pour les méthode searchDossier()
     * et searchGestionnaire().
     *
     * @param array $query
     * @param array $params
     * @return array
     */
    protected function _completeQuery(array $query, array $params) {
        $Dossierpcg66 = ClassRegistry::init('Dossierpcg66');

        // ------------- Récupération de la liste des motifs  ----------
        $qdLibellesSituationpdo = array(
            'fields' => array(
                'Situationpdo.libelle'
            ),
            'conditions' => array(
                'Personnepcg66.dossierpcg66_id = Dossierpcg66.id'
            ),
            'joins' => array(
                $Dossierpcg66->Personnepcg66->join('Personnepcg66Situationpdo', array('type' => 'LEFT OUTER')),
                $Dossierpcg66->Personnepcg66->Personnepcg66Situationpdo->join('Situationpdo', array('type' => 'LEFT OUTER'))
            )
        );

        $vfLibellesSituationpdo = $Dossierpcg66->Personnepcg66->vfListe($qdLibellesSituationpdo);
        $query['fields'][] = "{$vfLibellesSituationpdo} AS \"Personnepcg66__listemotifs\"";

        // ------------- Récupération de la liste des motifs  ----------
        $qdLibellesStatutpdo = array(
            'fields' => array(
                'Statutpdo.libelle'
            ),
            'conditions' => array(
                'Personnepcg66.dossierpcg66_id = Dossierpcg66.id'
            ),
            'joins' => array(
                $Dossierpcg66->Personnepcg66->join('Personnepcg66Statutpdo', array('type' => 'LEFT OUTER')),
                $Dossierpcg66->Personnepcg66->Personnepcg66Statutpdo->join('Statutpdo', array('type' => 'LEFT OUTER'))
            )
        );

        $vfLibellesStatutpdo = $Dossierpcg66->Personnepcg66->vfListe($qdLibellesStatutpdo);
        $query['fields'][] = "{$vfLibellesStatutpdo} AS \"Personnepcg66__listestatuts\"";

        // -- Récupération de la liste des traitements et des dates échéance  ---

        $qdTraitementpcg66 = array(
            'joins' => array(
                $Dossierpcg66->Personnepcg66->Traitementpcg66->join('Personnepcg66', array('type' => 'INNER'))
            ),
            'conditions' => array(
                'Personnepcg66.dossierpcg66_id = Dossierpcg66.id'
            ),
            'contain' => false
        );

        $qdTraitementpcg66Typetraitement = $qdTraitementpcg66;
        $qdTraitementpcg66Typetraitement['fields'] = array('Traitementpcg66.typetraitement',);
        $vfTraitementpcg66Typetraitement = $Dossierpcg66->Personnepcg66->Traitementpcg66->vfListe($qdTraitementpcg66Typetraitement);
        $query['fields'][] = "{$vfTraitementpcg66Typetraitement} AS \"Dossierpcg66__listetraitements\"";

        $qdTraitementpcg66Dateecheance = $qdTraitementpcg66;
        $qdTraitementpcg66Dateecheance['fields'] = array('Traitementpcg66.dateecheance',);
        $vfTraitementpcg66Dateecheance = $Dossierpcg66->Personnepcg66->Traitementpcg66->vfListe($qdTraitementpcg66Dateecheance);
        $query['fields'][] = "{$vfTraitementpcg66Dateecheance} AS \"Dossierpcg66__dateecheance\"";

        // ----- Récupération de la liste des organismes de décisions -------
        $qdOrganismes = array(
            'fields' => array(
                'Orgtransmisdossierpcg66.name'
            ),
            'conditions' => array(
                'Decisiondossierpcg66.dossierpcg66_id = Dossierpcg66.id'
            ),
            'joins' => array(
                $Dossierpcg66->Decisiondossierpcg66->join('Decdospcg66Orgdospcg66'),
                $Dossierpcg66->Decisiondossierpcg66->Decdospcg66Orgdospcg66->join('Orgtransmisdossierpcg66')
            ),
            'order' => array('Decisiondossierpcg66.created DESC'),
            'contain' => false
        );
        $vfNameOrganisme = $Dossierpcg66->Decisiondossierpcg66->vfListe($qdOrganismes);
        $query['fields'][] = "{$vfNameOrganisme} AS \"Orgtransmisdossierpcg66__listorgs\"";

        $query = $Dossierpcg66->Foyer->Personne->PersonneReferent->completeQdReferentParcours($query, $params);

        return $query;
    }

    /**
     *
     * @param array $params
     * @param array $mesCodesInsee
     * @param boolean $filtre_zone_geo
     * @return array
     */
    public function searchDossier($params, $mesCodesInsee, $filtre_zone_geo) {
        $Dossierpcg66 = ClassRegistry::init('Dossierpcg66');

        $sqDernierDetailcalculdroitrsa = $Dossierpcg66->Foyer->Dossier->Detaildroitrsa->Detailcalculdroitrsa->sqDernier('Detaildroitrsa.id');
        $sqDerniereDecisiondossierpcg66 = $Dossierpcg66->Decisiondossierpcg66->WebrsaDecisiondossierpcg66->sqDernier('Dossierpcg66.id');

        $conditions = array();
		
        /// Filtre zone géographique
        $conditions[] = $this->conditionsZonesGeographiques($filtre_zone_geo, $mesCodesInsee);

        $conditions = $this->conditionsAdresse($conditions, $params, $filtre_zone_geo, $mesCodesInsee);
        $conditions = $this->conditionsPersonneFoyerDossier($conditions, $params);
        $conditions = $this->conditionsDernierDossierAllocataire($conditions, $params);
        $conditions = $this->conditionsDetailcalculdroitrsa($conditions, $params);

        $conditions = $this->conditionsDates($conditions, $params, 'Dossierpcg66.dateaffectation');

        /// Critères
        $originepdo = Set::extract($params, 'Dossierpcg66.originepdo_id');
        $gestionnaire = Set::extract($params, 'Dossierpcg66.user_id');
        $gestionnaireAvistechnique = Set::extract($params, 'Decisiondossierpcg66.useravistechnique_id');
        $gestionnaireValidation = Set::extract($params, 'Decisiondossierpcg66.userproposition_id');
        $datereceptionpdo = Set::extract($params, 'Dossierpcg66.datereceptionpdo');
        $datereceptionpdo_to = Set::extract($params, 'Dossierpcg66.datereceptionpdo_to');
        $datereceptionpdo_from = Set::extract($params, 'Dossierpcg66.datereceptionpdo_from');
        $typepdo_id = Set::extract($params, 'Dossierpcg66.typepdo_id');
        $orgpayeur = Set::extract($params, 'Dossierpcg66.orgpayeur');
        $decisionpdo_id = Set::extract($params, 'Decisiondossierpcg66.decisionpdo_id');
        $poledossierpcg66_id = Set::extract($params, 'Dossierpcg66.poledossierpcg66_id');

        /// Critères sur les PDOs - date de reception de la PDO
        if (!empty($datereceptionpdo)) {
            $datereceptionpdo_from = "{$datereceptionpdo_from['year']}-{$datereceptionpdo_from['month']}-{$datereceptionpdo_from['day']}";
            $datereceptionpdo_to = "{$datereceptionpdo_to['year']}-{$datereceptionpdo_to['month']}-{$datereceptionpdo_to['day']}";
            $conditions[] = "Dossierpcg66.datereceptionpdo BETWEEN '{$datereceptionpdo_from}' AND '{$datereceptionpdo_to}'";
        }

        // Décision de la PDO
        if (!empty($decisionpdo)) {
            $conditions[] = 'Decisiondossierpcg66.decisionpdo_id = \'' . Sanitize::clean($decisionpdo, array('encode' => false)) . '\'';
        }

        // Filtre sur l'état du dossier PCG
        $etatdossierpcg = Set::extract($params, 'Dossierpcg66.etatdossierpcg');
        if (isset($params['Dossierpcg66']['etatdossierpcg']) && !empty($params['Dossierpcg66']['etatdossierpcg'])) {
            $conditions[] = '( Dossierpcg66.etatdossierpcg IN ( \'' . implode('\', \'', $etatdossierpcg) . '\' ) )';
        }


        // Origine de la PDO
        if (!empty($originepdo)) {
            $conditions[] = 'Dossierpcg66.originepdo_id = \'' . Sanitize::clean($originepdo, array('encode' => false)) . '\'';
        }
        // Gestionnaire de la PDO
        if (!empty($gestionnaire)) {
            $conditions[] = 'Dossierpcg66.user_id IN ( \'' . implode('\', \'', $gestionnaire) . '\' )';
        }

        // Pôle chargé de la PDO
        if (!empty($poledossierpcg66_id)) {
            $conditions[] = 'Dossierpcg66.poledossierpcg66_id IN ( \'' . implode('\', \'', $poledossierpcg66_id) . '\' )';
        }

        // Type de la PDO
        if (!empty($typepdo_id)) {
            $conditions[] = 'Dossierpcg66.typepdo_id = \'' . Sanitize::clean($typepdo_id, array('encode' => false)) . '\'';
        }

        // Organisme payeur
        if (!empty($orgpayeur)) {
            $conditions[] = 'Dossierpcg66.orgpayeur = \'' . Sanitize::clean($orgpayeur, array('encode' => false)) . '\'';
        }

        // Agent ayant émis l'avis technique
        if (!empty($gestionnaireAvistechnique)) {
            $conditions[] = 'Decisiondossierpcg66.useravistechnique_id = \'' . Sanitize::clean($gestionnaireAvistechnique, array('encode' => false)) . '\'';
        }
        // Agent ayant émis la validation
        if (!empty($gestionnaireValidation)) {
            $conditions[] = 'Decisiondossierpcg66.userproposition_id = \'' . Sanitize::clean($gestionnaireValidation, array('encode' => false)) . '\'';
        }

        // Corbeille vide ?
        $sqNbFichierDansCorbeille = '( SELECT count( fichiersmodules.id ) FROM fichiersmodules WHERE fichiersmodules.modele = \'Foyer\' AND fichiersmodules.fk_value = "Foyer"."id" )';

        if (isset($params['Dossierpcg66']['exists']) && ( $params['Dossierpcg66']['exists'] != '' )) {
            if ($params['Dossierpcg66']['exists']) {
                $conditions[] = "{$sqNbFichierDansCorbeille} > 0";
            } else {
                $conditions[] = "{$sqNbFichierDansCorbeille} = 0";
            }
        }


        // Motif concernant la personne du dossier
        $motifpersonnepcg66_id = Set::extract($params, 'Traitementpcg66.situationpdo_id');
        if (!empty($motifpersonnepcg66_id)) {
            $conditions[] = 'Personnepcg66.id IN ( ' .
                    ClassRegistry::init('Personnepcg66Situationpdo')->sq(
                            array(
                                'alias' => 'personnespcgs66_situationspdos',
                                'fields' => array('personnespcgs66_situationspdos.personnepcg66_id'),
                                'contain' => false,
                                'conditions' => array(
                                    'personnespcgs66_situationspdos.situationpdo_id' => $motifpersonnepcg66_id
                                ),
                                'joins' => array(
                                    array(
                                        'table' => 'situationspdos',
                                        'alias' => 'situationspdos',
                                        'type' => 'INNER',
                                        'foreignKey' => false,
                                        'conditions' => array('personnespcgs66_situationspdos.situationpdo_id = situationspdos.id'),
                                    )
                                )
                            )
                    )
                    . ' )';
        }

        // Statut concernant la perosnne du dossier
        $statutpersonnepcg66_id = Set::extract($params, 'Traitementpcg66.statutpdo_id');
        if (!empty($statutpersonnepcg66_id)) {
            $conditions[] = 'Personnepcg66.id IN ( ' .
                    ClassRegistry::init('Personnepcg66Statutpdo')->sq(
                            array(
                                'alias' => 'personnespcgs66_statutspdos',
                                'fields' => array('personnespcgs66_statutspdos.personnepcg66_id'),
                                'contain' => false,
                                'conditions' => array(
                                    'personnespcgs66_statutspdos.statutpdo_id' => $statutpersonnepcg66_id
                                ),
                                'joins' => array(
                                    array(
                                        'table' => 'statutspdos',
                                        'alias' => 'statutspdos',
                                        'type' => 'INNER',
                                        'foreignKey' => false,
                                        'conditions' => array('personnespcgs66_statutspdos.statutpdo_id = statutspdos.id'),
                                    )
                                )
                            )
                    )
                    . ' )';
        }

        // Décision sur la PDP
        if (!empty($decisionpdo_id)) {
            $conditions['Decisiondossierpcg66.decisionpdo_id'] = $decisionpdo_id;
		}


        /// Critères sur les dates de décisions PCG - date de validation et date de transmission à l'op
        foreach (array('datetransmissionop', 'datevalidation') as $dateSelected) {
            if (isset($params['Decisiondossierpcg66'][$dateSelected]) && !empty($params['Decisiondossierpcg66'][$dateSelected])) {
                if (!empty($dateSelected)) {
                    $dateSelected_from = "{$params['Decisiondossierpcg66']["{$dateSelected}_from"]['year']}-{$params['Decisiondossierpcg66']["{$dateSelected}_from"]['month']}-{$params['Decisiondossierpcg66']["{$dateSelected}_from"]['day']}";
                    $dateSelected_to = "{$params['Decisiondossierpcg66']["{$dateSelected}_to"]['year']}-{$params['Decisiondossierpcg66']["{$dateSelected}_to"]['month']}-{$params['Decisiondossierpcg66']["{$dateSelected}_to"]['day']}";
                    $conditions[] = "Decisiondossierpcg66.{$dateSelected} BETWEEN '{$dateSelected_from}' AND '{$dateSelected_to}'";
                }
            }
        }

        // Organismes auxquels le dossier a été transmis
        $organismes_id = Set::extract($params, 'Decisiondossierpcg66.org_id');
        if (!empty($organismes_id)) {
            $conditions[] = 'Decisiondossierpcg66.id IN ( ' .
                    ClassRegistry::init('Decdospcg66Orgdospcg66')->sq(
                            array(
                                'alias' => 'decsdospcgs66_orgsdospcgs66',
                                'fields' => array('decsdospcgs66_orgsdospcgs66.decisiondossierpcg66_id'),
                                'contain' => false,
                                'conditions' => array(
                                    'decsdospcgs66_orgsdospcgs66.orgtransmisdossierpcg66_id' => $organismes_id
                                ),
                                'joins' => array(
                                    array(
                                        'table' => 'orgstransmisdossierspcgs66',
                                        'alias' => 'orgstransmisdossierspcgs66',
                                        'type' => 'INNER',
                                        'foreignKey' => false,
                                        'conditions' => array('decsdospcgs66_orgsdospcgs66.orgtransmisdossierpcg66_id = orgstransmisdossierspcgs66.id'),
                                    )
                                )
                            )
                    )
                    . ' )';
        }

        $nbpropo = Hash::get($params, 'Decisiondossierpcg66.nbproposition');
        if (isset($nbpropo) && $nbpropo != '') {
            $conditions[] = array($Dossierpcg66->sqVirtualField('nbpropositions', false) => $nbpropo);
        }
		
		$joinTraitementpcg66 = $Dossierpcg66->Personnepcg66->join('Traitementpcg66');
		$sqJoinTraitement = str_replace('Traitementpcg66', 'traitement', $Dossierpcg66->Personnepcg66->Traitementpcg66->sq(
			array(
				'fields' => 'id',
				'conditions' => array(
					$joinTraitementpcg66['conditions'],
					'Traitementpcg66.typetraitement' => 'documentarrive',
					'Traitementpcg66.datereception IS NOT NULL',
					'Dossierpcg66.etatdossierpcg' => 'attinstrdocarrive',
				),
				'order' => array('Traitementpcg66.created' => 'DESC'),
				'recursive' => -1,
				'limit' => 1
			)
		));
		$joinTraitementpcg66['conditions'] = 'Traitementpcg66.id IN ('.$sqJoinTraitement.')';

		$querydata = array(
            'fields' => array(
                'Dossierpcg66.id',
                'Dossierpcg66.foyer_id',
                'Dossierpcg66.datereceptionpdo',
                'Dossierpcg66.typepdo_id',
                'Dossierpcg66.etatdossierpcg',
                'Dossierpcg66.datetransmission',
                'Dossierpcg66.dateaffectation',
                'Dossierpcg66.poledossierpcg66_id',
                'Decisiondossierpcg66.datetransmissionop',
                'Decisiondossierpcg66.datevalidation',
                'Decisiondossierpcg66.useravistechnique_id',
                'Decisiondossierpcg66.userproposition_id',
                'Decisiondossierpcg66.etatop',
                'Dossierpcg66.originepdo_id',
                'Dossierpcg66.user_id',
                'Dossier.id',
                'Dossier.numdemrsa',
                'Dossier.dtdemrsa',
                'Dossier.matricule',
                'Personne.id',
                'Personne.nom',
                'Personne.prenom',
                'Personne.dtnai',
                'Personne.nir',
                'Personne.qual',
                'Personne.nomcomnai',
                'Adresse.nomcom',
                'Adresse.codepos',
                'Adresse.numcom',
                'Situationdossierrsa.etatdosrsa',
                $Dossierpcg66->sqVirtualField('nbpropositions'),
                ClassRegistry::init('Fichiermodule')->sqNbFichiersLies(ClassRegistry::init('Foyer'), 'nb_fichiers_lies'),
                'Decisionpdo.libelle',
                $Dossierpcg66->User->sqVirtualField('nom_complet'),
                'Poledossierpcg66.name',
				'Traitementpcg66.id',
				'Traitementpcg66.datereception'
            ),
            'recursive' => -1,
            'joins' => array(
                $Dossierpcg66->join('Foyer', array('type' => 'INNER')),
				$Dossierpcg66->Foyer->join('Dossier', array('type' => 'INNER')),
                $Dossierpcg66->Foyer->join(
                    'Personne',
                    array(
                        'type' => 'LEFT OUTER',
                        'conditions' => array(
                            'Personne.id IN (
                                ' . ClassRegistry::init('Personne')->WebrsaPersonne->sqResponsableDossierUnique('Foyer.id') . '
                            )'
                        )
                    )
                ),
				$Dossierpcg66->join('User', array('type' => 'LEFT OUTER')),
                $Dossierpcg66->join('Personnepcg66'),
                $joinTraitementpcg66,
				$Dossierpcg66->Foyer->join('Adressefoyer', 
					array(
						'type' => 'LEFT OUTER',
						'conditions' => 'Adressefoyer.id IN ( ' . $Dossierpcg66->Foyer->Adressefoyer->sqDerniereRgadr01('Foyer.id') . ' )'
					)
				),
                $Dossierpcg66->Foyer->Adressefoyer->join('Adresse', array('type' => 'LEFT OUTER')),
                $Dossierpcg66->Foyer->Dossier->join('Situationdossierrsa', array('type' => 'INNER')),
                $Dossierpcg66->join(
                        'Decisiondossierpcg66', array(
                    'type' => 'LEFT OUTER'
                        )
                ),
                $Dossierpcg66->Decisiondossierpcg66->join('Decisionpdo', 
					array(
						'type' => 'LEFT OUTER',
						'conditions' => "Decisiondossierpcg66.id IN ( {$sqDerniereDecisiondossierpcg66} )"
					)
				),
                $Dossierpcg66->Foyer->Dossier->join('Detaildroitrsa', array('type' => 'LEFT OUTER')),
                $Dossierpcg66->Foyer->Dossier->Detaildroitrsa->join('Detailcalculdroitrsa', 
					array(
						'type' => 'LEFT OUTER',
						'conditions' => "Detailcalculdroitrsa.id IN ( {$sqDernierDetailcalculdroitrsa} )"
					)
				),
                $Dossierpcg66->join('Poledossierpcg66', array('type' => 'LEFT OUTER'))
            ),
            'limit' => 10,
            'conditions' => $conditions
        );

        $querydata = $this->_completeQuery($querydata, $params);

		// Champs et jointures ROME V2
		$querydata['fields'] = Hash::merge( $querydata['fields'], $Dossierpcg66->Personnepcg66->Categoriesecteurromev2->fields() );
		$querydata['joins'][] = $Dossierpcg66->Personnepcg66->join( 'Categoriesecteurromev2', array( 'type' => 'LEFT OUTER' ) );

		$querydata['fields'] = Hash::merge( $querydata['fields'], $Dossierpcg66->Personnepcg66->Categoriemetierromev2->fields() );
		$querydata['joins'][] = $Dossierpcg66->Personnepcg66->join( 'Categoriemetierromev2', array( 'type' => 'LEFT OUTER' ) );

		// Champs et jointures ROME V3
		if( Configure::read( 'Romev3.enabled' ) ) {
			$aliases = array_keys( $Dossierpcg66->Personnepcg66->Categorieromev3->belongsTo );

			$querydata['joins'][] = $Dossierpcg66->Personnepcg66->join( 'Categorieromev3', array( 'type' => 'LEFT OUTER' ) );

			foreach( $aliases as $alias ) {
				$querydata['fields'][] = "{$alias}.name";
				$querydata['joins'][] = $Dossierpcg66->Personnepcg66->Categorieromev3->join( $alias, array( 'type' => 'LEFT OUTER' ) );
			}
		}

		// Conditions ROME V3
		if( Configure::read( 'Romev3.enabled' ) ) {
			$foreignKeys = Hash::extract( $Dossierpcg66->Personnepcg66->Categorieromev3->belongsTo, '{s}.foreignKey' );
			$filters = (array)Hash::get( $params, 'Categorieromev3' );

			foreach( $foreignKeys as $foreignKey ) {
				$value = suffix( Hash::get( $filters, $foreignKey ) );
				if( !empty( $value ) ) {
					$querydata['conditions'][] = array( "Categorieromev3.{$foreignKey}" => $value );
				}
			}
		}

        return $querydata;
    }

    /**
     *
     * @param array $params
     * @param array $mesCodesInsee
     * @param boolean $filtre_zone_geo
     * @return array
     */
    public function searchGestionnaire($params, $mesCodesInsee, $filtre_zone_geo) {
        $conditions = array();
        $Dossierpcg66 = ClassRegistry::init('Dossierpcg66');
        $gestionnaire = Set::extract($params, 'Dossierpcg66.user_id');
        $poledossierpcg66_id = Set::extract($params, 'Dossierpcg66.poledossierpcg66_id');

        $originepdo = Set::extract($params, 'Dossierpcg66.originepdo_id');
        $typepdo_id = Set::extract($params, 'Dossierpcg66.typepdo_id');
        $orgpayeur = Set::extract($params, 'Dossierpcg66.orgpayeur');
        $decisionpdo_id = Set::extract($params, 'Decisiondossierpcg66.decisionpdo_id');


        // Gestionnaire de la PDO
        if (!empty($gestionnaire)) {
            $conditions[] = 'Dossierpcg66.user_id IN ( \'' . implode('\', \'', $gestionnaire) . '\' )';
        }

        // Pôle chargé de la PDO
        if (!empty($poledossierpcg66_id)) {
            $conditions[] = 'Dossierpcg66.poledossierpcg66_id IN ( \'' . implode('\', \'', $poledossierpcg66_id) . '\' )';
        }

        // Filtre sur l'état du dossier PCG
        $etatdossierpcg = Set::extract($params, 'Dossierpcg66.etatdossierpcg');
        if (isset($params['Dossierpcg66']['etatdossierpcg']) && !empty($params['Dossierpcg66']['etatdossierpcg'])) {
            $conditions[] = '( Dossierpcg66.etatdossierpcg IN ( \'' . implode('\', \'', $etatdossierpcg) . '\' ) )';
        }

        $sqDernierDetailcalculdroitrsa = $Dossierpcg66->Foyer->Dossier->Detaildroitrsa->Detailcalculdroitrsa->sqDernier('Detaildroitrsa.id');
        $sqDerniereDecisiondossierpcg66 = $Dossierpcg66->Decisiondossierpcg66->WebrsaDecisiondossierpcg66->sqDernier('Dossierpcg66.id');
        $sqDernierTraitementpcg66 = $Dossierpcg66->Personnepcg66->sqLatest('Traitementpcg66', 'created', array(true));

        $conditions[] = array(
            array(
                array(
                    'OR' => array(
                        'Adressefoyer.id IS NULL',
                        'Adressefoyer.id IN ( ' . $Dossierpcg66->Foyer->Adressefoyer->sqDerniereRgadr01('Foyer.id') . ' )'
                    )
                ),
                array(
                    'OR' => array(
                        'Detailcalculdroitrsa.id IS NULL',
                        "Detailcalculdroitrsa.id IN ( {$sqDernierDetailcalculdroitrsa} )"
                    )
                ),
                array(
                    'OR' => array(
                        'Decisiondossierpcg66.id IS NULL',
                        "Decisiondossierpcg66.id IN ( {$sqDerniereDecisiondossierpcg66} )",
                    )
                ),
                $sqDernierTraitementpcg66
            )
        );
        $conditions[] = 'Personne.id IN ( ' . $Dossierpcg66->Foyer->Personne->WebrsaPersonne->sqResponsableDossierUnique('Foyer.id') . ' )';



        $conditions = $this->conditionsPersonneFoyerDossier($conditions, $params);
        $conditions = $this->conditionsDernierDossierAllocataire($conditions, $params);

        $conditions = $this->conditionsDates($conditions, $params, 'Dossierpcg66.dateaffectation');


        $dossierEchu = Set::extract($params, 'Dossierpcg66.dossierechu');
        if (isset($params['Dossierpcg66']['dossierechu']) && !empty($params['Dossierpcg66']['dossierechu'])) {
            $conditions[] = 'Traitementpcg66.id IN ( ' . $Dossierpcg66->Personnepcg66->Traitementpcg66->WebrsaTraitementpcg66->sqTraitementpcg66Echu('Personnepcg66.id') . ' )';
        }

        // Origine de la PDO
        if (!empty($originepdo)) {
            $conditions[] = 'Dossierpcg66.originepdo_id = \'' . Sanitize::clean($originepdo, array('encode' => false)) . '\'';
        }
        // Type de la PDO
        if (!empty($typepdo_id)) {
            $conditions[] = 'Dossierpcg66.typepdo_id = \'' . Sanitize::clean($typepdo_id, array('encode' => false)) . '\'';
        }

        // Organisme payeur
        if (!empty($orgpayeur)) {
            $conditions[] = 'Dossierpcg66.orgpayeur = \'' . Sanitize::clean($orgpayeur, array('encode' => false)) . '\'';
        }

        // Corbeille vide ?
        $sqNbFichierDansCorbeille = '( SELECT count( fichiersmodules.id ) FROM fichiersmodules WHERE fichiersmodules.modele = \'Foyer\' AND fichiersmodules.fk_value = "Foyer"."id" )';

        if (isset($params['Dossierpcg66']['exists']) && ( $params['Dossierpcg66']['exists'] != '' )) {
            if ($params['Dossierpcg66']['exists']) {
                $conditions[] = "{$sqNbFichierDansCorbeille} = 0";
            } else {
                $conditions[] = "{$sqNbFichierDansCorbeille} > 0";
            }
        }

        // Décision sur la PCG
        if (!empty($decisionpdo_id)) {
            $conditions[] = 'Decisiondossierpcg66.decisionpdo_id = \'' . Sanitize::clean($decisionpdo_id, array('encode' => false)) . '\'';
        }


        // Motif concernant la personne du dossier
        $motifpersonnepcg66_id = Set::extract($params, 'Traitementpcg66.situationpdo_id');
        if (!empty($motifpersonnepcg66_id)) {
            $conditions[] = 'Personnepcg66.id IN ( ' .
                    ClassRegistry::init('Personnepcg66Situationpdo')->sq(
                            array(
                                'alias' => 'personnespcgs66_situationspdos',
                                'fields' => array('personnespcgs66_situationspdos.personnepcg66_id'),
                                'contain' => false,
                                'conditions' => array(
                                    'personnespcgs66_situationspdos.situationpdo_id' => $motifpersonnepcg66_id
                                ),
                                'joins' => array(
                                    array(
                                        'table' => 'situationspdos',
                                        'alias' => 'situationspdos',
                                        'type' => 'INNER',
                                        'foreignKey' => false,
                                        'conditions' => array('personnespcgs66_situationspdos.situationpdo_id = situationspdos.id'),
                                    )
                                )
                            )
                    )
                    . ' )';
        }

        // Statut concernant la perosnne du dossier
        $statutpersonnepcg66_id = Set::extract($params, 'Traitementpcg66.statutpdo_id');
        if (!empty($statutpersonnepcg66_id)) {
            $conditions[] = 'Personnepcg66.id IN ( ' .
                    ClassRegistry::init('Personnepcg66Statutpdo')->sq(
                            array(
                                'alias' => 'personnespcgs66_statutspdos',
                                'fields' => array('personnespcgs66_statutspdos.personnepcg66_id'),
                                'contain' => false,
                                'conditions' => array(
                                    'personnespcgs66_statutspdos.statutpdo_id' => $statutpersonnepcg66_id
                                ),
                                'joins' => array(
                                    array(
                                        'table' => 'statutspdos',
                                        'alias' => 'statutspdos',
                                        'type' => 'INNER',
                                        'foreignKey' => false,
                                        'conditions' => array('personnespcgs66_statutspdos.statutpdo_id = statutspdos.id'),
                                    )
                                )
                            )
                    )
                    . ' )';
        }

        // Organismes auxquels le dossier a été transmis
        $organismes_id = Set::extract($params, 'Decisiondossierpcg66.org_id');
        if (!empty($organismes_id)) {
            $conditions[] = 'Decisiondossierpcg66.id IN ( ' .
                    ClassRegistry::init('Decdospcg66Orgdospcg66')->sq(
                            array(
                                'alias' => 'decsdospcgs66_orgsdospcgs66',
                                'fields' => array('decsdospcgs66_orgsdospcgs66.decisiondossierpcg66_id'),
                                'contain' => false,
                                'conditions' => array(
                                    'decsdospcgs66_orgsdospcgs66.orgtransmisdossierpcg66_id' => $organismes_id
                                ),
                                'joins' => array(
                                    array(
                                        'table' => 'orgstransmisdossierspcgs66',
                                        'alias' => 'orgstransmisdossierspcgs66',
                                        'type' => 'INNER',
                                        'foreignKey' => false,
                                        'conditions' => array('decsdospcgs66_orgsdospcgs66.orgtransmisdossierpcg66_id = orgstransmisdossierspcgs66.id'),
                                    )
                                )
                            )
                    )
                    . ' )';
        }

        $nbpropo = Hash::get($params, 'Decisiondossierpcg66.nbproposition');
        if (isset($nbpropo) && $nbpropo != '' && is_numeric($nbpropo)) {
            $conditions[] = array($Dossierpcg66->sqVirtualField('nbpropositions', false) => $nbpropo);
        }


        $conditions = $this->conditionsDetailcalculdroitrsa($conditions, $params);
        $querydata = array(
            'fields' => array(
                'Dossierpcg66.id',
                'Dossierpcg66.foyer_id',
                'Dossierpcg66.datereceptionpdo',
                'Dossierpcg66.typepdo_id',
                'Dossierpcg66.etatdossierpcg',
                'Dossierpcg66.originepdo_id',
                'Dossierpcg66.user_id',
                'Dossierpcg66.dateaffectation',
                'Dossierpcg66.datetransmission',
                'Dossierpcg66.poledossierpcg66_id',
                'Decisiondossierpcg66.datetransmissionop',
                'Traitementpcg66.dateecheance',
                'Traitementpcg66.dateecheance',
                'Decisionpdo.libelle',
                'Dossier.id',
                'Dossier.numdemrsa',
                'Dossier.dtdemrsa',
                'Dossier.matricule',
                'Personne.id',
                'Personne.nom',
                'Personne.prenom',
                'Personne.dtnai',
                'Personne.nir',
                'Personne.qual',
                'Personne.nomcomnai',
                'Adresse.nomcom',
                'Adresse.codepos',
                'Adresse.numcom',
                'Situationdossierrsa.etatdosrsa',
                $Dossierpcg66->sqVirtualField('nbpropositions'),
                $Dossierpcg66->Personnepcg66->sqVirtualField('nbtraitements'),
                ClassRegistry::init('Fichiermodule')->sqNbFichiersLies(ClassRegistry::init('Foyer'), 'nb_fichiers_lies'),
                $Dossierpcg66->User->sqVirtualField('nom_complet'),
                'Poledossierpcg66.name'
            ),
            'recursive' => -1,
            'joins' => array(
                $Dossierpcg66->join('User', array('type' => 'LEFT OUTER')),
                $Dossierpcg66->join('Foyer', array('type' => 'INNER')),
                $Dossierpcg66->Foyer->join('Personne', array('type' => 'INNER')),
                $Dossierpcg66->join('Personnepcg66', array(
                    'type' => 'LEFT OUTER',
                    'conditions' => array(
                        'Personnepcg66.personne_id = Personne.id'
                    )
                        )
                ),
                $Dossierpcg66->Personnepcg66->join('Traitementpcg66', array('type' => 'LEFT OUTER')),
                $Dossierpcg66->Foyer->join('Adressefoyer', array('type' => 'LEFT OUTER')),
                $Dossierpcg66->Foyer->Adressefoyer->join('Adresse', array('type' => 'LEFT OUTER')),
                $Dossierpcg66->Foyer->join('Dossier', array('type' => 'INNER')),
                $Dossierpcg66->Foyer->Dossier->join('Situationdossierrsa', array('type' => 'INNER')),
                $Dossierpcg66->join('Decisiondossierpcg66', array('type' => 'LEFT OUTER')),
                $Dossierpcg66->Decisiondossierpcg66->join('Decisionpdo', array('type' => 'LEFT OUTER')),
                $Dossierpcg66->Foyer->Dossier->join('Detaildroitrsa', array('type' => 'LEFT OUTER')),
                $Dossierpcg66->Foyer->Dossier->Detaildroitrsa->join('Detailcalculdroitrsa', array('type' => 'LEFT OUTER')),
                $Dossierpcg66->join('Poledossierpcg66', array('type' => 'LEFT OUTER'))
            ),
            'limit' => 10,
            'contain' => false,
            'conditions' => $conditions
        );

        $querydata = $this->_completeQuery($querydata, $params);

        return $querydata;
    }

}

?>