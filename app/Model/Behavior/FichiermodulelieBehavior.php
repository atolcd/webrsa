<?php
	/**
	 * Code source de la classe FichiermodulelieBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * La classe FichiermodulelieBehavior permet de supprimer automatiquement les
	 * fichiers liés à l'enregistrement métier lié.
	 *
	 * @package app.Model.Behavior
	 */
	class FichiermodulelieBehavior extends ModelBehavior
	{
		/**
		 * After delete is called after any delete occurs on the attached model.
		 *
		 * @param Model $Model Model using this behavior
		 * @return void
		 */
		public function afterDelete( Model $Model ) {
			parent::afterDelete( $Model );
			$Fichiermodule = ClassRegistry::init( 'Fichiermodule' );

			$Fichiermodule->unbindModelAll(false);
			$result = $Fichiermodule->deleteAll(
				array(
					'modele' => $Model->alias,
					'fk_value' => $Model->id
				)
			);
			$Fichiermodule->resetAssociations();
		}
	}
?>