<?php
	/**
	 * Code source de la classe Personnepcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Personnepcg66 ...
	 *
	 * @package app.Model
	 */
	class Personnepcg66 extends AppModel
	{
		public $name = 'Personnepcg66';

		public $recursive = -1;

		public $actsAs = array(
			'Allocatairelie',
			'Autovalidate2',
			'ValidateTranslate',
			'Formattable' => array( 'suffix' => array( 'categoriedetail' ) )
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
		*	Liste des traitements non clos liés à n'importe quel dossier du Foyer
		*	@params	integer (defaut Foyer.id)
		*	@return array
		*
		*/
		public function listeTraitementpcg66NonClos( $personneId = 'Personne.id', $action, $data = array(), $traitementspcgsouverts = array() ) {
			$traitementsNonClos = array();

			$personnespcgs66 = $this->find(
				'all',
				array(
					'fields' => array(
						'Personnepcg66.id',
						'Personnepcg66.dossierpcg66_id'
					),
					'conditions' => array(
						'Personnepcg66.personne_id' => $personneId
					),
					'contain' => false
				)
			);
			$listDossierspcgs66 = (array)Set::extract( $personnespcgs66, '{n}.Personnepcg66.dossierpcg66_id' );
			$listPersonnespcgs66 = (array)Set::extract( $personnespcgs66, '{n}.Personnepcg66.id' );

			$traitementspcgs66 = array();
			if( !empty( $listDossierspcgs66 ) ) {
				if( $action == 'edit' && isset( $data['Traitementpcg66']['id'] ) && !empty( $data['Traitementpcg66']['id'] ) ) {
					$conditions = array(
						'Traitementpcg66.personnepcg66_id' => $listPersonnespcgs66,
						'Traitementpcg66.clos' => 'N',
						'Traitementpcg66.id NOT' => $data['Traitementpcg66']['id']
					);
				}
				else {
					$conditions = array(
						'Traitementpcg66.personnepcg66_id' => $listPersonnespcgs66,
						'Traitementpcg66.clos' => 'N'
					);
				}

                // On enlève les IDs des traitements ouverts déjà pris en compte
                if( !empty( $traitementspcgsouverts ) ) {
                    $traitementspcgsouverts = Hash::extract( $traitementspcgsouverts, '{n}.Traitementpcg66.id' );
                    $conditions[] = array( 'Traitementpcg66.id NOT' => $traitementspcgsouverts );
                }


				$traitementspcgs66 = $this->Traitementpcg66->find(
					'all',
					array(
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
						'conditions' => $conditions,
						'joins' => array(
							$this->Traitementpcg66->join( 'Personnepcg66', array( 'type' => 'INNER' ) ),
							$this->Traitementpcg66->Personnepcg66->join( 'Dossierpcg66', array( 'type' => 'INNER' ) ),
							$this->Traitementpcg66->Personnepcg66->Dossierpcg66->join( 'Typepdo', array( 'type' => 'INNER' ) ),
							$this->Traitementpcg66->Personnepcg66->Dossierpcg66->join( 'User', array( 'type' => 'INNER' ) ),
							$this->join( 'Personne', array( 'type' => 'INNER' ) ),
							$this->Traitementpcg66->join( 'Descriptionpdo', array( 'type' => 'INNER' ) ),
							$this->Traitementpcg66->join( 'Situationpdo', array( 'type' => 'LEFT OUTER' ) )
						),
						'contain' => false
					)
				);

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