<?php
	/**
	 * Code source de la classe Group.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Group ...
	 *
	 * @package app.Model
	 */
	class Group extends AppModel
	{
		public $name = 'Group';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $order = array( 'Group.name ASC' );

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
			'Acl' => array('type' => 'requester')
		);

		public $belongsTo = array(
			'ParentGroup' => array(
				'className' => 'Group',
				'foreignKey' => 'parent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'ChildGroup' => array(
				'className' => 'Group',
				'foreignKey' => 'parent_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'group_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		/**
		 * Permet d'obtenir le noeud parent pour la mise à jour automatique des aros
		 *
		 * @return array
		 */
		public function parentNode() {
			if (!$this->id && empty($this->data)) {
				return null;
			}
			if (isset($this->data['Group']['parent_id'])) {
				$groupId = $this->data['Group']['parent_id'];
			} else {
				$groupId = $this->field('parent_id');
			}
			if (!$groupId) {
				return null;
			}
			return array('Group' => array('id' => $groupId));
		}

		/**
		 * Ajoute un alias à l'Aro correspondant dans le cadre d'un ajout
		 *
		 * @param boolean $created
		 */
		public function afterSave( $created, $options = array() ) {
			parent::afterSave( $created, $options );

			if ($created) {
				$aro = $this->Aro->find('first',
					array(
						'conditions' => array('model' => $this->alias, 'foreign_key' => $this->id),
						'recursive' => -1
					)
				);
				$this->Aro->id = Hash::get($aro, $this->Aro->alias.'.id');
				$aro[$this->Aro->alias]['alias'] = Hash::get($this->data, $this->alias.'.name');
				$this->Aro->create(false);
				$this->Aro->save( $aro, array( 'atomic' => false ) );
			}
		}

		/**
		 * Retourne la liste des id des groupes enfants.
		 *
		 * @param integer $id
		 * @return array
		 */
		public function getChildren( $id ) {
			$result = array();
			$sql = 'WITH RECURSIVE nodes_cte(id, name, parent_id, depth, path) AS (
						SELECT tn.id, tn.name, tn.parent_id, 1::INT AS depth, tn.id::TEXT AS path
							FROM groups AS tn
							WHERE tn.parent_id IS NULL
						UNION ALL
						SELECT c.id, c.name, c.parent_id, p.depth + 1 AS depth,
							(p.path || \'->\' || c.id::TEXT)
						FROM nodes_cte AS p, groups AS c
						WHERE c.parent_id = p.id
					)
					SELECT * FROM nodes_cte AS n ORDER BY n.id ASC;';
			$Dbo = $this->getDataSource();
			$tmps = $Dbo->query( $sql );

			if( false !== $tmps ) {
				foreach( $tmps as $tmp ) {
					$matches = array();
					if(preg_match('/(?<![0-9])'.$id.'(?![0-9])(.*)$/', $tmp[0]['path'], $matches)) {
						$result = array_merge( $result, array_filter( explode( '->', $matches[1] ) ) );
					}
				}
			}

			return array_unique( $result );
		}
	}
?>
