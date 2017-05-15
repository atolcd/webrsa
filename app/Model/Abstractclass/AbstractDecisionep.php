<?php
	/**
	 * Code source de la classe AbstractDecisionep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Abstractclass
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe AbstractDecisionep ...
	 *
	 * @package app.Model
	 */
	abstract class AbstractDecisionep extends AppModel
	{
		/**
		 * Retourne une sous-requête permettant d'obtenir l'id de la décision
		 * finale d'un passage en commission.
		 *
		 * @param string $passageCommissionepId
		 * @return string
		 */
		public function sqDerniere( $passageCommissionepId = 'Passagecommissionep.id' ) {
			$alias = Inflector::underscore( $this->alias );

			$querydata = array(
				'alias' => $alias,
				'fields' => array( "{$alias}.id" ),
				'contain' => false,
				'conditions' => array(
					"{$alias}.passagecommissionep_id = {$passageCommissionepId}"
				),
				'order' => array(
					"( CASE WHEN {$alias}.etape = 'cg' THEN 2 ELSE 1 END ) DESC"
				),
				'limit' => 1
			);

			$sq = $this->sq( $querydata );

			return $sq;
		}

		/**
		 * Retourne la condition permettant d'obtenir la décision finale d'un
		 * passage en commission.
		 *
		 * @param string $passageCommissionepId
		 * @param boolean $allowNull
		 * @return array|string
		 */
		public function conditionsDerniere( $passageCommissionepId = 'Passagecommissionep.id', $allowNull = true ) {
			$sq = $this->sqDerniere( $passageCommissionepId );

			$condition = "{$this->alias}.id IN ( {$sq} )";

			if( $allowNull ) {
				return array(
					'OR' => array(
						"{$this->alias}.id IS NULL",
						$condition
					)
				);
			}

			return $condition;
		}
	}
?>