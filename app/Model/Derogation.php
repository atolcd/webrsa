<?php	
	/**
	 * Code source de la classe Derogation.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Derogation ...
	 *
	 * @package app.Model
	 */
	class Derogation extends AppModel
	{
		public $name = 'Derogation';

		public $validate = array(
			'avispcgpersonne_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
		);
		
		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 * 
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array(
			'typedero' => array('AGE', 'ACT', 'RES', 'NAT'),
			'avisdero' => array('D', 'O', 'N', 'A'),
		);

		public $belongsTo = array(
			'Avispcgpersonne' => array(
				'className' => 'Avispcgpersonne',
				'foreignKey' => 'avispcgpersonne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		*
		*/

		public function dossierId( $derogation_id ) {
			$query = array(
				'fields' => array(
					'"Foyer"."dossier_id"'
				),
				'recursive' => -1,
				'joins' => array(
					array(
						'table'      => 'avispcgpersonnes',
						'alias'      => 'Avispcgpersonne',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'Derogation.avispcgpersonne_id = Avispcgpersonne.id',
							'Derogation.id' => $derogation_id
						)
					),
					array(
						'table'      => 'personnes',
						'alias'      => 'Personne',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Avispcgpersonne.personne_id = Personne.id' )
					),
					array(
						'table'      => 'foyers',
						'alias'      => 'Foyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Personne.foyer_id = Foyer.id' )
					)
				)
			);

			$result = $this->find( 'first', $query );

			if( !empty( $result ) ) {
				return $result['Foyer']['dossier_id'];
			}
			else {
				return null;
			}
		}
	}
?>