<#include "freemarker_functions.ftl">
<?php
	/**
	 * Code source de la classe ${name}.
	 *
<#if php_version??>
	 * PHP ${php_version}
	 *
</#if>
	 * @package app.Model.Behavior
	 * @license ${license}
	 */

	/**
	 * La classe ${name} ...
	 *
	 * @package app.Model.Behavior
	 */
	class ${name} extends ModelBehavior
	{
		/**
		 * Contains configuration settings for use with individual model objects. This
		 * is used because if multiple models use this Behavior, each will use the same
		 * object instance. Individual model settings should be stored as an
		 * associative array, keyed off of the model name.
		 *
		 * @var array
		 * @see Model::$alias
		 */
		public $settings = array();

		/**
		 * Allows the mapping of preg-compatible regular expressions to public or
		 * private methods in this class, where the array key is a /-delimited regular
		 * expression, and the value is a class method. Similar to the functionality of
		 * the findBy* / findAllBy* magic methods.
		 *
		 * @var array
		 */
		public $mapMethods = array();

		/**
		 * Setup this behavior with the specified configuration settings.
		 *
		 * @param Model $Model Model using this behavior
		 * @param array $config Configuration settings for $model
		 * @return void
		 */
		public function setup( Model $Model, $config = array() ) {
			parent::setup( $Model, $config );
		}

		/**
		 * Clean up any initialization this behavior has done on a model. Called when a behavior is dynamically
		 * detached from a model using Model::detach().
		 *
		 * @param Model $Model Model using this behavior
		 * @return void
		 * @see BehaviorCollection::detach()
		 */
		public function cleanup( Model $Model ) {
			parent::cleanup( $Model );
		}

		/**
		 * beforeFind can be used to cancel find operations, or modify the query that will be executed.
		 * By returning null/false you can abort a find. By returning an array you can modify/replace the query
		 * that is going to be run.
		 *
		 * @param Model $Model Model using this behavior
		 * @param array $query Data used to execute this query, i.e. conditions, order, etc.
		 * @return boolean|array False or null will abort the operation. You can return an array to replace the
		 *   $query that will be eventually run.
		 */
		public function beforeFind( Model $Model, $query ) {
			return parent::beforeFind( $Model, $query );
		}

		/**
		 * After find callback. Can be used to modify any results returned by find.
		 *
		 * @param Model $Model Model using this behavior
		 * @param mixed $results The results of the find operation
		 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
		 * @return mixed An array value will replace the value of $results - any other value will be ignored.
		 */
		public function afterFind( Model $Model, $results, $primary = false ) {
			return parent::afterFind( $Model, $results, $primary );
		}

		/**
		 * beforeValidate is called before a model is validated, you can use this callback to
		 * add behavior validation rules into a models validate array. Returning false
		 * will allow you to make the validation fail.
		 *
		 * @param Model $Model Model using this behavior
		 * @param array $options Options passed from Model::save().
		 * @return mixed False or null will abort the operation. Any other result will continue.
		 * @see Model::save()
		 */
		public function beforeValidate( Model $Model, $options = array() ) {
			return parent::beforeValidate( $Model, $options );
		}

		/**
		 * afterValidate is called just after model data was validated, you can use this callback
		 * to perform any data cleanup or preparation if needed
		 *
		 * @param Model $Model Model using this behavior
		 * @return mixed False will stop this event from being passed to other behaviors
		 */
		public function afterValidate( Model $Model ) {
			return parent::afterValidate( $Model );
		}

		/**
		 * beforeSave is called before a model is saved. Returning false from a beforeSave callback
		 * will abort the save operation.
		 *
		 * @param Model $Model Model using this behavior
		 * @param array $options Options passed from Model::save().
		 * @return mixed False if the operation should abort. Any other result will continue.
		 * @see Model::save()
		 */
		public function beforeSave( Model $Model, $options = array( ) ) {
			return parent::beforeSave( $Model, $options );
		}

		/**
		 * afterSave is called after a model is saved.
		 *
		 * @param Model $Model Model using this behavior
		 * @param boolean $created True if this save created a new record
		 * @param array $options Options passed from Model::save().
		 * @return boolean
		 * @see Model::save()
		 */
		public function afterSave( Model $Model, $created, $options = array() ) {
			return parent::afterSave( $Model, $created, $options );
		}

		/**
		 * Before delete is called before any delete occurs on the attached model, but after the model's
		 * beforeDelete is called. Returning false from a beforeDelete will abort the delete.
		 *
		 * @param Model $Model Model using this behavior
		 * @param boolean $cascade If true records that depend on this record will also be deleted
		 * @return mixed False if the operation should abort. Any other result will continue.
		 */
		public function beforeDelete( Model $Model, $cascade = true ) {
			return parent::beforeDelete( $Model, $cascade );
		}

		/**
		 * After delete is called after any delete occurs on the attached model.
		 *
		 * @param Model $Model Model using this behavior
		 * @return void
		 */
		public function afterDelete( Model $Model ) {
			parent::afterDelete( $Model );
		}

		/**
		 * DataSource error callback
		 *
		 * @param Model $Model Model using this behavior
		 * @param string $error Error generated in DataSource
		 * @return void
		 */
		public function onError( Model $Model, $error ) {
			parent::onError( $Model, $error );
		}
	}
?>