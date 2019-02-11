<?php
	/**
	 * Code source de la classe AideapreBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * La classe AideapreBehavior ...
	 *
	 * @package app.Model.Behavior
	 */
    class AideapreBehavior extends ModelBehavior
    {
		/**
		 *
		 * @param Model $model
		 * @return mixed
		 */
        public function beforeSave( Model $model, $options = array() ) {
            $return = parent::beforeSave( $model, $options );

            $suivi = ClassRegistry::init( 'Suiviaideapretypeaide' );

			$qd_personne = array(
				'conditions' => array(
					'Suiviaideapretypeaide.typeaide' => $model->name
				)
			);
			$personne = $suivi->find('first', $qd_personne);


            if( !empty( $personne ) ) {
                foreach( array( 'qual', 'nom', 'prenom', 'numtel' ) as $field ) {
                    $model->data[$model->name]["{$field}suivi"] = Set::classicExtract( $personne, "Suiviaideapre.{$field}" );
                }
            }

			return $return;
        }
    }
?>