<?php
	/**
	 * Code source de la classe Comiteapre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Comiteapre ...
	 *
	 * @package app.Model
	 */
	class Comiteapre extends AppModel
	{
		public $name = 'Comiteapre';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'intitulecomite';

		public $order = array( 'datecomite ASC' ); // <-- TODO

		public $hasAndBelongsToMany = array(
			'Apre' => array(
				'className' => 'Apre',
				'joinTable' => 'apres_comitesapres',
				'foreignKey' => 'comiteapre_id',
				'associationForeignKey' => 'apre_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'ApreComiteapre'
			),
			'Participantcomite' => array(
				'className' => 'Participantcomite',
				'joinTable' => 'comitesapres_participantscomites',
				'foreignKey' => 'comiteapre_id',
				'associationForeignKey' => 'participantcomite_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'ComiteapreParticipantcomite'
			)
		);

		/**
		 * Behaviors utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'datecomite' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				),
				'isUnique' => array(
					'rule' => array( 'isUnique' ),
					'message' => 'Un comité d\'examen existe déjà à cette date.'
				)
			),
			'heurecomite' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'lieucomite' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			)
		);

		/**
		*
		*/

		public function search( $display, $criterescomite ) {
			/// Conditions de base
			$conditions = array(
			);

			/// Critères sur le Comité - date du comité
			if( isset( $criterescomite['Comiteapre']['datecomite'] ) && !empty( $criterescomite['Comiteapre']['datecomite'] ) ) {
				$valid_from = ( valid_int( $criterescomite['Comiteapre']['datecomite_from']['year'] ) && valid_int( $criterescomite['Comiteapre']['datecomite_from']['month'] ) && valid_int( $criterescomite['Comiteapre']['datecomite_from']['day'] ) );
				$valid_to = ( valid_int( $criterescomite['Comiteapre']['datecomite_to']['year'] ) && valid_int( $criterescomite['Comiteapre']['datecomite_to']['month'] ) && valid_int( $criterescomite['Comiteapre']['datecomite_to']['day'] ) );
				if( $valid_from && $valid_to ) {
					$conditions[] = 'Comiteapre.datecomite BETWEEN \''.implode( '-', array( $criterescomite['Comiteapre']['datecomite_from']['year'], $criterescomite['Comiteapre']['datecomite_from']['month'], $criterescomite['Comiteapre']['datecomite_from']['day'] ) ).'\' AND \''.implode( '-', array( $criterescomite['Comiteapre']['datecomite_to']['year'], $criterescomite['Comiteapre']['datecomite_to']['month'], $criterescomite['Comiteapre']['datecomite_to']['day'] ) ).'\'';
				}
			}

			/// Critères sur le Comité - heure du comité
			if( isset( $criterescomite['Comiteapre']['heurecomite'] ) && !empty( $criterescomite['Comiteapre']['heurecomite'] ) ) {
				$valid_from = ( valid_int( $criterescomite['Comiteapre']['heurecomite_from']['hour'] ) && valid_int( $criterescomite['Comiteapre']['heurecomite_from']['min'] ) );
				$valid_to = ( valid_int( $criterescomite['Comiteapre']['heurecomite_to']['hour'] ) && valid_int( $criterescomite['Comiteapre']['heurecomite_to']['min'] ) );
				if( $valid_from && $valid_to ) {
					$conditions[] = 'Comiteapre.heurecomite BETWEEN \''.implode( ':', array( $criterescomite['Comiteapre']['heurecomite_from']['hour'], $criterescomite['Comiteapre']['heurecomite_from']['min'] ) ).'\' AND \''.implode( ':', array( $criterescomite['Comiteapre']['heurecomite_to']['hour'], $criterescomite['Comiteapre']['heurecomite_to']['min'] ) ).'\'';
				}
			}

			/// Requête
			$this->Dossier = ClassRegistry::init( 'Dossier' );

			$query = array(
				'fields' => array(
					'"Comiteapre"."id"',
					'"Comiteapre"."datecomite"',
					'"Comiteapre"."heurecomite"',
					'"Comiteapre"."lieucomite"',
					'"Comiteapre"."intitulecomite"',
					'"Comiteapre"."observationcomite"',
				),
				'recursive' => -1,
				'order' => array( '"Comiteapre"."datecomite" DESC' ),
				'conditions' => $conditions
			);

			return $query;
		}
	}
?>