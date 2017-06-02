<?php
	/**
	 * Fichier source de la classe Partenairecui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Partenairecui66 est la classe contenant les partenaires (entreprises/mairie...) du CUI.
	 *
	 * @package app.Model
	 */
	class Partenairecui66 extends AppModel
	{
		public $name = 'Partenairecui66';

        public $belongsTo = array(
			'Partenairecui' => array(
				'className' => 'Partenairecui',
				'foreignKey' => 'partenairecui_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'dependent' => true
			),
        );

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate',
		);

		/**
		 * Fait le lien entre la table Partenaire et Les nouvelles tables pour le CUI
		 *
		 * @var array
		 */
		public $correspondancesChamps = array(
			'id' => 'Cui.partenaire_id',
			'canton' => 'Adressecui.canton',
			'clerib' => 'Partenairecui66.clerib',
			'codeban' => 'Partenairecui66.codebanque',
			'codepartenaire' => 'Partenairecui66.codepartenaire',
			'codepostal' => 'Adressecui.codepostal',
			'compladr' => 'Adressecui.complement',
			'email' => 'Adressecui.email',
			'guiban' => 'Partenairecui66.codeguichet',
			'nometaban' => 'Partenairecui66.etablissementbancaire',
			'nomtiturib' => 'Partenairecui66.nomtitulairerib',
			'nomvoie' => 'Adressecui.nomvoie',
			'numcompt' => 'Partenairecui66.numerocompte',
			'numfax' => 'Adressecui.numfax',
			'numtel' => 'Adressecui.numtel',
			'numvoie' => 'Adressecui.numvoie',
			'orgrecouvcotis' => 'Partenairecui.organismerecouvrement',
			'siret' => 'Partenairecui.siret',
			'statut' => 'Partenairecui.statut',
			'typevoie' => 'Adressecui.typevoie',
			'ville' => 'Adressecui.commune',
			'libstruc' => 'Partenairecui.raisonsociale',
			'nbcontratsaidescg' => 'Partenairecui66.nbcontratsaidescg',
			'nomresponsable' => 'Partenairecui66.responsable',
		);

		/**
		 * Permet d'ajouter les champs de la table Partenaire en fonction des champs de Partenairecui, Partenairecui66 et Adressecui
		 *
		 * @param array $data
		 * @return array
		 */
		public function addPartenaireData( $data ){
			$champsNonCouvert = array(
				'iscui' => '1',
				'secteuractivitepartenaire_id' => null,
				'raisonsocialepartenairecui66_id' => null,
				'president' => null,
				'adressepresident' => null,
				'directeur' => null,
				'adressedirecteur' => null,
			);

			// Conversion de champs
			foreach( $this->correspondancesChamps as $fieldName => $path ){
				$extract = Set::classicExtract( $data, $path );
				if ( $extract !== '' && $extract !== null ){
					$data['Partenaire'][$fieldName] = $extract;
				}
			}

			// Ajout de champs
			foreach( $champsNonCouvert as $key => $value ){
				if ( $value !== null ){
					$data['Partenaire'][$key] = $value;
				}
			}

			return $data;
		}

		/**
		 * Requète permettant de connaitre le nombre de Cuis actif d'un partenaire en fonction
		 * de l'id du partenaire.
		 * lié à la table partenaires et stocké dans la table cuis (CG 66)
		 *
		 * @param integer $partenaire_id
		 * @return array
		 */
		public function sqNbCuisActif( $partenaire_id ){
			$query = array(
				'fields' => array(
					'COUNT(*) AS "Cui__nbcuisactif"'
				),
				'joins' => array(
					$this->join( 'Partenairecui'),
					$this->Partenairecui->join( 'Cui' ),
					$this->Partenairecui->Cui->join( 'Cui66' )
				),
				'conditions' => array(
					'Cui66.etatdossiercui66' => array(
						'encours',
						'contratsuspendu'
					),
					'Cui.partenaire_id' => $partenaire_id
				)
			);

			return $this->sq( $query );
		}
	}
?>